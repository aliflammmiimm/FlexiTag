<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tag;
use App\Models\Tap;
use App\Services\OrderActivationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Console Dashboard.
     */
    public function index(Request $request, OrderActivationService $activationService): View
    {
        $user = Auth::user();

        // 1. Resolve Selected Period
        $period = $request->query('period', 'pekan_ini');
        $now = Carbon::now();

        // Calculate Date Ranges
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);

        // Previous Week for comparison
        $prevStartOfWeek = $startOfWeek->copy()->subWeek();
        $prevEndOfWeek = $endOfWeek->copy()->subWeek();

        // Resolve labels & date filters based on period
        switch ($period) {
            case 'bulan_ini':
                $periodStartDate = $now->copy()->startOfMonth();
                $periodEndDate = $now->copy()->endOfMonth();
                $prevStartDate = $periodStartDate->copy()->subMonth();
                $prevEndDate = $periodStartDate->copy()->subSecond();
                $periodLabel = 'Bulan Ini ('.$now->locale('id')->translatedFormat('F Y').')';
                $periodShortLabel = 'bulan ini';
                $vsLabel = 'vs bulan lalu';
                break;

            case 'tahun_ini':
                $periodStartDate = $now->copy()->startOfYear();
                $periodEndDate = $now->copy()->endOfYear();
                $prevStartDate = $periodStartDate->copy()->subYear();
                $prevEndDate = $periodStartDate->copy()->subSecond();
                $periodLabel = 'Tahun Ini ('.$now->format('Y').')';
                $periodShortLabel = 'tahun ini';
                $vsLabel = 'vs tahun lalu';
                break;

            case 'semua':
                $periodStartDate = null;
                $periodEndDate = null;
                $prevStartDate = null;
                $prevEndDate = null;
                $periodLabel = 'Semua Waktu';
                $periodShortLabel = 'keseluruhan';
                $vsLabel = 'kumulatif';
                break;

            case 'pekan_ini':
            default:
                $period = 'pekan_ini';
                $periodStartDate = $startOfWeek;
                $periodEndDate = $endOfWeek;
                $prevStartDate = $prevStartOfWeek;
                $prevEndDate = $prevEndOfWeek;
                $periodLabel = 'Pekan Ini ('.$startOfWeek->locale('id')->translatedFormat('d').' – '.$endOfWeek->locale('id')->translatedFormat('d M Y').')';
                $periodShortLabel = 'pekan ini';
                $vsLabel = 'vs pekan lalu';
                break;
        }

        // 2. Metrics Calculation
        // A. Total Revenue (excluding cancelled orders)
        $totalRevenue = (float) Order::where('status', '!=', Order::STATUS_DIBATALKAN)->sum('total');

        // Revenue Comparison for Growth Rate
        if ($periodStartDate && $periodEndDate && $prevStartDate && $prevEndDate) {
            $currentPeriodRevenue = (float) Order::where('status', '!=', Order::STATUS_DIBATALKAN)
                ->whereBetween('created_at', [$periodStartDate, $periodEndDate])
                ->sum('total');
            $prevPeriodRevenue = (float) Order::where('status', '!=', Order::STATUS_DIBATALKAN)
                ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->sum('total');

            if ($prevPeriodRevenue > 0) {
                $growthPct = round((($currentPeriodRevenue - $prevPeriodRevenue) / $prevPeriodRevenue) * 100, 1);
                $growthLabel = ($growthPct >= 0 ? '+' : '').$growthPct.'%';
            } elseif ($currentPeriodRevenue > 0) {
                $growthLabel = '+100%';
            } else {
                $growthLabel = '0%';
            }
        } else {
            $growthLabel = '+0%';
        }

        // B. Total Orders & New Orders This Period
        $totalOrdersCount = Order::count();
        if ($periodStartDate && $periodEndDate) {
            $newOrdersCount = Order::whereBetween('created_at', [$periodStartDate, $periodEndDate])->count();
            $newOrdersLabel = "+{$newOrdersCount} {$periodShortLabel}";
        } else {
            $newOrdersLabel = "{$totalOrdersCount} pesanan total";
        }

        // C. Tag Aktif & Rasio Aktif
        $activeTagCount = Tag::where('status', 'aktif')->count();
        $totalTagsCount = Tag::count();
        $rasioAktif = $totalTagsCount > 0 ? round(($activeTagCount / $totalTagsCount) * 100, 1) : 0;

        // D. Perlu Dikirim / Diproses
        // According to SRS workflow: Orders confirmed and currently in production/assembly/shipping
        $perluDikirimCount = Order::whereIn('status', [
            Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
            Order::STATUS_DESAIN_DISETUJUI,
            Order::STATUS_DICETAK,
            Order::STATUS_DIRAKIT_DAN_DIUJI,
        ])->count();
        $siapDikirimCount = Order::where('status', Order::STATUS_SIAP_DIAMBIL_DIKIRIM)->count();

        $availableStockCount = $activationService->getAvailableStockCount();
        $isLowStock = $availableStockCount < OrderActivationService::LOW_STOCK_THRESHOLD;

        $metrics = [
            'total_pendapatan' => 'Rp '.number_format($totalRevenue, 0, ',', '.'),
            'growth_pendapatan' => $growthLabel,
            'growth_vs' => $vsLabel,
            'is_growth_positive' => ! str_starts_with($growthLabel, '-'),
            'total_pesanan' => number_format($totalOrdersCount, 0, ',', '.'),
            'new_orders' => $newOrdersLabel,
            'tag_aktif' => $activeTagCount,
            'total_tag' => $totalTagsCount,
            'rasio_aktif' => $rasioAktif.'%',
            'perlu_dikirim' => $perluDikirimCount + $siapDikirimCount,
            'siap_pickup' => $siapDikirimCount,
            'stok_tag_tersedia' => $availableStockCount,
            'is_low_stock' => $isLowStock,
        ];

        // 3. Weekly Trend Data (Senin - Minggu of current week)
        $daysMeta = [
            ['day' => 'Sen', 'day_full' => 'Senin'],
            ['day' => 'Sel', 'day_full' => 'Selasa'],
            ['day' => 'Rab', 'day_full' => 'Rabu'],
            ['day' => 'Kam', 'day_full' => 'Kamis'],
            ['day' => 'Jum', 'day_full' => 'Jumat'],
            ['day' => 'Sab', 'day_full' => 'Sabtu'],
            ['day' => 'Min', 'day_full' => 'Minggu'],
        ];

        $weeklyTrends = [];
        $maxTrendValue = 1;
        $maxDaySum = 0;
        $peakIndex = -1;

        for ($i = 0; $i < 7; $i++) {
            $dayCarbon = $startOfWeek->copy()->addDays($i);
            $dayDate = $dayCarbon->toDateString();

            // Real physical orders / items ordered on that day
            $ordersQuery = Order::whereDate('created_at', $dayDate)
                ->where('status', '!=', Order::STATUS_DIBATALKAN);
            $salesCount = (int) ($ordersQuery->sum('quantity') ?: $ordersQuery->count());

            // Real tag activations on that day + NFC taps on that day
            $activatedTagsCount = Tag::whereDate('activated_at', $dayDate)->count();
            $tapsCount = Tap::whereDate('tapped_at', $dayDate)->count();
            $activationsCount = $activatedTagsCount + $tapsCount;

            $daySum = $salesCount + $activationsCount;
            if ($daySum > $maxDaySum) {
                $maxDaySum = $daySum;
                $peakIndex = $i;
            }

            if ($salesCount > $maxTrendValue) {
                $maxTrendValue = $salesCount;
            }
            if ($activationsCount > $maxTrendValue) {
                $maxTrendValue = $activationsCount;
            }

            $weeklyTrends[] = [
                'day' => $daysMeta[$i]['day'],
                'day_full' => $daysMeta[$i]['day_full'],
                'date' => $dayDate,
                'date_formatted' => $dayCarbon->locale('id')->translatedFormat('d M'),
                'penjualan' => $salesCount,
                'aktivasi' => $activationsCount,
                'is_peak' => false,
            ];
        }

        // Set peak day if there is any activity
        if ($peakIndex >= 0 && $maxDaySum > 0) {
            $weeklyTrends[$peakIndex]['is_peak'] = true;
        }

        // Calculate bar heights proportionally so they fit into ~130px max
        foreach ($weeklyTrends as &$trend) {
            $trend['penjualan_height'] = $trend['penjualan'] > 0
                ? max(8, (int) round(($trend['penjualan'] / $maxTrendValue) * 128))
                : 4;
            $trend['aktivasi_height'] = $trend['aktivasi'] > 0
                ? max(8, (int) round(($trend['aktivasi'] / $maxTrendValue) * 128))
                : 4;
        }
        unset($trend);

        // Average Tap Telemetry Footer
        $totalTapsCount = Tap::count() + DB::table('link_clicks')->count();
        $avgTapPerTag = $activeTagCount > 0 ? round($totalTapsCount / $activeTagCount, 1) : 0;

        // 4. Customer Mode Distribution (FR-ADM-01)
        $activeTags = Tag::with('user.profile')->where('status', 'aktif')->get();
        $totalActiveTagsCount = $activeTags->count();

        $modeCounts = [
            'kampus' => 0,
            'umkm' => 0,
            'kerja' => 0,
            'hilang' => 0,
        ];

        if ($totalActiveTagsCount > 0) {
            foreach ($activeTags as $tag) {
                $m = $tag->mode ?: ($tag->user?->profile?->active_mode ?? 'kampus');
                if (! array_key_exists($m, $modeCounts)) {
                    $m = 'kampus';
                }
                $modeCounts[$m]++;
            }
        } else {
            $profiles = DB::table('profiles')->get();
            if ($profiles->isNotEmpty()) {
                $totalActiveTagsCount = $profiles->count();
                foreach ($profiles as $p) {
                    $m = $p->active_mode ?? 'kampus';
                    if (! array_key_exists($m, $modeCounts)) {
                        $m = 'kampus';
                    }
                    $modeCounts[$m]++;
                }
            }
        }

        $modeMeta = [
            'kampus' => [
                'name' => 'Mode Kampus',
                'color' => '#8B0000',
                'bg_dot' => 'bg-[#8B0000]',
                'bg_bar' => 'bg-[#8B0000]',
            ],
            'umkm' => [
                'name' => 'Mode UMKM',
                'color' => '#B85D19',
                'bg_dot' => 'bg-[#B85D19]',
                'bg_bar' => 'bg-[#B85D19]',
            ],
            'kerja' => [
                'name' => 'Mode Kerja',
                'color' => '#1A1A1A',
                'bg_dot' => 'bg-[#1A1A1A]',
                'bg_bar' => 'bg-[#1A1A1A]',
            ],
            'hilang' => [
                'name' => 'Mode Barang Hilang',
                'color' => '#DC2626',
                'bg_dot' => 'bg-[#DC2626]',
                'bg_bar' => 'bg-[#DC2626]',
            ],
        ];

        $modeDistribution = [];
        foreach ($modeMeta as $key => $meta) {
            $count = $modeCounts[$key];
            $pct = $totalActiveTagsCount > 0 ? (int) round(($count / $totalActiveTagsCount) * 100) : 0;
            $modeDistribution[] = [
                'key' => $key,
                'name' => $meta['name'],
                'count' => $count,
                'percentage' => $pct,
                'color' => $meta['color'],
                'bg_dot' => $meta['bg_dot'],
                'bg_bar' => $meta['bg_bar'],
            ];
        }

        // 5. Recent 5 Orders (Real Database Orders)
        $dbOrders = Order::latest()->take(5)->get();
        $orders = $dbOrders->map(function ($order) {
            $badge = $order->status_badge;

            return [
                'id_pesanan' => $order->order_no,
                'nama_pembeli' => $order->customer_name,
                'kota' => $order->address ? Str::limit($order->address, 22) : 'Makassar',
                'produk' => $order->shape.' ('.$order->color.')',
                'quantity' => $order->quantity,
                'channel' => $order->channel ?? 'online',
                'channel_badge' => $order->channel_badge ?? null,
                'icon_type' => 'key',
                'badge_label' => $badge['label'],
                'badge_class' => $badge['bg_class'],
                'badge_dot' => $badge['dot_class'],
                'detail_status' => $order->detail_status_label,
                'status_tag' => in_array($order->status, [Order::STATUS_DIRAKIT_DAN_DIUJI, Order::STATUS_SIAP_DIAMBIL_DIKIRIM, Order::STATUS_SELESAI]) ? 'aktif' : 'belum_aktif',
            ];
        })->toArray();

        return view('admin.dashboard', compact(
            'user',
            'period',
            'periodLabel',
            'metrics',
            'weeklyTrends',
            'maxTrendValue',
            'avgTapPerTag',
            'totalTapsCount',
            'modeDistribution',
            'totalActiveTagsCount',
            'orders',
            'availableStockCount',
            'isLowStock'
        ));
    }
}
