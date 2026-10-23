<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Tag;
use App\Models\Tap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function landing(): View
    {
        return view('landing');
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
            ]);
        }

        // Catat analitik tap (FR-PUB-04)
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
        $activeMode = $request->query('mode', $profile?->active_mode ?? 'kampus');
        $modeData = $user?->modeData?->firstWhere('mode', $activeMode)?->data ?? [];

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

        if ($cvPath && file_exists(public_path($cvPath))) {
            return response()->download(public_path($cvPath));
        }

        $name = $profile?->display_name ?? $user?->name ?? 'Curriculum Vitae';
        $headline = $modeData['headline'] ?? $profile?->headline ?? 'Software Engineer & Embedded IoT Developer';
        $phone = $modeData['telepon_kerja'] ?? $user?->no_whatsapp ?? '';
        $email = $modeData['email_kerja'] ?? $user?->email ?? '';

        $resumeContent = "========================================================\n"
            ."  CURRICULUM VITAE - ".strtoupper($name)."\n"
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
}
