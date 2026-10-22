<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Console Dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        // 1. Metric Counts (from DB with design baseline)
        $dbOrderCount = DB::table('orders')->count();
        $dbTagCount = DB::table('tags')->where('status', 'aktif')->count();
        $dbPendingCount = DB::table('orders')->whereIn('status', ['menunggu_pembayaran', 'pembayaran_dikonfirmasi'])->count();
        $dbRevenue = DB::table('orders')->sum('total');

        $totalTags = DB::table('tags')->count();
        $rasioAktif = $totalTags > 0 ? round(($dbTagCount / $totalTags) * 100, 1) : 0;

        $metrics = [
            'total_pendapatan' => 'Rp '.number_format($dbRevenue, 0, ',', '.'),
            'growth_pendapatan' => '+12.5%',
            'total_pesanan' => $dbOrderCount,
            'new_orders' => '+'.$dbOrderCount.' pesanan',
            'tag_aktif' => $dbTagCount,
            'rasio_aktif' => $rasioAktif.'%',
            'perlu_dikirim' => $dbPendingCount,
        ];

        // 2. Weekly Trend Data (Sen - Min)
        $weeklyTrends = [
            ['day' => 'Sen', 'penjualan' => 38, 'aktivasi' => 30, 'is_peak' => false],
            ['day' => 'Sel', 'penjualan' => 48, 'aktivasi' => 36, 'is_peak' => false],
            ['day' => 'Rab', 'penjualan' => 54, 'aktivasi' => 46, 'is_peak' => false],
            ['day' => 'Kam', 'penjualan' => 42, 'aktivasi' => 52, 'is_peak' => false],
            ['day' => 'Jum', 'penjualan' => 76, 'aktivasi' => 72, 'is_peak' => true],
            ['day' => 'Sab', 'penjualan' => 58, 'aktivasi' => 55, 'is_peak' => false],
            ['day' => 'Min', 'penjualan' => 34, 'aktivasi' => 40, 'is_peak' => false],
        ];

        // 3. Customer Mode Distribution
        $modeDistribution = [
            [
                'name' => 'Mode Kampus',
                'count' => 56,
                'percentage' => 45,
                'color' => '#540404', // Deep Rich Maroon
                'bg_dot' => 'bg-[#540404]',
                'bg_bar' => 'bg-[#540404]',
            ],
            [
                'name' => 'Mode UMKM',
                'count' => 37,
                'percentage' => 30,
                'color' => '#7F6B2A', // Warm Olive-Gold
                'bg_dot' => 'bg-[#7F6B2A]',
                'bg_bar' => 'bg-[#7F6B2A]',
            ],
            [
                'name' => 'Mode Kerja',
                'count' => 19,
                'percentage' => 15,
                'color' => '#1A1A1A', // Dark Charcoal
                'bg_dot' => 'bg-[#1A1A1A]',
                'bg_bar' => 'bg-[#1A1A1A]',
            ],
            [
                'name' => 'Mode Barang Hilang',
                'count' => 12,
                'percentage' => 10,
                'color' => '#D4AF37', // Warm Gold
                'bg_dot' => 'bg-[#D4AF37]',
                'bg_bar' => 'bg-[#D4AF37]',
            ],
        ];

        // 4. Recent 5 Orders (Exclusively Ganci variants with SRS status mapping)
        $dbOrders = Order::latest()->take(5)->get();
        if ($dbOrders->isNotEmpty()) {
            $orders = $dbOrders->map(function ($order) {
                $badge = $order->status_badge;

                return [
                    'id_pesanan' => $order->order_no,
                    'nama_pembeli' => $order->customer_name,
                    'kota' => $order->address ? Str::limit($order->address, 20) : 'Makassar',
                    'produk' => $order->shape.' ('.$order->color.')',
                    'icon_type' => 'key',
                    'badge_label' => $badge['label'],
                    'badge_class' => $badge['bg_class'],
                    'badge_dot' => $badge['dot_class'],
                    'detail_status' => $order->detail_status_label,
                    'status_tag' => in_array($order->status, [Order::STATUS_DIRAKIT_DAN_DIUJI, Order::STATUS_SIAP_DIAMBIL_DIKIRIM, Order::STATUS_SELESAI]) ? 'aktif' : 'belum_aktif',
                ];
            })->toArray();
        } else {
            $orders = [
                [
                    'id_pesanan' => '#ORD-0001',
                    'nama_pembeli' => 'Andi Muhammad Rifki',
                    'kota' => 'Makassar',
                    'produk' => 'Ganci Bulat (Hitam Doff)',
                    'icon_type' => 'key',
                    'badge_label' => 'Diproses (Cetak/Rakit)',
                    'badge_class' => 'bg-[#F3E8FF] text-[#6B21A8] border border-[#E9D5FF]',
                    'badge_dot' => 'bg-[#6B21A8]',
                    'detail_status' => '5. Dirakit & Diuji (QC)',
                    'status_tag' => 'aktif',
                ],
            ];
        }

        return view('admin.dashboard', compact(
            'user',
            'metrics',
            'weeklyTrends',
            'modeDistribution',
            'orders'
        ));
    }
}
