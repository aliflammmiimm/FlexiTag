<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagActivationController extends Controller
{
    /** Batas percobaan gagal per akun: 5 kali dalam 10 menit. */
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 600;

    /**
     * Tampilkan wizard aktivasi tag 4 langkah.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Jika pengunjung belum login (guest), langkah hanya boleh 1 (Akun).
        if (! $user) {
            $step = 1;
            if ($request->has('step') && (int) $request->query('step') !== 1) {
                return redirect()->route('user.activation.show', ['step' => 1]);
            }

            return view('user.activation.show', [
                'step' => 1,
                'user' => null,
                'profile' => null,
                'tag' => null,
                'activeTagCount' => 0,
                'currentTag' => null,
            ]);
        }

        // Pengguna sudah login: tentukan langkah
        $activeTags = $user->tags()->where('status', 'aktif')->orderByDesc('activated_at')->get();
        $activeTagCount = $activeTags->count();

        // Cari tag yang sedang aktif dalam alur aktivasi ini
        $tag = null;
        if (session('activation_tag_id')) {
            $tag = $activeTags->firstWhere('id', session('activation_tag_id'));
        }
        if (! $tag && $activeTagCount > 0) {
            $tag = $activeTags->first();
        }

        if ($request->has('step')) {
            $step = (int) $request->query('step');
            $step = max(1, min(4, $step));

            // Jika minta langkah 3 atau 4 tetapi belum punya tag aktif, arahkan ke langkah 2
            if ($step >= 3 && ! $tag) {
                return redirect()->route('user.activation.show', ['step' => 2])
                    ->with('info', 'Silakan masukkan kode aktivasi gantungan kunci Anda terlebih dahulu.');
            }
        } else {
            // Default langkah saat sudah login:
            if (session('activation_step')) {
                $step = (int) session('activation_step');
            } elseif ($tag && session('activated')) {
                $step = 4;
            } elseif ($tag && session('activation_toast')) {
                $step = 3;
            } else {
                $step = 2; // Langsung ke langkah input kode aktivasi
            }
        }

        $user->load('profile');

        return view('user.activation.show', [
            'step' => $step,
            'user' => $user,
            'profile' => $user->profile,
            'tag' => $tag,
            'activeTagCount' => $activeTagCount,
            'currentTag' => $tag,
        ]);
    }

    /**
     * Langkah 1: Autentikasi langsung dari dalam wizard (Daftar Akun Baru atau Masuk).
     */
    public function authStep(Request $request): RedirectResponse
    {
        $action = $request->input('auth_action', 'register');

        if ($action === 'login') {
            $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

            $credentials = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Kata sandi wajib diisi.',
            ]);

            $throttleKey = Str::transliterate($credentials['email'].'|'.$request->ip());

            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                $seconds = RateLimiter::availableIn($throttleKey);

                return redirect()->route('user.activation.show', ['step' => 1, 'tab' => 'login'])
                    ->withErrors(['email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik."])
                    ->withInput();
            }

            if (! Auth::attempt($credentials, true)) {
                RateLimiter::hit($throttleKey, 300);

                return redirect()->route('user.activation.show', ['step' => 1, 'tab' => 'login'])
                    ->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])
                    ->withInput();
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            session(['activation_step' => 2]);

            return redirect()->route('user.activation.show', ['step' => 2])
                ->with('status', 'Berhasil masuk! Silakan masukkan kode aktivasi gantungan kunci Anda.');
        }

        // Registrasi akun baru
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'agree_terms' => ['required', 'accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan pilih tab Masuk Akun.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'agree_terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan serta Kebijakan Privasi.',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = new User;
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'user',
                'is_active' => true,
            ])->save();

            Profile::create([
                'user_id' => $user->id,
                'display_name' => $data['name'],
                'theme' => 'sulawesi_heritage',
                'active_mode' => 'kampus',
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();
        session(['activation_step' => 2]);

        return redirect()->route('user.activation.show', ['step' => 2])
            ->with('status', 'Akun berhasil dibuat! Silakan masukkan kode aktivasi.');
    }

    /**
     * Langkah 2: Verifikasi kode aktivasi dan klaim tag.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('user.activation.show', ['step' => 1])
                ->withErrors(['auth' => 'Silakan masuk atau buat akun terlebih dahulu.']);
        }

        $key = 'tag-activation:'.$user->id;

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            return $this->fail('throttled', [
                'minutes' => (int) ceil(RateLimiter::availableIn($key) / 60),
            ]);
        }

        $request->validate([
            'activation_code' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9\-\s]+$/'],
        ], [
            'activation_code.required' => 'Kode aktivasi wajib diisi.',
            'activation_code.max' => 'Kode aktivasi terlalu panjang.',
            'activation_code.regex' => 'Kode hanya boleh berisi huruf, angka, dan tanda hubung (-).',
        ]);

        $rawCode = strtoupper(trim((string) $request->input('activation_code')));
        $cleanCode = preg_replace('/[^A-Z0-9]/', '', $rawCode);

        $result = DB::transaction(function () use ($rawCode, $cleanCode, $user) {
            $tag = Tag::where(function ($query) use ($rawCode, $cleanCode) {
                $query->where('activation_code', $rawCode)
                    ->orWhere('tag_code', $rawCode)
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(activation_code, '-', ''), ' ', '')) = ?", [$cleanCode])
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(tag_code, '-', ''), ' ', '')) = ?", [$cleanCode]);
            })->lockForUpdate()->first();

            if (! $tag) {
                return ['wrong', null];
            }

            if ($tag->status === 'nonaktif') {
                return ['disabled', null];
            }

            if ($tag->status === 'aktif') {
                return [(int) $tag->user_id === (int) $user->id ? 'own' : 'used', null];
            }

            if ($tag->status !== 'belum_aktif' || $tag->user_id !== null) {
                return ['used', null];
            }

            $tag->user_id = $user->id;
            $tag->status = 'aktif';
            $tag->mode = $user->profile?->active_mode ?? 'kampus';
            $tag->activated_at = now();
            $tag->save();

            return ['ok', $tag];
        });

        [$state, $tag] = $result;

        if ($state !== 'ok') {
            return $this->fail($state);
        }

        RateLimiter::clear($key);
        Log::info('Tag diaktifkan', ['user_id' => $user->id, 'tag_id' => $tag->id]);

        $hasExistingProfile = $user->profile && ! empty($user->profile->display_name);

        if ($hasExistingProfile) {
            session([
                'activation_tag_id' => $tag->id,
                'activation_tag_code' => $tag->tag_code,
                'activation_step' => 4,
                'activated' => true,
            ]);

            return redirect()->route('user.activation.show', ['step' => 4])
                ->with('status', "Tag baru ({$tag->tag_code}) berhasil ditautkan ke profil aktif Anda!");
        }

        session([
            'activation_tag_id' => $tag->id,
            'activation_tag_code' => $tag->tag_code,
            'activation_step' => 3,
        ]);

        return redirect()->route('user.activation.show', ['step' => 3])
            ->with('activation_toast', 'Kode Terverifikasi! Menghubungkan ke profil digital Anda...');
    }

    /**
     * Langkah 3: Simpan konfigurasi awal profil pengguna (Foto, Nama, Headline, Mode, Kontak).
     */
    public function profileStep(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('user.activation.show', ['step' => 1]);
        }

        $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'headline' => ['nullable', 'string', 'max:150'],
            'active_mode' => ['required', 'in:kampus,kerja,umkm,hilang'],
            'no_whatsapp' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'display_name.required' => 'Nama tampilan wajib diisi.',
            'display_name.max' => 'Nama tampilan maksimal 100 karakter.',
            'headline.max' => 'Headline maksimal 150 karakter.',
            'active_mode.required' => 'Pilih salah satu mode awal aktif.',
            'active_mode.in' => 'Mode pilihan tidak valid.',
            'photo.image' => 'Berkas foto profil harus berupa gambar.',
            'photo.max' => 'Ukuran foto profil maksimal 2MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
        }

        $profileData = [
            'display_name' => $request->display_name,
            'headline' => $request->headline,
            'active_mode' => $request->active_mode,
            'theme' => 'sulawesi_heritage',
        ];

        if ($photoPath) {
            $profileData['photo_path'] = $photoPath;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // Sinkronkan mode awal ke tag yang baru saja diaktifkan dalam alur onboarding ini
        if (session('activation_tag_id')) {
            Tag::where('id', session('activation_tag_id'))->where('user_id', $user->id)->update([
                'mode' => $request->active_mode,
            ]);
        }

        // Update kontak WhatsApp
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

        session(['activation_step' => 4]);

        return redirect()->route('user.activation.show', ['step' => 4])
            ->with('status', 'Profil awal berhasil disimpan! FlexiTag Anda siap digunakan.');
    }

    /**
     * Kegagalan verifikasi kode aktivasi.
     */
    private function fail(string $state, array $extra = []): RedirectResponse
    {
        if ($state !== 'throttled') {
            RateLimiter::hit('tag-activation:'.request()->user()->id, self::DECAY_SECONDS);
        }

        return redirect()->route('user.activation.show', ['step' => 2])
            ->with('activation_state', ['state' => $state] + $extra);
    }
}
