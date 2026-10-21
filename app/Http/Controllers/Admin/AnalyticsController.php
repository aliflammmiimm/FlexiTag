<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display the NFC Analytics & Tap interactions dashboard.
     */
    public function index(Request $request): View
    {
        $range = $request->query('range', '30_days');

        // Top Summary Metric Cards
        $metrics = [
            'total_tap' => '3.420',
            'growth_rate' => '+18.4%',
            'growth_label' => 'vs bulan sebelumnya',
            'avg_daily_tap' => 114,
            'peak_hours' => 'Puncak 12.00 – 15.00 WITA',
            'emergency_cases' => 5,
            'emergency_label' => 'Kasus Selesai',
            'emergency_response' => '100% respons Mode Barang Hilang terhubung',
        ];

        // 30-Day NFC Scan Frequency Curve Points
        $trendData = [
            [
                'date' => '20 Apr',
                'full_date' => '20 Apr 2025',
                'taps' => 25,
                'x' => 45,
                'y' => 195,
                'is_peak' => false,
            ],
            [
                'date' => '25 Apr',
                'full_date' => '25 Apr 2025',
                'taps' => 55,
                'x' => 140,
                'y' => 170,
                'is_peak' => false,
            ],
            [
                'date' => '30 Apr',
                'full_date' => '30 Apr 2025',
                'taps' => 82,
                'x' => 235,
                'y' => 148,
                'is_peak' => false,
            ],
            [
                'date' => '05 Mei',
                'full_date' => '05 Mei 2025',
                'taps' => 126,
                'x' => 335,
                'y' => 112,
                'is_peak' => false,
            ],
            [
                'date' => '10 Mei',
                'full_date' => '10 Mei 2025',
                'taps' => 174,
                'x' => 435,
                'y' => 70,
                'is_peak' => false,
            ],
            [
                'date' => '14 Mei',
                'full_date' => '14 Mei 2025',
                'taps' => 184,
                'x' => 520,
                'y' => 58,
                'is_peak' => true,
                'peak_label' => '184 Tap (Peak)',
            ],
            [
                'date' => '19 Mei',
                'full_date' => '19 Mei 2025',
                'taps' => 172,
                'x' => 615,
                'y' => 72,
                'is_peak' => false,
            ],
        ];

        // Mode Distribution Breakdown
        $modeBreakdown = [
            [
                'name' => 'Mode Kampus',
                'taps' => '1.539',
                'percentage' => 45,
                'color' => '#540404',
                'badge_bg' => 'bg-[#F7EBEB]',
                'badge_text' => 'text-[#540404]',
                'dot_color' => 'bg-[#540404]',
            ],
            [
                'name' => 'Mode UMKM & Bisnis',
                'taps' => '1.026',
                'percentage' => 30,
                'color' => '#D4AF37',
                'badge_bg' => 'bg-[#FCF5E3]',
                'badge_text' => 'text-[#8C6D23]',
                'dot_color' => 'bg-[#D4AF37]',
            ],
            [
                'name' => 'Mode Kerja & Portofolio',
                'taps' => '513',
                'percentage' => 15,
                'color' => '#1A1A1A',
                'badge_bg' => 'bg-[#F2EEE9]',
                'badge_text' => 'text-[#1A1A1A]',
                'dot_color' => 'bg-[#1A1A1A]',
            ],
            [
                'name' => 'Mode Barang Hilang',
                'taps' => '342',
                'percentage' => 10,
                'color' => '#A39E93',
                'badge_bg' => 'bg-[#F0ECE7]',
                'badge_text' => 'text-[#666666]',
                'dot_color' => 'bg-[#A39E93]',
            ],
        ];

        // Geographic concentration in South Sulawesi
        $regions = [
            [
                'rank' => 1,
                'name' => 'Kota Makassar',
                'badge' => 'PUSAT UTAMA',
                'badge_class' => 'bg-[#F7EBEB] text-[#540404]',
                'taps' => '2.052 tap',
                'percentage' => 60,
                'bar_color' => 'bg-[#540404]',
            ],
            [
                'rank' => 2,
                'name' => 'Kab. Pangkep',
                'badge' => null,
                'badge_class' => '',
                'taps' => '684 tap',
                'percentage' => 20,
                'bar_color' => 'bg-[#C49A5E]',
            ],
            [
                'rank' => 3,
                'name' => 'Kab. Gowa',
                'badge' => null,
                'badge_class' => '',
                'taps' => '410 tap',
                'percentage' => 12,
                'bar_color' => 'bg-[#1A1A1A]',
            ],
            [
                'rank' => 4,
                'name' => 'Tana Toraja',
                'badge' => 'HERITAGE TAG',
                'badge_class' => 'bg-[#FCF5E3] text-[#8C6D23]',
                'taps' => '274 tap',
                'percentage' => 8,
                'bar_color' => 'bg-[#E5BC50]',
            ],
        ];

        return view('admin.analytics', compact(
            'metrics',
            'trendData',
            'modeBreakdown',
            'regions',
            'range'
        ));
    }
}
