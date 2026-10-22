<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Tampilkan halaman Analitik untuk pemilik tag (User Analytics).
     * Semua query ketat difilter berdasarkan kepemilikan tag user yang sedang login (tags.user_id = auth()->id()).
     */
    public function index(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags']);

        // 1. Validasi Parameter Periode (Whitelist: 7d, 30d, month; fallback: 7d)
        $periodInput = (string) $request->query('period', '7d');
        $validPeriods = ['7d', '30d', 'month'];
        $period = in_array($periodInput, $validPeriods, true) ? $periodInput : '7d';

        // 2. Ambil seluruh Tag ID yang dimiliki user login
        $userTags = $user->tags;
        $userTagIds = $userTags->pluck('id')->filter()->values();

        // 3. Tentukan Rentang Tanggal berdasarkan Periode
        [$startDate, $endDate, $prevStartDate, $prevEndDate, $periodLabel] = $this->resolvePeriodDates($period);

        // 4. Hitung Total Tap pada Periode Terpilih & Periode Sebelumnya (Data Nyata)
        $totalTaps = 0;
        $previousPeriodTaps = 0;

        if ($userTagIds->isNotEmpty()) {
            $totalTaps = Tap::whereIn('tag_id', $userTagIds)
                ->whereBetween('tapped_at', [$startDate, $endDate])
                ->count();

            $previousPeriodTaps = Tap::whereIn('tag_id', $userTagIds)
                ->whereBetween('tapped_at', [$prevStartDate, $prevEndDate])
                ->count();
        }

        // Perbandingan Persentase Periode
        $periodComparisonText = 'Belum ada pembanding';
        $periodComparisonStatus = 'neutral'; // 'positive', 'negative', 'neutral'

        if ($previousPeriodTaps > 0) {
            $diff = (($totalTaps - $previousPeriodTaps) / $previousPeriodTaps) * 100;
            $sign = $diff >= 0 ? '+' : '';
            $periodComparisonText = $sign.round($diff, 1).'% vs periode lalu';
            $periodComparisonStatus = $diff >= 0 ? 'positive' : 'negative';
        } elseif ($totalTaps > 0) {
            $periodComparisonText = '+100% pertama kali';
            $periodComparisonStatus = 'positive';
        }

        // 5. Hitung Tap Minggu Ini (Senin s/d Minggu saat ini)
        $weeklyTaps = 0;
        $prevWeeklyTaps = 0;

        if ($userTagIds->isNotEmpty()) {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            $weeklyTaps = Tap::whereIn('tag_id', $userTagIds)
                ->whereBetween('tapped_at', [$startOfWeek, $endOfWeek])
                ->count();

            $prevStartOfWeek = now()->subWeek()->startOfWeek();
            $prevEndOfWeek = now()->subWeek()->endOfWeek();

            $prevWeeklyTaps = Tap::whereIn('tag_id', $userTagIds)
                ->whereBetween('tapped_at', [$prevStartOfWeek, $prevEndOfWeek])
                ->count();
        }

        $weeklyComparisonText = 'Belum ada pembanding';
        if ($prevWeeklyTaps > 0) {
            $diffWeekly = (($weeklyTaps - $prevWeeklyTaps) / $prevWeeklyTaps) * 100;
            $signWeekly = $diffWeekly >= 0 ? '+' : '';
            $weeklyComparisonText = $signWeekly.round($diffWeekly, 1).'% vs mgg lalu';
        } elseif ($weeklyTaps > 0) {
            $weeklyComparisonText = 'Mulai ada tap';
        }

        // 6. Aktivitas Tap Harian (7 Hari Terakhir: Sen s/d Min kalender berjalan / 7 hari terakhir)
        // Hari tanpa tap tetap muncul dengan nilai 0 (tepat 7 elemen)
        $dailyActivity = [];
        $dayLabelsMap = [
            'Monday' => 'Sen',
            'Tuesday' => 'Sel',
            'Wednesday' => 'Rab',
            'Thursday' => 'Kam',
            'Friday' => 'Jum',
            'Saturday' => 'Sab',
            'Sunday' => 'Min',
        ];

        // Buat 7 hari terakhir mundur dari hari ini
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->toDateString();
            $dayEnglish = $date->format('l');
            $shortDay = $dayLabelsMap[$dayEnglish] ?? substr($dayEnglish, 0, 3);

            $last7Days[$dateString] = [
                'date' => $dateString,
                'day_label' => $shortDay,
                'full_date' => $date->translatedFormat('d M Y'),
                'count' => 0,
                'height_percent' => 0,
                'is_highest' => false,
            ];
        }

        if ($userTagIds->isNotEmpty()) {
            $sevenDaysAgo = now()->subDays(6)->startOfDay();
            $dailyCounts = Tap::whereIn('tag_id', $userTagIds)
                ->where('tapped_at', '>=', $sevenDaysAgo)
                ->select(DB::raw('DATE(tapped_at) as tap_date'), DB::raw('count(*) as aggregate_count'))
                ->groupBy('tap_date')
                ->pluck('aggregate_count', 'tap_date');

            foreach ($dailyCounts as $dateKey => $count) {
                if (isset($last7Days[$dateKey])) {
                    $last7Days[$dateKey]['count'] = (int) $count;
                }
            }
        }

        // Tentukan Hari Tertinggi
        $maxDailyTap = 0;
        $highestDateKey = null;
        foreach ($last7Days as $key => $item) {
            if ($item['count'] > $maxDailyTap) {
                $maxDailyTap = $item['count'];
                $highestDateKey = $key;
            }
        }

        // Jika semua nol, tidak ada bar yang diberi bubble tertinggi
        if ($maxDailyTap > 0 && $highestDateKey !== null) {
            $last7Days[$highestDateKey]['is_highest'] = true;
        }

        // Skala tinggi bar (minimum 6% agar terlihat sedikit di UI bila bernilai 0, atau skala proporsional)
        foreach ($last7Days as &$item) {
            if ($maxDailyTap > 0 && $item['count'] > 0) {
                $item['height_percent'] = max(14, (int) round(($item['count'] / $maxDailyTap) * 92));
            } else {
                $item['height_percent'] = 6; // minimum visual bar
            }
        }
        unset($item);

        $dailyActivity = array_values($last7Days);

        // Insight Teks Hari Tertinggi
        if ($maxDailyTap > 0 && $highestDateKey !== null) {
            $highestItem = $last7Days[$highestDateKey];
            $highestInsightText = "Interaksi tertinggi tercatat pada hari {$highestItem['day_label']} ({$highestItem['full_date']}) sebanyak {$highestItem['count']} tap.";
        } else {
            $highestInsightText = 'Belum ada aktivitas tap pada periode ini.';
        }

        // 7. Hardware Teraktif (Hanya dari Tag Milik User yang Ada di DB)
        $activeHardware = null;
        if ($userTagIds->isNotEmpty()) {
            $tagCounts = Tap::whereIn('tag_id', $userTagIds)
                ->whereBetween('tapped_at', [$startDate, $endDate])
                ->select('tag_id', DB::raw('count(*) as count'))
                ->groupBy('tag_id')
                ->orderByDesc('count')
                ->first();

            $targetTag = null;
            $targetTapCount = 0;

            if ($tagCounts) {
                $targetTag = $userTags->firstWhere('id', $tagCounts->tag_id);
                $targetTapCount = (int) $tagCounts->count;
            } else {
                // Jika belum ada tap sama sekali, ambil tag pertama user jika ada
                $targetTag = $userTags->where('status', 'aktif')->first() ?? $userTags->first();
                $targetTapCount = 0;
            }

            if ($targetTag) {
                $dominancePercent = $totalTaps > 0 ? (int) round(($targetTapCount / $totalTaps) * 100) : 0;

                // Ambil lokasi terbaru jika ada dari record tap tag ini
                $latestLocation = Tap::where('tag_id', $targetTag->id)
                    ->whereNotNull('region')
                    ->where('region', '!=', '')
                    ->latest('tapped_at')
                    ->value('region');

                $activeHardware = [
                    'tag_code' => $targetTag->tag_code,
                    'status' => $targetTag->status,
                    'taps_count' => $targetTapCount,
                    'dominance_percent' => $dominancePercent,
                    'location' => $latestLocation ?: null,
                    'batch_id' => $targetTag->batch_id,
                ];
            }
        }

        // 8. Mode Profil Aktif Saat Ini (Truthful State: Sesuai schema yang ada tanpa mengarang histori per tap)
        $currentActiveMode = $user->profile?->active_mode ?? 'kampus';
        $modeLabels = [
            'kampus' => 'Mode Kampus',
            'kerja' => 'Mode Kerja',
            'umkm' => 'Mode UMKM',
            'hilang' => 'Mode Barang Hilang',
        ];
        $currentActiveModeLabel = $modeLabels[$currentActiveMode] ?? ucfirst($currentActiveMode);

        return view('user.analytics', [
            'user' => $user,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'totalTaps' => $totalTaps,
            'periodComparisonText' => $periodComparisonText,
            'periodComparisonStatus' => $periodComparisonStatus,
            'weeklyTaps' => $weeklyTaps,
            'weeklyComparisonText' => $weeklyComparisonText,
            'dailyActivity' => $dailyActivity,
            'highestInsightText' => $highestInsightText,
            'maxDailyTap' => $maxDailyTap,
            'activeHardware' => $activeHardware,
            'currentActiveMode' => $currentActiveMode,
            'currentActiveModeLabel' => $currentActiveModeLabel,
        ]);
    }

    /**
     * Hitung rentang waktu saat ini dan periode pembanding sebelumnya.
     *
     * @return array{0: Carbon, 1: Carbon, 2: Carbon, 3: Carbon, 4: string}
     */
    private function resolvePeriodDates(string $period): array
    {
        return match ($period) {
            '30d' => [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay(),
                now()->subDays(60)->startOfDay(),
                now()->subDays(30)->endOfDay(),
                '30 Hari',
            ],
            'month' => [
                now()->startOfMonth(),
                now()->endOfDay(),
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
                'Bulan Ini',
            ],
            default => [ // '7d'
                now()->subDays(6)->startOfDay(),
                now()->endOfDay(),
                now()->subDays(13)->startOfDay(),
                now()->subDays(7)->endOfDay(),
                '7 Hari Terakhir',
            ],
        };
    }
}
