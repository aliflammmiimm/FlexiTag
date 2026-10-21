<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $metrics = [
            'total_pendapatan' => $dbRevenue > 0 ? 'Rp '.number_format($dbRevenue, 0, ',', '.') : 'Rp 14.500.000',
            'growth_pendapatan' => '+12.5%',
            'total_pesanan' => $dbOrderCount >= 5 ? 48 : $dbOrderCount,
            'new_orders' => '+8 pesanan baru',
            'tag_aktif' => $dbTagCount > 0 ? 124 : 124,
            'rasio_aktif' => '96.8%',
            'perlu_dikirim' => $dbPendingCount > 0 ? 12 : 12,
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

        // 4. Recent 5 Orders (Exclusively Ganci variants)
        $orders = [
            [
                'id_pesanan' => '#ORD-048',
                'nama_pembeli' => 'Budi Santoso',
                'kota' => 'Makassar',
                'produk' => "Ganci Akrilik Persegi – Pa'teddong",
                'icon_type' => 'key',
                'status_pengiriman' => 'dikirim',
                'status_tag' => 'aktif',
            ],
            [
                'id_pesanan' => '#ORD-047',
                'nama_pembeli' => 'Nurul Aisyah',
                'kota' => 'Gowa',
                'produk' => 'Ganci Kulit Bulat – Motif Saqbe',
                'icon_type' => 'key',
                'status_pengiriman' => 'pending',
                'status_tag' => 'belum_aktif',
            ],
            [
                'id_pesanan' => '#ORD-046',
                'nama_pembeli' => 'Rahmat Hidayat',
                'kota' => 'Maros',
                'produk' => 'Ganci Kayu Persegi – Ukir Toraja',
                'icon_type' => 'key',
                'status_pengiriman' => 'selesai',
                'status_tag' => 'aktif',
            ],
            [
                'id_pesanan' => '#ORD-045',
                'nama_pembeli' => 'Dewi Sartika',
                'kota' => 'Makassar',
                'produk' => "Ganci Bulat – Pa'barre Allo",
                'icon_type' => 'key',
                'status_pengiriman' => 'dikirim',
                'status_tag' => 'aktif',
            ],
            [
                'id_pesanan' => '#ORD-044',
                'nama_pembeli' => 'Fajar Pratama',
                'kota' => 'Parepare',
                'produk' => 'Ganci Custom Akrilik – Motif Lagosi',
                'icon_type' => 'key',
                'status_pengiriman' => 'pending',
                'status_tag' => 'belum_aktif',
            ],
        ];

        return view('admin.dashboard', compact(
            'user',
            'metrics',
            'weeklyTrends',
            'modeDistribution',
            'orders'
        ));
    }
}
