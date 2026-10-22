<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicProfileController extends Controller
{
    /**
     * Show public profile accessed via Tag Code (/u/{tag_code}).
     */
    public function showByTag(Request $request, string $tag_code)
    {
        try {
            $tag = Tag::where('tag_code', $tag_code)->first();

            if (!$tag) {
                return response()->view('public.not-found', [
                    'status' => 'not_found',
                    'title' => 'Tag Tidak Ditemukan — FlexiTag',
                    'heading' => 'Tag Belum Terdaftar',
                    'message' => "Kode tag [{$tag_code}] tidak terdaftar dalam sistem MaccaTech FlexiTag.",
                ], 404);
            }

            if ($tag->status === 'belum_aktif') {
                return response()->view('public.not-found', [
                    'status' => 'belum_aktif',
                    'title' => 'Tag Belum Diaktivasi — FlexiTag',
                    'heading' => 'Kartu Belum Diaktivasi',
                    'message' => 'Kartu fisik FlexiTag ini telah diproduksi namun belum ditautkan atau diaktivasi oleh pemilik akun.',
                ], 200);
            }

            if ($tag->status === 'nonaktif') {
                $cause = $tag->disabled_by === 'admin'
                    ? 'Tag ini telah dinonaktifkan sementara oleh Administrator karena alasan keamanan.'
                    : 'Pemilik tag telah mematikan akses publik kartu ini melalui fitur Kill-Switch.';

                return response()->view('public.not-found', [
                    'status' => 'nonaktif',
                    'title' => 'Tag Dinonaktifkan — FlexiTag',
                    'heading' => 'Akses Publik Dinonaktifkan',
                    'message' => $cause,
                ], 403);
            }

            $user = User::find($tag->user_id);
            if (!$user || !$user->is_active) {
                return response()->view('public.not-found', [
                    'status' => 'nonaktif',
                    'title' => 'Akun Tidak Aktif — FlexiTag',
                    'heading' => 'Profil Tidak Tersedia',
                    'message' => 'Akun pemilik tag ini sedang tidak aktif atau ditangguhkan.',
                ], 403);
            }

            // Safe telemetry tap logging
            $this->logTapSafely($tag->id, $request);

            return $this->renderProfileView($user, $tag->tag_code);
        } catch (\Throwable $e) {
            Log::error('PublicProfileController@showByTag error: ' . $e->getMessage());

            return response()->view('public.not-found', [
                'status' => 'error',
                'title' => 'Terjadi Kendala — FlexiTag',
                'heading' => 'Gagal Memuat Profil',
                'message' => 'Layanan sedang mengalami gangguan sementara. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }

    /**
     * Show public profile accessed via Username (/@{username}).
     */
    public function showByUsername(Request $request, string $username)
    {
        try {
            $profile = Profile::where('username', $username)->first();

            if (!$profile) {
                return response()->view('public.not-found', [
                    'status' => 'not_found',
                    'title' => 'Pengguna Tidak Ditemukan — FlexiTag',
                    'heading' => 'Username Tidak Ditemukan',
                    'message' => "Pengguna dengan username @{$username} tidak terdaftar di sistem FlexiTag.",
                ], 404);
            }

            $user = User::find($profile->user_id);
            if (!$user || !$user->is_active) {
                return response()->view('public.not-found', [
                    'status' => 'nonaktif',
                    'title' => 'Akun Tidak Aktif — FlexiTag',
                    'heading' => 'Profil Tidak Tersedia',
                    'message' => 'Akun pengguna ini sedang tidak aktif atau ditangguhkan.',
                ], 403);
            }

            // Optional: get first active tag code of user if any
            $activeTag = Tag::where('user_id', $user->id)
                ->where('status', 'aktif')
                ->first();

            $tagCode = $activeTag ? $activeTag->tag_code : null;

            return $this->renderProfileView($user, $tagCode);
        } catch (\Throwable $e) {
            Log::error('PublicProfileController@showByUsername error: ' . $e->getMessage());

            return response()->view('public.not-found', [
                'status' => 'error',
                'title' => 'Terjadi Kendala — FlexiTag',
                'heading' => 'Gagal Memuat Profil',
                'message' => 'Layanan sedang mengalami kendala sementara. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }

    /**
     * Common method to compile profile data, filter privacy, process active mode, and render Blade view.
     */
    protected function renderProfileView(User $user, ?string $tagCode = null)
    {
        // 1. Fetch Profile
        $profile = Profile::where('user_id', $user->id)->first();
        if (!$profile) {
            $profile = (object) [
                'display_name' => $user->name,
                'headline' => null,
                'bio' => null,
                'photo_path' => null,
                'theme' => 'sulawesi_heritage',
                'active_mode' => 'kampus',
            ];
        }

        $activeMode = $profile->active_mode ?? 'kampus';

        // 2. Fetch Privacy Settings
        $privacy = DB::table('privacy_settings')->where('user_id', $user->id)->first();
        if (!$privacy) {
            $privacy = (object) [
                'show_phone' => false,
                'show_email' => false,
                'show_location' => false,
                'indexable' => false,
            ];
        }

        // 3. Process Mode Data ONLY for the Active Mode
        $rawModeRecord = DB::table('mode_data')
            ->where('user_id', $user->id)
            ->where('mode', $activeMode)
            ->first();

        $modeData = [];
        if ($rawModeRecord && !empty($rawModeRecord->data)) {
            $decoded = json_decode($rawModeRecord->data, true);
            if (is_array($decoded)) {
                $modeData = $this->filterSensitiveModeFields($decoded, $privacy);
            }
        }

        // 4. Fetch Visible Links
        $links = DB::table('links')
            ->where('user_id', $user->id)
            ->where('is_visible', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('public.profile', [
            'user' => $user,
            'profile' => $profile,
            'privacy' => $privacy,
            'activeMode' => $activeMode,
            'modeData' => $modeData,
            'links' => $links,
            'tagCode' => $tagCode,
            'indexable' => (bool) ($privacy->indexable ?? false),
        ]);
    }

    /**
     * Filter sensitive fields from mode_data based on privacy settings before passing to view.
     */
    protected function filterSensitiveModeFields(array $data, object $privacy): array
    {
        if (empty($privacy->show_phone)) {
            unset($data['phone'], $data['telepon'], $data['no_hp'], $data['whatsapp']);
        }

        if (empty($privacy->show_email)) {
            unset($data['email'], $data['surel']);
        }

        if (empty($privacy->show_location)) {
            unset($data['lokasi'], $data['alamat'], $data['location']);
        }

        return $data;
    }

    /**
     * Safely log tap events to the taps table without ever failing the user request.
     */
    protected function logTapSafely(int $tagId, Request $request): void
    {
        try {
            $userAgent = $request->userAgent() ?? '';
            $deviceType = 'desktop';

            if (preg_match('/(android|iphone|ipad|mobile)/i', $userAgent)) {
                $deviceType = 'mobile';
            }

            $source = $request->query('source', 'nfc');
            if (!in_array($source, ['nfc', 'qr', 'link'])) {
                $source = 'nfc';
            }

            DB::table('taps')->insert([
                'tag_id' => $tagId,
                'tapped_at' => now(),
                'source' => $source,
                'device_type' => substr($deviceType, 0, 50),
                'region' => substr($request->header('CF-IPCountry', 'ID'), 0, 100),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently log error, never break public page response
            Log::warning('Failed logging tap: ' . $e->getMessage());
        }
    }

    /**
     * Track link click safely and redirect user to target URL.
     */
    public function trackLinkClick(int $linkId)
    {
        try {
            $link = DB::table('links')->where('id', $linkId)->first();

            if (!$link) {
                return redirect()->route('landing');
            }

            // Safe telemetry link_click logging
            try {
                DB::table('link_clicks')->insert([
                    'link_id' => $link->id,
                    'clicked_at' => now(),
                ]);
            } catch (\Throwable $te) {
                Log::warning('Failed logging link click: ' . $te->getMessage());
            }

            return redirect()->away($link->url);
        } catch (\Throwable $e) {
            Log::error('Error in trackLinkClick: ' . $e->getMessage());
            return redirect()->route('landing');
        }
    }

    /**
     * Store finder message for lost item mode with server-side validation and rate limiting.
     */
    public function storeLostMessage(Request $request, string $tag_code)
    {
        $validated = $request->validate([
            'sender_name' => 'required|string|max:100',
            'location' => 'required|string|max:150',
            'contact' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $tag = Tag::where('tag_code', $tag_code)->firstOrFail();

            DB::table('messages')->insert([
                'type' => 'lost_item',
                'user_id' => $tag->user_id,
                'sender_name' => $validated['sender_name'],
                'location' => $validated['location'],
                'contact' => $validated['contact'],
                'message' => $validated['message'],
                'read_at' => null,
                'created_at' => now(),
            ]);

            return redirect()
                ->route('public.tag', $tag_code)
                ->with('lost_success', 'Pesan Anda berhasil dikirim ke pemilik barang. Terima kasih atas bantuan dan kejujuran Anda!');
        } catch (\Throwable $e) {
            Log::error('storeLostMessage error: ' . $e->getMessage());

            return redirect()
                ->route('public.tag', $tag_code)
                ->withErrors(['lost_rate_limit' => 'Terjadi kesalahan sistem saat mengirim pesan. Silakan coba kembali sesaat lagi.']);
        }
    }
}
