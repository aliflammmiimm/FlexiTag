<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Profile;
use App\Models\Tap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    /**
     * Display the NFC Analytics & Tap interactions dashboard (FR-ADM-06).
     */
    public function index(Request $request): View
    {
        $range = $request->query('range', '30_days');

        [$startDate, $prevStartDate, $prevEndDate, $daysCount, $rangeLabel] = $this->resolveDateRange($range);

        // 1. Metric: Total Tap & Growth Rate (Real database queries)
        $currentTapsQuery = Tap::query();
        if ($startDate) {
            $currentTapsQuery->where('tapped_at', '>=', $startDate);
        }
        $currentTapsCount = $currentTapsQuery->count();

        $previousTapsCount = 0;
        if ($prevStartDate && $prevEndDate) {
            $previousTapsCount = Tap::whereBetween('tapped_at', [$prevStartDate, $prevEndDate])->count();
        }

        if ($previousTapsCount > 0) {
            $diff = (($currentTapsCount - $previousTapsCount) / $previousTapsCount) * 100;
            $growthRate = ($diff >= 0 ? '+' : '').round($diff, 1).'%';
            $isGrowthPositive = $diff >= 0;
        } elseif ($currentTapsCount > 0) {
            $growthRate = '+100%';
            $isGrowthPositive = true;
        } else {
            $growthRate = '0%';
            $isGrowthPositive = true;
        }

        // 2. Metric: Rata-Rata Tap Harian & Peak Hours
        $avgDailyTap = $daysCount > 0 ? round($currentTapsCount / $daysCount, 1) : 0;

        // Determine Peak Hours from actual taps in Sulsel timezone (WITA)
        $peakHourQuery = Tap::query();
        if ($startDate) {
            $peakHourQuery->where('tapped_at', '>=', $startDate);
        }
        $peakHourBucket = $peakHourQuery
            ->selectRaw('HOUR(tapped_at) as tap_hour, count(*) as count')
            ->groupBy('tap_hour')
            ->orderByDesc('count')
            ->first();

        if ($peakHourBucket && $peakHourBucket->tap_hour !== null && $currentTapsCount > 0) {
            $startHour = str_pad($peakHourBucket->tap_hour, 2, '0', STR_PAD_LEFT);
            $endHour = str_pad(($peakHourBucket->tap_hour + 2) % 24, 2, '0', STR_PAD_LEFT);
            $peakHoursText = "Puncak {$startHour}.00 – {$endHour}.00 WITA";
        } else {
            $peakHoursText = 'Puncak 12.00 – 14.00 WITA';
        }

        // 3. Metric: Panggilan Relay Darurat (Mode Barang Hilang)
        $emergencyCasesQuery = DB::table('messages')->where('type', 'lost_item');
        if ($startDate) {
            $emergencyCasesQuery->where('created_at', '>=', $startDate);
        }
        $emergencyCasesCount = $emergencyCasesQuery->count();

        // Count resolved/read messages
        $emergencyReadQuery = DB::table('messages')->where('type', 'lost_item')->whereNotNull('read_at');
        if ($startDate) {
            $emergencyReadQuery->where('created_at', '>=', $startDate);
        }
        $emergencyReadCount = $emergencyReadQuery->count();

        if ($emergencyCasesCount > 0) {
            $respPct = round(($emergencyReadCount / $emergencyCasesCount) * 100);
            $emergencyLabel = "{$emergencyReadCount} Selesai";
            $emergencyResponse = "{$respPct}% respons Mode Barang Hilang terhubung";
        } else {
            $emergencyLabel = '0 Laporan Aktif';
            $emergencyResponse = 'Mode Barang Hilang siaga aktif';
        }

        $metrics = [
            'total_tap' => number_format($currentTapsCount, 0, ',', '.'),
            'growth_rate' => $growthRate,
            'is_growth_positive' => $isGrowthPositive,
            'growth_label' => 'vs periode sebelumnya',
            'avg_daily_tap' => number_format($avgDailyTap, 1, ',', '.'),
            'peak_hours' => $peakHoursText,
            'emergency_cases' => $emergencyCasesCount,
            'emergency_label' => $emergencyLabel,
            'emergency_response' => $emergencyResponse,
        ];

        // 4. Trend Data Curve (7 Data Points across chosen range)
        $xCoords = [45, 140, 235, 335, 435, 520, 615];
        $trendData = [];
        $intervalDays = max(1, $daysCount / 6);
        $chartStart = $startDate ?? now()->subDays(30);

        $maxTaps = 0;
        $peakIndex = 0;

        for ($i = 0; $i < 7; $i++) {
            $pointDate = $chartStart->copy()->addDays((int) round($i * $intervalDays));
            $nextDate = $i === 6 ? now() : $pointDate->copy()->addDays((int) max(1, round($intervalDays)));

            $tapsInWindow = Tap::whereBetween('tapped_at', [$pointDate->copy()->startOfDay(), $nextDate->copy()->endOfDay()])->count();

            if ($tapsInWindow > $maxTaps) {
                $maxTaps = $tapsInWindow;
                $peakIndex = $i;
            }

            $trendData[] = [
                'date' => $pointDate->locale('id')->translatedFormat('d M'),
                'full_date' => $pointDate->locale('id')->translatedFormat('d M Y'),
                'taps' => $tapsInWindow,
                'x' => $xCoords[$i],
                'y' => 205, // computed below
                'is_peak' => false,
            ];
        }

        // Scale y-coordinates: y=205 is 0, y=45 is $maxScale
        $maxScale = max(10, (int) (ceil(($maxTaps ?: 10) / 10) * 10));
        foreach ($trendData as $idx => &$pt) {
            $pt['y'] = (int) round(205 - (($pt['taps'] / $maxScale) * 160));
            $pt['y'] = max(45, min(200, $pt['y']));
            if ($idx === $peakIndex && $maxTaps > 0) {
                $pt['is_peak'] = true;
                $pt['peak_label'] = "{$pt['taps']} Tap (Puncak)";
            } else {
                $pt['peak_label'] = "{$pt['taps']} Tap";
            }
        }
        unset($pt);

        $peakPoint = $trendData[$peakIndex];

        // Generate smooth SVG paths
        $splinePath = $this->buildSvgSmoothPath($trendData);
        $areaPath = $splinePath." L {$trendData[6]['x']} 205 L {$trendData[0]['x']} 205 Z";

        $chartScale = [
            'y200' => $maxScale,
            'y150' => (int) round($maxScale * 0.75),
            'y100' => (int) round($maxScale * 0.50),
            'y50' => (int) round($maxScale * 0.25),
        ];

        // 5. Mode Distribution Breakdown (calculated from actual taps with tag/profile mode)
        $tapsWithTags = Tap::query();
        if ($startDate) {
            $tapsWithTags->where('tapped_at', '>=', $startDate);
        }
        $tapsRecords = $tapsWithTags->with(['tag.user.profile'])->get();

        $modeTapCounts = [
            'kampus' => 0,
            'umkm' => 0,
            'kerja' => 0,
            'hilang' => 0,
        ];

        foreach ($tapsRecords as $tap) {
            $m = $tap->tag?->mode ?: ($tap->tag?->user?->profile?->active_mode ?? 'kampus');
            if (! array_key_exists($m, $modeTapCounts)) {
                $m = 'kampus';
            }
            $modeTapCounts[$m]++;
        }

        // If no taps in period, fall back to registered tag mode proportions
        $totalModeTaps = array_sum($modeTapCounts);
        if ($totalModeTaps === 0) {
            $tagsByMode = DB::table('tags')->where('status', 'aktif')->select('mode', DB::raw('count(*) as count'))->groupBy('mode')->pluck('count', 'mode')->toArray();
            $totalActiveTags = array_sum($tagsByMode);
            if ($totalActiveTags > 0) {
                foreach ($tagsByMode as $k => $c) {
                    if (array_key_exists($k, $modeTapCounts)) {
                        $modeTapCounts[$k] = $c;
                    }
                }
            }
        }

        $modeConfigs = [
            'kampus' => [
                'name' => 'Mode Kampus',
                'color' => '#540404',
                'badge_bg' => 'bg-[#F7EBEB]',
                'badge_text' => 'text-[#540404]',
                'dot_color' => 'bg-[#540404]',
            ],
            'umkm' => [
                'name' => 'Mode UMKM & Bisnis',
                'color' => '#D4AF37',
                'badge_bg' => 'bg-[#FCF5E3]',
                'badge_text' => 'text-[#8C6D23]',
                'dot_color' => 'bg-[#D4AF37]',
            ],
            'kerja' => [
                'name' => 'Mode Kerja & Portofolio',
                'color' => '#1A1A1A',
                'badge_bg' => 'bg-[#F2EEE9]',
                'badge_text' => 'text-[#1A1A1A]',
                'dot_color' => 'bg-[#1A1A1A]',
            ],
            'hilang' => [
                'name' => 'Mode Barang Hilang',
                'color' => '#A39E93',
                'badge_bg' => 'bg-[#F0ECE7]',
                'badge_text' => 'text-[#666666]',
                'dot_color' => 'bg-[#A39E93]',
            ],
        ];

        $totalTapsForDonut = array_sum($modeTapCounts);
        $circumference = 364.42; // 2 * PI * 58
        $accumulatedPct = 0;
        $modeBreakdown = [];
        $highestModeCount = -1;
        $mostActiveMode = ['name' => 'Mode Kampus'];

        foreach ($modeConfigs as $modeKey => $config) {
            $count = $modeTapCounts[$modeKey];
            $pct = $totalTapsForDonut > 0 ? (int) round(($count / $totalTapsForDonut) * 100) : 0;

            if ($count > $highestModeCount) {
                $highestModeCount = $count;
                $mostActiveMode = $config;
            }

            $strokeLength = round(($pct / 100) * $circumference, 2);
            $offset = round(-($accumulatedPct / 100) * $circumference, 2);
            $accumulatedPct += $pct;

            $modeBreakdown[] = [
                'name' => $config['name'],
                'taps' => number_format($count, 0, ',', '.'),
                'percentage' => $pct,
                'color' => $config['color'],
                'badge_bg' => $config['badge_bg'],
                'badge_text' => $config['badge_text'],
                'dot_color' => $config['dot_color'],
                'dash_array' => "{$strokeLength} {$circumference}",
                'dash_offset' => $offset,
            ];
        }

        // 6. Geographic concentration in South Sulawesi
        $regionQuery = Tap::query();
        if ($startDate) {
            $regionQuery->where('tapped_at', '>=', $startDate);
        }
        $dbRegions = $regionQuery
            ->whereNotNull('region')
            ->select('region', DB::raw('count(*) as count'))
            ->groupBy('region')
            ->orderByDesc('count')
            ->get();

        $regions = [];
        $rank = 1;

        if ($dbRegions->isNotEmpty()) {
            $totalRegionTaps = $dbRegions->sum('count');
            foreach ($dbRegions as $row) {
                $pct = $totalRegionTaps > 0 ? (int) round(($row->count / $totalRegionTaps) * 100) : 0;

                $badge = null;
                $badgeClass = '';
                $barColor = 'bg-[#C49A5E]';

                if ($row->region === 'Kota Makassar') {
                    $badge = 'PUSAT UTAMA';
                    $badgeClass = 'bg-[#F7EBEB] text-[#540404]';
                    $barColor = 'bg-[#540404]';
                } elseif ($row->region === 'Tana Toraja') {
                    $badge = 'HERITAGE TAG';
                    $badgeClass = 'bg-[#FCF5E3] text-[#8C6D23]';
                    $barColor = 'bg-[#E5BC50]';
                } elseif ($row->region === 'Kab. Gowa') {
                    $barColor = 'bg-[#1A1A1A]';
                }

                $regions[] = [
                    'rank' => $rank++,
                    'name' => $row->region,
                    'badge' => $badge,
                    'badge_class' => $badgeClass,
                    'taps' => number_format($row->count, 0, ',', '.').' tap',
                    'percentage' => $pct,
                    'bar_color' => $barColor,
                ];
            }
        } else {
            // Default regional empty state / reference regions with 0
            $defaultRegions = ['Kota Makassar', 'Kab. Gowa', 'Kab. Pangkep', 'Tana Toraja'];
            foreach ($defaultRegions as $regName) {
                $regions[] = [
                    'rank' => $rank++,
                    'name' => $regName,
                    'badge' => $regName === 'Kota Makassar' ? 'PUSAT UTAMA' : ($regName === 'Tana Toraja' ? 'HERITAGE TAG' : null),
                    'badge_class' => $regName === 'Kota Makassar' ? 'bg-[#F7EBEB] text-[#540404]' : 'bg-[#FCF5E3] text-[#8C6D23]',
                    'taps' => '0 tap',
                    'percentage' => 0,
                    'bar_color' => 'bg-[#ECE8E1]',
                ];
            }
        }

        return view('admin.analytics', compact(
            'metrics',
            'trendData',
            'peakPoint',
            'splinePath',
            'areaPath',
            'chartScale',
            'modeBreakdown',
            'mostActiveMode',
            'regions',
            'range',
            'rangeLabel'
        ));
    }

    /**
     * Export tap telemetry data to CSV (FR-ADM-06 & FR-ADM-10).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $range = $request->query('range', '30_days');
        [$startDate, , , , $rangeLabel] = $this->resolveDateRange($range);

        $tapsQuery = Tap::with(['tag.user.profile'])
            ->orderByDesc('tapped_at');

        if ($startDate) {
            $tapsQuery->where('tapped_at', '>=', $startDate);
        }

        $taps = $tapsQuery->get();

        // Record sensitive administrative action in audit log (FR-ADM-10)
        AuditLog::record('export_telemetry_csv', "Laporan telemetri NFC diekspor ({$taps->count()} baris, rentang: {$rangeLabel})");

        $filename = 'FlexiTag_Tap_Telemetry_'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($taps) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Tap',
                'Kode Tag',
                'Waktu Tap (WITA)',
                'Sumber Interaksi',
                'Tipe Perangkat',
                'Wilayah Geolokasi',
                'Mode Profil Aktif',
                'Pemilik Tag',
            ]);

            foreach ($taps as $tap) {
                $modeKey = $tap->tag?->mode ?: ($tap->tag?->user?->profile?->active_mode ?? 'kampus');
                $activeMode = match ($modeKey) {
                    'kampus' => 'Mode Kampus',
                    'umkm' => 'Mode UMKM & Bisnis',
                    'kerja' => 'Mode Kerja & Portofolio',
                    'hilang' => 'Mode Barang Hilang',
                    default => 'Mode Kampus',
                };

                fputcsv($handle, [
                    $tap->id,
                    $tap->tag?->tag_code ?? '-',
                    $tap->tapped_at ? $tap->tapped_at->format('Y-m-d H:i:s').' WITA' : '-',
                    strtoupper($tap->source ?? 'NFC'),
                    $tap->device_type ?? 'Smartphone',
                    $tap->region ?? 'Sulawesi Selatan',
                    $activeMode,
                    $tap->tag?->user?->name ?? 'Belum Ditautkan',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Resolve date bounds based on time range query parameter.
     *
     * @return array{0: Carbon|null, 1: Carbon|null, 2: Carbon|null, 3: int, 4: string}
     */
    private function resolveDateRange(string $range): array
    {
        return match ($range) {
            '7_days' => [
                now()->subDays(7),
                now()->subDays(14),
                now()->subDays(7),
                7,
                '7 Hari Terakhir',
            ],
            '90_days' => [
                now()->subDays(90),
                now()->subDays(180),
                now()->subDays(90),
                90,
                '90 Hari Terakhir',
            ],
            'year' => [
                now()->startOfYear(),
                now()->subYear()->startOfYear(),
                now()->subYear()->endOfYear(),
                max(1, (int) now()->dayOfYear),
                'Tahun Ini',
            ],
            'all' => [
                null,
                null,
                null,
                max(30, (int) now()->diffInDays(Carbon::create(2026, 1, 1))),
                'Semua Waktu',
            ],
            default => [
                now()->subDays(30),
                now()->subDays(60),
                now()->subDays(30),
                30,
                '30 Hari Terakhir',
            ],
        };
    }

    /**
     * Build smooth cubic Bezier SVG path through series of coordinates.
     */
    private function buildSvgSmoothPath(array $points): string
    {
        if (empty($points)) {
            return '';
        }

        $count = count($points);
        if ($count === 1) {
            return "M {$points[0]['x']} {$points[0]['y']}";
        }

        $d = "M {$points[0]['x']} {$points[0]['y']}";

        for ($i = 0; $i < $count - 1; $i++) {
            $p0 = $points[max(0, $i - 1)];
            $p1 = $points[$i];
            $p2 = $points[$i + 1];
            $p3 = $points[min($count - 1, $i + 2)];

            $cp1x = round($p1['x'] + ($p2['x'] - $p0['x']) / 6, 1);
            $cp1y = round($p1['y'] + ($p2['y'] - $p0['y']) / 6, 1);
            $cp2x = round($p2['x'] - ($p3['x'] - $p1['x']) / 6, 1);
            $cp2y = round($p2['y'] - ($p3['y'] - $p1['y']) / 6, 1);

            $d .= " C {$cp1x} {$cp1y}, {$cp2x} {$cp2y}, {$p2['x']} {$p2['y']}";
        }

        return $d;
    }
}
