<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Tampilkan halaman Pengaturan untuk akun pengguna (User Settings).
     * Data difilter ketat berdasarkan akun user yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags']);

        // Data hardware/tags milik user yang sedang login
        $tags = $user->tags()->orderByDesc('activated_at')->get();
        $primaryTag = $tags->where('status', 'aktif')->first() ?? $tags->first();
        $hardwareCount = $tags->count();

        // Data privasi akun dari tabel privacy_settings
        $privacy = DB::table('privacy_settings')->where('user_id', $user->id)->first();

        // Inisial avatar (prioritaskan nama asli user agar tidak tertukar nama toko UMKM)
        $displayName = $user->name;
        if (empty($displayName)) {
            $displayName = $user->profile?->display_name ?? 'Pengguna FlexiTag';
        }

        $umkmRecord = DB::table('mode_data')->where('user_id', $user->id)->where('mode', 'umkm')->first();
        if ($umkmRecord && $umkmRecord->data) {
            $umkmData = json_decode($umkmRecord->data, true);
            if (! empty($umkmData['nama_usaha']) && $user->profile?->display_name === $umkmData['nama_usaha'] && ! empty($user->name)) {
                DB::table('profiles')->where('user_id', $user->id)->update(['display_name' => $user->name]);
                $displayName = $user->name;
            }
        }

        $words = preg_split('/\s+/', trim($displayName));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= Str::upper(mb_substr($w, 0, 1));
        }
        $initials = $initials ?: 'FT';

        // Kontak Bantuan resmi dari konfigurasi
        $contactConfig = config('flexitag.contact', []);
        $waNumber = $contactConfig['whatsapp'] ?? '6281234567890';
        $cleanWa = preg_replace('/[^0-9]/', '', (string) $waNumber);
        $supportWaUrl = 'https://wa.me/'.$cleanWa.'?text='.urlencode('Halo Tim Support FlexiTag, saya butuh bantuan terkait akun saya.');

        return view('user.settings', [
            'user' => $user,
            'profile' => $user->profile,
            'tags' => $tags,
            'primaryTag' => $primaryTag,
            'hardwareCount' => $hardwareCount,
            'privacy' => $privacy,
            'displayName' => $displayName,
            'initials' => $initials,
            'supportWaUrl' => $supportWaUrl,
        ]);
    }

    /**
     * Update kata sandi pengguna secara aman.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('user.settings')
            ->with('status', 'Kata sandi berhasil diperbarui dengan aman.');
    }

    /**
     * Update pengaturan privasi tag milik pengguna login.
     */
    public function updatePrivacy(Request $request): RedirectResponse
    {
        $user = $request->user();

        $showPhone = $request->has('show_phone');
        $showEmail = $request->has('show_email');
        $showLocation = $request->has('show_location');
        $indexable = $request->has('indexable');

        DB::table('privacy_settings')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'show_phone' => $showPhone,
                'show_email' => $showEmail,
                'show_location' => $showLocation,
                'indexable' => $indexable,
                'updated_at' => now(),
            ]
        );

        return redirect()->route('user.settings')
            ->with('status', 'Pengaturan privasi tag berhasil diperbarui.');
    }

    /**
     * Kirim saran, masukan, kritik, atau laporan kendala dari user ke Notifikasi Admin.
     */
    public function submitFeedback(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:saran,kendala,pertanyaan'],
            'message' => ['required', 'string', 'min:5', 'max:2000'],
            'contact' => ['nullable', 'string', 'max:100'],
        ], [
            'category.required' => 'Kategori pesan wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'message.required' => 'Pesan, saran, atau laporan wajib diisi.',
            'message.min' => 'Pesan minimal 5 karakter.',
            'message.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        $user = $request->user();

        Message::recordUserFeedback(
            user: $user,
            category: $validated['category'],
            content: strip_tags(trim($validated['message'])),
            contact: ! empty($validated['contact']) ? strip_tags(trim($validated['contact'])) : null
        );

        return redirect()->route('user.settings')
            ->with('status', 'Terima kasih! Saran atau laporan Anda telah berhasil dikirim ke notifikasi administrator FlexiTag.');
    }
}
