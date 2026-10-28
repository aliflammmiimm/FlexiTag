<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\LostItemMessageNotification;
use App\Models\Design;
use App\Models\Link;
use App\Models\Message;
use App\Models\Tag;
use App\Models\Tap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function landing(): View
    {
        $designs = Design::active()->get()
            ->sortByDesc(fn (Design $d) => $d->units_sold)
            ->values()
            ->take(4);

        return view('landing', compact('designs'));
    }

    public function showTagProfile(Request $request, string $tag_code): View|RedirectResponse
    {
        $tag = Tag::with(['user.profile', 'user.links' => function ($q) {
            $q->where('is_visible', true)->orderBy('sort_order');
        }, 'user.privacySetting', 'user.modeData'])->where('tag_code', $tag_code)->firstOrFail();

        // FR-PUB-02: Jika tag belum aktif, alihkan ke alur aktivasi
        if ($tag->status === 'belum_aktif') {
            return redirect()->route('user.activation.show', [
                'step' => 2,
                'tag_code' => $tag->tag_code,
            ])->with('warning', 'Gantungan kunci ini belum diaktivasi. Silakan lakukan klaim aktivasi terlebih dahulu.');
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
        $links = $user?->links ?? collect();
        $privacy = $user?->privacySetting;

        // Mode resolution:
        // Prioritaskan mode spesifik milik tag ($tag->mode), fallback ke active_mode profil.
        // Jika status profil user sedang dalam darurat 'hilang', prioritaskan 'hilang' kecuali jika ada query ?mode= eksplisit.
        $defaultMode = $tag->mode ?: ($profile?->active_mode ?? 'kampus');
        if ($profile?->active_mode === 'hilang' && ! $request->has('mode')) {
            $defaultMode = 'hilang';
        }
        $activeMode = $request->query('mode', $defaultMode);

        // Mode data resolution: check user relation first, fallback to DB query
        $modeRow = $user?->modeData?->firstWhere('mode', $activeMode);
        if ($modeRow) {
            $modeData = is_array($modeRow->data) ? $modeRow->data : json_decode($modeRow->data, true);
        } else {
            $dbRow = DB::table('mode_data')->where('user_id', $user?->id)->where('mode', $activeMode)->first();
            $modeData = $dbRow && $dbRow->data ? json_decode($dbRow->data, true) : [];
        }
        $modeData = is_array($modeData) ? $modeData : [];

        // Tampilkan halaman publik khusus jika pengguna sedang dalam Mode UMKM
        if ($activeMode === 'umkm') {
            return view('user.public_profile_umkm', [
                'tag' => $tag,
                'user' => $user,
                'profile' => $profile,
                'umkm' => $modeData,
                'links' => $links,
            ]);
        }

        return view('user.public_profile', [
            'tag' => $tag,
            'user' => $user,
            'profile' => $profile,
            'links' => $links,
            'privacy' => $privacy,
            'activeMode' => $activeMode,
            'modeData' => $modeData,
            'isLocked' => $tag->status === 'nonaktif',
        ]);
    }

    /**
     * Unduh kontak vCard (.vcf) untuk aksi "Simpan Kontak".
     */
    public function downloadVCard(string $tag_code): Response
    {
        $tag = Tag::with(['user.profile', 'user.privacySetting'])->where('tag_code', $tag_code)->firstOrFail();
        $user = $tag->user;
        $profile = $user?->profile;
        $privacy = $user?->privacySetting;

        $name = $profile?->display_name ?? $user?->name ?? 'FlexiTag Contact';
        $headline = $profile?->headline ?? '';
        $phone = ($privacy?->show_phone ?? true) ? ($user?->no_whatsapp ?? '') : '';
        $email = ($privacy?->show_email ?? true) ? ($user?->email ?? '') : '';
        $url = url('/u/'.$tag->tag_code);

        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "FN:{$name}\r\n";
        if ($headline) {
            $vcard .= "TITLE:{$headline}\r\n";
        }
        if ($phone) {
            $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '+62'.substr($cleanPhone, 1);
            }
            $vcard .= "TEL;TYPE=CELL,VOICE:{$cleanPhone}\r\n";
        }
        if ($email) {
            $vcard .= "EMAIL;TYPE=INTERNET:{$email}\r\n";
        }
        $vcard .= "URL:{$url}\r\n";
        $vcard .= "NOTE:Kontak via Smart Merchandise FlexiTag ({$tag->tag_code})\r\n";
        $vcard .= "END:VCARD\r\n";

        $filename = Str::slug($name, '_').'.vcf';

        return response($vcard, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Catat analitik klik tautan dan teruskan ke URL tujuan (FR-PUB-11).
     */
    public function clickLink(string $tag_code, int $link_id): RedirectResponse
    {
        $link = Link::findOrFail($link_id);

        DB::table('link_clicks')->insert([
            'link_id' => $link->id,
            'clicked_at' => now(),
        ]);

        return redirect()->away($link->url);
    }

    /**
     * Unduh file CV / resume pengguna pada Mode Kerja.
     */
    public function downloadCv(string $tag_code): Response|RedirectResponse
    {
        $tag = Tag::with(['user.profile', 'user.modeData'])->where('tag_code', $tag_code)->firstOrFail();
        $user = $tag->user;
        $profile = $user?->profile;
        $modeData = $user?->modeData?->firstWhere('mode', 'kerja')?->data ?? [];
        $cvPath = $modeData['cv_file_path'] ?? null;

        if ($cvPath) {
            if (Storage::disk('public')->exists($cvPath)) {
                $nameForFile = $profile?->display_name ?? $user?->name ?? 'Curriculum_Vitae';
                $ext = pathinfo($cvPath, PATHINFO_EXTENSION) ?: 'pdf';
                $filename = Str::slug($nameForFile, '_').'_cv.'.$ext;

                return Storage::disk('public')->download($cvPath, $filename);
            }
            if (file_exists(public_path($cvPath))) {
                return response()->download(public_path($cvPath));
            }
        }

        $name = $profile?->display_name ?? $user?->name ?? 'Curriculum Vitae';
        $headline = $modeData['headline'] ?? $profile?->headline ?? 'Software Engineer & Embedded IoT Developer';
        $phone = $modeData['telepon_kerja'] ?? $user?->no_whatsapp ?? '';
        $email = $modeData['email_kerja'] ?? $user?->email ?? '';

        $resumeContent = "========================================================\n"
            .'  CURRICULUM VITAE - '.strtoupper($name)."\n"
            ."========================================================\n\n"
            ."Posisi     : {$headline}\n"
            ."Telepon    : {$phone}\n"
            ."Email      : {$email}\n"
            ."Lokasi     : Makassar, Sulawesi Selatan\n"
            ."FlexiTag ID: {$tag->tag_code}\n\n"
            ."PROFIL PROFESIONAL:\n"
            .($modeData['bio'] ?? 'Fokus membangun sistem web modern dan perangkat keras pintar berbasis mikrokontroler.')."\n\n"
            ."TAUTAN RESMI:\n"
            ."- Portofolio: https://andimuhammad.dev\n"
            ."- LinkedIn  : https://linkedin.com/in/andimuhammad\n"
            ."- GitHub    : https://github.com/andimuhammad\n"
            ."- Behance   : https://behance.net/andimuhammad\n\n"
            ."--------------------------------------------------------\n"
            ."Diverifikasi oleh FlexiTag Smart Merchandise (MaccaTech)\n"
            ."Enkripsi Kunci Aman Sulawesi Selatan\n";

        $filename = Str::slug($name, '_').'_cv.txt';

        return response($resumeContent, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Kirim pesan aman dari penemu barang ke pemilik tag (FR-LST-01, FR-LST-02, FR-LST-04).
     */
    public function submitLostItemMessage(Request $request, string $tag_code): JsonResponse|RedirectResponse
    {
        $tag = Tag::with('user')->where('tag_code', $tag_code)->firstOrFail();

        if ($tag->status !== 'aktif' || ! $tag->user) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Perangkat tag tidak aktif atau belum terikat pada pemilik.'], 422);
            }

            return back()->with('error', 'Perangkat tag tidak aktif atau belum terikat pada pemilik.');
        }

        $validated = $request->validate([
            'sender_name' => ['nullable', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:1000'],
            'contact' => ['nullable', 'string', 'max:100'],
        ], [
            'location.required' => 'Lokasi penemuan barang wajib diisi agar pemilik dapat menjemputnya.',
            'location.max' => 'Lokasi penemuan maksimal 150 karakter.',
            'message.required' => 'Pesan atau keterangan penemuan wajib diisi.',
            'message.max' => 'Pesan maksimal 1000 karakter.',
        ]);

        $lostMessage = Message::create([
            'type' => 'lost_item',
            'user_id' => $tag->user_id,
            'sender_name' => ! empty($validated['sender_name']) ? strip_tags(trim($validated['sender_name'])) : 'Penemu Baik Hati',
            'location' => strip_tags(trim($validated['location'])),
            'message' => strip_tags(trim($validated['message'])),
            'contact' => ! empty($validated['contact']) ? strip_tags(trim($validated['contact'])) : null,
            'read_at' => null,
            'created_at' => now(),
        ]);

        // Kirim notifikasi email relay ke pemilik jika relay aktif (FR-LST-03)
        try {
            $relayEnabled = DB::table('settings')->where('key', 'relay_wa_otomatis_aktif')->value('value');
            if ($tag->user && $tag->user->email && ($relayEnabled === null || in_array((string) $relayEnabled, ['1', 'true'], true))) {
                Mail::to($tag->user->email)->send(
                    new LostItemMessageNotification($tag->user, $tag, $lostMessage)
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirimkan email notifikasi relay barang hilang: '.$e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas kejujuran dan bantuan Anda! Pesan penemuan berhasil diteruskan dengan aman ke pemilik barang.',
            ]);
        }

        return back()->with('lost_message_success', 'Terima kasih atas kejujuran dan bantuan Anda! Pesan penemuan berhasil diteruskan dengan aman ke pemilik barang.');
    }

    /**
     * Kirim laporan penyalahgunaan profil publik ke panel admin (FR-PUB-12).
     */
    public function submitAbuseReport(Request $request, string $tag_code): JsonResponse|RedirectResponse
    {
        $tag = Tag::with('user')->where('tag_code', $tag_code)->firstOrFail();

        $validated = $request->validate([
            'sender_name' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:1000'],
            'contact' => ['nullable', 'string', 'max:100'],
        ], [
            'message.required' => 'Keterangan indikasi penyalahgunaan wajib diisi.',
            'message.max' => 'Keterangan indikasi penyalahgunaan maksimal 1000 karakter.',
        ]);

        Message::create([
            'type' => 'abuse_report',
            'user_id' => $tag->user_id,
            'sender_name' => ! empty($validated['sender_name']) ? strip_tags(trim($validated['sender_name'])) : 'Pengunjung Anonim',
            'location' => ! empty($validated['location']) ? strip_tags(trim($validated['location'])) : 'Online / Profil Publik',
            'message' => "[Laporan Profil {$tag->tag_code}] ".strip_tags(trim($validated['message'])),
            'contact' => ! empty($validated['contact']) ? strip_tags(trim($validated['contact'])) : null,
            'read_at' => null,
            'created_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan penyalahgunaan berhasil dikirim ke tim keamanan kami untuk ditinjau.',
            ]);
        }

        return back()->with('abuse_report_success', 'Laporan penyalahgunaan berhasil dikirim ke tim keamanan kami untuk ditinjau.');
    }

    /**
     * Kirim pesan kontak dari landing page ke admin drawer (FR-ADM-09).
     */
    public function submitContact(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'sender_name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:1500'],
            'contact' => ['required', 'string', 'max:100'],
        ], [
            'sender_name.required' => 'Nama lengkap wajib diisi.',
            'contact.required' => 'Nomor WhatsApp atau Email kontak wajib diisi.',
            'message.required' => 'Pesan atau pertanyaan wajib diisi.',
        ]);

        Message::create([
            'type' => 'contact',
            'user_id' => null,
            'sender_name' => strip_tags(trim($validated['sender_name'])),
            'location' => ! empty($validated['location']) ? strip_tags(trim($validated['location'])) : 'Website Landing Page',
            'message' => strip_tags(trim($validated['message'])),
            'contact' => strip_tags(trim($validated['contact'])),
            'read_at' => null,
            'created_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan Anda telah kami terima! Tim FlexiTag akan segera menghubungi Anda.',
            ]);
        }

        return back()->with('contact_success', 'Pesan Anda telah kami terima! Tim FlexiTag akan segera menghubungi Anda.');
    }
}
