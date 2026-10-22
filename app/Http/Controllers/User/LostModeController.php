<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateLostModeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LostModeController extends Controller
{
    /**
     * Tampilkan halaman Edit Profil (Mode Hilang) untuk pengguna login.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags']);

        // Data tag aktif milik user
        $tags = $user->tags()->orderByDesc('activated_at')->get();
        $primaryTag = $tags->where('status', 'aktif')->first() ?? $tags->first();

        // Ambil data mode hilang dari tabel mode_data jika sudah ada
        $modeRow = DB::table('mode_data')
            ->where('user_id', $user->id)
            ->where('mode', 'hilang')
            ->first();

        $savedData = [];
        if ($modeRow && $modeRow->data) {
            $savedData = json_decode($modeRow->data, true) ?: [];
        }

        // Nilai default jika belum pernah disimpan
        $defaultMessage = 'Halo! Barang ini milik saya dan sangat berharga. Jika Anda menemukannya, mohon bantu hubungi saya melalui formulir pesan relay di bawah ini. Terima kasih banyak!';
        $currentMessage = $savedData['message'] ?? $defaultMessage;
        $currentRelayPhone = $savedData['relay_phone'] ?? $user->no_whatsapp ?? '';
        $currentReward = $savedData['reward'] ?? '';
        $formEnabled = isset($savedData['form_enabled']) ? (bool) $savedData['form_enabled'] : true;
        $shareLocation = isset($savedData['share_location']) ? (bool) $savedData['share_location'] : true;

        $isModeHilangActive = ($user->profile?->active_mode === 'hilang');
        $publicPreviewUrl = $primaryTag ? route('public.tag', ['tag_code' => $primaryTag->tag_code]) : null;

        return view('user.lost-mode.edit', [
            'user' => $user,
            'primaryTag' => $primaryTag,
            'currentMessage' => $currentMessage,
            'currentRelayPhone' => $currentRelayPhone,
            'currentReward' => $currentReward,
            'formEnabled' => $formEnabled,
            'shareLocation' => $shareLocation,
            'isModeHilangActive' => $isModeHilangActive,
            'publicPreviewUrl' => $publicPreviewUrl,
        ]);
    }

    /**
     * Simpan pengaturan formulir informasi darurat Mode Hilang.
     */
    public function update(UpdateLostModeRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $dataPayload = [
            'message' => $validated['message'],
            'relay_phone' => $validated['relay_phone'],
            'reward' => $validated['reward'] ?? null,
            'form_enabled' => $request->boolean('form_enabled', false),
            'share_location' => $request->boolean('share_location', false),
            'updated_at' => now()->toIso8601String(),
        ];

        // Simpan ke tabel mode_data
        DB::table('mode_data')->updateOrInsert(
            ['user_id' => $user->id, 'mode' => 'hilang'],
            [
                'data' => json_encode($dataPayload),
                'updated_at' => now(),
            ]
        );

        // Aktifkan mode hilang pada profil user jika dicentang atau otomatis aktif
        if ($request->boolean('activate_mode', true)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'active_mode' => 'hilang',
                    'display_name' => $user->profile?->display_name ?? $user->name ?? 'Pengguna FlexiTag',
                ]
            );
        }

        return redirect()->route('user.lost-mode.edit')
            ->with('status', 'Pengaturan Mode Hilang berhasil disimpan.');
    }
}
