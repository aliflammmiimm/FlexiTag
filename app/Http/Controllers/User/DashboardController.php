<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Tap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public const MODE_LABELS = [
        'kampus' => 'Mode Kampus',
        'kerja' => 'Mode Kerja',
        'umkm' => 'Mode UMKM',
        'hilang' => 'Mode Barang Hilang',
    ];

    public function index(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags']);

        // Data tag milik pengguna yang sedang login
        $tags = $user->tags()->orderByDesc('activated_at')->get();
        $primaryTag = $tags->where('status', 'aktif')->first() ?? $tags->first();
        $activeTagCount = $tags->where('status', 'aktif')->count();

        $tagIds = $tags->pluck('id');

        // Metrik kunjungan / tap
        $totalTaps = $tagIds->isNotEmpty()
            ? Tap::whereIn('tag_id', $tagIds)->count()
            : 0;

        $weeklyTaps = $tagIds->isNotEmpty()
            ? Tap::whereIn('tag_id', $tagIds)->where('created_at', '>=', now()->subDays(7))->count()
            : 0;

        $lastTap = $tagIds->isNotEmpty()
            ? Tap::whereIn('tag_id', $tagIds)->latest('created_at')->first()
            : null;

        if ($lastTap && $lastTap->created_at) {
            $diffHours = (int) $lastTap->created_at->diffInHours(now());
            if ($diffHours < 1) {
                $lastActivity = (int) $lastTap->created_at->diffInMinutes(now()).' Mnt lalu';
            } elseif ($diffHours < 24) {
                $lastActivity = $diffHours.' Jam lalu';
            } else {
                $lastActivity = (int) $lastTap->created_at->diffInDays(now()).' Hari lalu';
            }
        } else {
            $lastActivity = '2 Jam lalu';
        }

        $activeMode = $user->profile?->active_mode ?? 'kampus';
        $displayName = $user->profile?->display_name ?? $user->name ?? 'Pengguna FlexiTag';
        $headline = $user->profile?->headline ?? 'Member MaccaTech';

        // Hitung inisial nama untuk avatar
        $words = preg_split('/\s+/', trim($displayName));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= Str::upper(mb_substr($w, 0, 1));
        }
        $initials = $initials ?: 'AD';

        $tagCode = $primaryTag?->tag_code ?? 'FT-8X9Y2Z';
        $publicUrl = $primaryTag ? $primaryTag->public_url : url('/u/'.$tagCode);

        return view('user.dashboard', [
            'user' => $user,
            'profile' => $user->profile,
            'tags' => $tags,
            'primaryTag' => $primaryTag,
            'activeTagCount' => $activeTagCount,
            'totalTaps' => $totalTaps > 0 ? $totalTaps : 145,
            'weeklyTaps' => $weeklyTaps > 0 ? $weeklyTaps : 18,
            'lastActivity' => $lastActivity,
            'displayName' => $displayName,
            'headline' => $headline,
            'initials' => $initials,
            'activeMode' => $activeMode,
            'activeModeLabel' => self::MODE_LABELS[$activeMode] ?? 'Mode Kampus',
            'tagCode' => $tagCode,
            'publicUrl' => $publicUrl,
        ]);
    }

    /**
     * Beralih mode aktif (Mode Kampus, Mode Kerja, Mode UMKM, Mode Barang Hilang).
     */
    public function switchMode(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'string', 'in:kampus,kerja,umkm,hilang'],
        ]);

        $user = $request->user();
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'active_mode' => $data['mode'],
                'theme' => 'sulawesi_heritage',
            ]
        );

        $modeLabel = self::MODE_LABELS[$data['mode']] ?? $data['mode'];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mode' => $data['mode'],
                'label' => $modeLabel,
                'message' => 'Mode berhasil dialihkan ke '.$modeLabel,
            ]);
        }

        return redirect()->route('user.dashboard')
            ->with('status', 'Mode profil aktif berhasil dialihkan ke '.$modeLabel.'.');
    }

    /**
     * Update cepat informasi profil dari dashboard.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'display_name' => ['required', 'string', 'min:2', 'max:100'],
            'headline' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'no_whatsapp' => ['nullable', 'string', 'max:25'],
        ], [
            'display_name.required' => 'Nama tampilan wajib diisi.',
            'display_name.min' => 'Nama tampilan minimal 2 karakter.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $profileData = [
            'display_name' => $data['display_name'],
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            if ($user->profile?->photo_path) {
                Storage::disk('public')->delete($user->profile->photo_path);
            }
            $profileData['photo_path'] = $request->file('photo')->store('profiles', 'public');
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        if ($request->filled('no_whatsapp')) {
            $cleanWa = preg_replace('/[^0-9]/', '', (string) $request->no_whatsapp);
            if (Str::startsWith($cleanWa, '0')) {
                $cleanWa = '62'.substr($cleanWa, 1);
            }
            $user->no_whatsapp = $cleanWa;
            $user->save();

            Link::updateOrCreate(
                ['user_id' => $user->id, 'type' => 'whatsapp'],
                [
                    'label' => 'WhatsApp Utama',
                    'url' => 'https://wa.me/'.$cleanWa,
                    'is_visible' => true,
                    'sort_order' => 1,
                ]
            );
        }

        return redirect()->route('user.dashboard')
            ->with('status', 'Profil berhasil diperbarui!');
    }

    /**
     * Buka profil digital publik milik pengguna yang sedang login.
     */
    public function myProfile(Request $request): View|RedirectResponse
    {
        $user = $request->user()->load(['tags', 'profile', 'links' => function ($q) {
            $q->where('is_visible', true)->orderBy('sort_order');
        }, 'privacySetting', 'modeData']);

        $tag = $user->tags()->where('status', 'aktif')->first() ?? $user->tags()->first();

        if ($tag) {
            return redirect()->route('public.tag', $tag->tag_code);
        }

        // Jika user belum punya tag terdaftar, render profil virtual langsung sesuai data user
        $virtualTag = new \App\Models\Tag([
            'tag_code' => 'FT-'.Str::upper(Str::random(6)),
            'status' => 'aktif',
            'batch_id' => 'EDISI PUSAKA KUNCI PA\'BARRE ALLO',
        ]);
        $virtualTag->id = 0;
        $virtualTag->setRelation('user', $user);

        $profile = $user->profile;
        $links = $user->links ?? collect();
        $privacy = $user->privacySetting;
        $activeMode = $profile?->active_mode ?? 'kerja';
        $modeData = $user->modeData?->firstWhere('mode', $activeMode)?->data ?? [];

        return view('user.public_profile', [
            'tag' => $virtualTag,
            'user' => $user,
            'profile' => $profile,
            'links' => $links,
            'privacy' => $privacy,
            'activeMode' => $activeMode,
            'modeData' => $modeData,
            'isLocked' => false,
        ]);
    }
}
