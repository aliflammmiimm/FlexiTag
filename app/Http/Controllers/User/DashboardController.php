<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tap;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const MODE_LABELS = [
        'kampus' => 'Mode Kampus',
        'kerja' => 'Mode Kerja',
        'umkm' => 'Mode UMKM',
        'hilang' => 'Mode Barang Hilang',
    ];

    public function index(Request $request): View
    {
        $user = $request->user()->load('profile');

        // Hanya data milik pengguna yang sedang login.
        $tags = $user->tags()->orderByDesc('activated_at')->get();
        $tagIds = $tags->pluck('id');

        $totalTaps = $tagIds->isEmpty()
            ? 0
            : Tap::whereIn('tag_id', $tagIds)->count();

        $activeMode = $user->profile?->active_mode;

        return view('user.dashboard', [
            'user' => $user,
            'tags' => $tags,
            'activeTagCount' => $tags->where('status', 'aktif')->count(),
            'totalTaps' => $totalTaps,
            'activeModeLabel' => self::MODE_LABELS[$activeMode] ?? null,
        ]);
    }
}
