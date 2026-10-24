<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\ModeData;
use App\Models\Tag;
use App\Models\Tap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function landing(): View
    {
        return view('landing');
    }

    public function showTagProfile(Request $request, string $tag_code): View|RedirectResponse
    {
        $tag = Tag::with(['user.profile'])->where('tag_code', $tag_code)->firstOrFail();

        // FR-PUB-02: Jika belum aktif, arahkan ke aktivasi
        if ($tag->status === 'belum_aktif') {
            return redirect()->route('user.activation.show')
                ->with('warning', 'Gantungan kunci ini belum diaktivasi. Silakan lakukan klaim aktivasi terlebih dahulu.');
        }

        // FR-PUB-03: Jika nonaktif (Kill Switch pemilik atau blokir admin), tampilkan layar terkunci
        if ($tag->status === 'nonaktif') {
            return view('user.tag_locked', [
                'tag' => $tag,
                'isOwnerLocked' => $tag->disabled_by === 'owner',
            ]);
        }

        if ($tag->status === 'aktif' && $tag->user) {
            Tap::create([
                'tag_id' => $tag->id,
                'tapped_at' => now(),
                'source' => $request->header('sec-ch-ua-mobile') ? 'nfc' : 'link',
                'device_type' => $request->header('sec-ch-ua-mobile') ? 'mobile' : 'desktop',
                'region' => 'Makassar',
                'created_at' => now(),
            ]);
        }

        $user = $tag->user;
        $profile = $user?->profile;
        $activeMode = $profile?->active_mode ?? 'kampus';

        $links = $user
            ? Link::where('user_id', $user->id)->where('is_visible', true)->orderBy('sort_order')->get()
            : collect();

        // Tampilkan halaman publik khusus jika pengguna sedang dalam Mode UMKM
        if ($activeMode === 'umkm') {
            $modeRecord = $user
                ? ModeData::where('user_id', $user->id)->where('mode', 'umkm')->first()
                : null;
            $umkmData = $modeRecord?->data ?? [];

            return view('user.public_profile_umkm', [
                'tag' => $tag,
                'user' => $user,
                'profile' => $profile,
                'umkm' => $umkmData,
                'links' => $links,
            ]);
        }

        return view('user.public_profile', [
            'tag' => $tag,
            'user' => $user,
            'profile' => $profile,
            'links' => $links,
            'activeMode' => $activeMode,
        ]);
    }
}
