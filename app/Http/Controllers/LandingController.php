<?php

namespace App\Http\Controllers;

use App\Models\Design;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the official FlexiTag public landing page.
     */
    public function index()
    {
        $designs = [];
        try {
            $designs = Design::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();
        } catch (\Throwable $e) {
            // Silently fall back to empty collection if database is not ready
            $designs = collect([]);
        }

        // 4 Official Fallback Heritage Motifs per DESIGN.md & SRS
        $fallbackMotifs = [
            [
                'name' => 'Pa\'tedong',
                'origin' => 'Toraja',
                'meaning' => 'Simbol kemakmuran, martabat, dan keteguhan kerja keras khas suku Toraja.',
                'symbol' => '🐂',
                'bg_gradient' => 'linear-gradient(135deg, #3d0c11, #1a0507)',
            ],
            [
                'name' => 'Ne\' Limbongan',
                'origin' => 'Toraja',
                'meaning' => 'Melambangkan sumber mata air kehidupan dan persatuan rumpun keluarga.',
                'symbol' => '🌊',
                'bg_gradient' => 'linear-gradient(135deg, #1c2b36, #0e171d)',
            ],
            [
                'name' => 'Sa\'pi\' (Bunga Melati)',
                'origin' => 'Mandar',
                'meaning' => 'Keluhuran budi pekerti, keharuman nama baik, dan kesucian perjuangan hidup.',
                'symbol' => '🌸',
                'bg_gradient' => 'linear-gradient(135deg, #382414, #1c1109)',
            ],
            [
                'name' => 'Bunga Lontara',
                'origin' => 'Bugis-Makassar',
                'meaning' => 'Kecerdasan aksara, kebijaksanaan peradaban literasi, dan kehormatan Siri\' na Pacce.',
                'symbol' => '📜',
                'bg_gradient' => 'linear-gradient(135deg, #2b1f11, #140e07)',
            ],
        ];

        return view('landing.index', [
            'designs' => $designs,
            'fallbackMotifs' => $fallbackMotifs,
        ]);
    }
}
