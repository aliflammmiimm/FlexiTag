<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateLostModeRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
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

        // Ambil data tersimpan jika ada; jangan isi default text agar placeholder dapat tampil
        $currentNamaBarang = $savedData['nama_barang'] ?? $savedData['alert_title'] ?? '';
        $currentMessage = $savedData['message'] ?? $savedData['finder_message'] ?? '';
        $currentRelayPhone = $savedData['relay_phone'] ?? $user->no_whatsapp ?? '';
        $currentReward = $savedData['reward'] ?? $savedData['reward_info'] ?? '';
        $currentInstruksi = $savedData['instruksi_pengembalian'] ?? $savedData['kemungkinan_tertinggal'] ?? '';
        $formEnabled = isset($savedData['form_enabled']) ? (bool) $savedData['form_enabled'] : true;
        $shareLocation = isset($savedData['share_location']) ? (bool) $savedData['share_location'] : true;

        $isModeHilangActive = ($user->profile?->active_mode === 'hilang');
        $publicPreviewUrl = $primaryTag ? route('public.tag', ['tag_code' => $primaryTag->tag_code]) : null;

        // Ambil pesan penemuan barang masuk untuk pemilik tag (Owner Inbox / FR-LST-03)
        $lostMessages = $user->messages()
            ->where('type', 'lost_item')
            ->orderByDesc('created_at')
            ->get();
        $unreadMessagesCount = $lostMessages->whereNull('read_at')->count();

        return view('user.lost-mode.edit', [
            'user' => $user,
            'primaryTag' => $primaryTag,
            'currentNamaBarang' => $currentNamaBarang,
            'currentMessage' => $currentMessage,
            'currentRelayPhone' => $currentRelayPhone,
            'currentReward' => $currentReward,
            'currentInstruksi' => $currentInstruksi,
            'formEnabled' => $formEnabled,
            'shareLocation' => $shareLocation,
            'isModeHilangActive' => $isModeHilangActive,
            'publicPreviewUrl' => $publicPreviewUrl,
            'lostMessages' => $lostMessages,
            'unreadMessagesCount' => $unreadMessagesCount,
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
            'nama_barang' => $validated['nama_barang'] ?? null,
            'alert_title' => $validated['nama_barang'] ?? null,
            'message' => $validated['message'],
            'finder_message' => $validated['message'],
            'relay_phone' => $validated['relay_phone'],
            'reward' => $validated['reward'] ?? null,
            'reward_info' => $validated['reward'] ?? null,
            'instruksi_pengembalian' => $validated['instruksi_pengembalian'] ?? null,
            'kemungkinan_tertinggal' => $validated['instruksi_pengembalian'] ?? null,
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
            $personalName = $user->name ?: ($user->profile?->display_name ?? 'Pengguna FlexiTag');

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'active_mode' => 'hilang',
                    'display_name' => $personalName,
                ]
            );

            // Sinkronkan seluruh tag aktif milik pengguna ke mode hilang darurat
            $user->tags()->where('status', 'aktif')->update(['mode' => 'hilang']);
        }

        return redirect()->route('user.lost-mode.edit')
            ->with('status', 'Pengaturan Mode Hilang berhasil disimpan.');
    }

    /**
     * Tandai pesan penemuan barang sebagai telah dibaca (FR-LST-03).
     */
    public function markMessageAsRead(Request $request, Message $message): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if ((int) $message->user_id !== (int) $user->id) {
            abort(403, 'Akses pesan tidak diizinkan.');
        }

        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan telah ditandai sebagai dibaca.',
                'message_id' => $message->id,
                'read_at' => $message->read_at?->format('d M Y, H:i'),
            ]);
        }

        return back()->with('status', 'Pesan penemuan telah ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua pesan penemuan barang sebagai telah dibaca (FR-LST-03).
     */
    public function markAllMessagesAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $user->messages()
            ->where('type', 'lost_item')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua pesan penemuan telah ditandai sebagai dibaca.',
            ]);
        }

        return back()->with('status', 'Semua pesan penemuan telah ditandai sebagai dibaca.');
    }
}
