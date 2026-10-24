<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('user.auth.login');
    }

    public function showRegister(): View
    {
        return view('user.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.letters' => 'Kata sandi harus mengandung huruf.',
            'password.numbers' => 'Kata sandi harus mengandung angka.',
        ]);

        // Role dan status SELALU ditetapkan di server, bukan dari input form.
        // Password di-hash otomatis oleh cast 'hashed' pada model User.
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
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.activation.show', ['step' => 2])
            ->with('status', 'Akun berhasil dibuat. Silakan masukkan kode aktivasi untuk mengaktifkan gantungan kunci Anda.');
    }

    public function login(Request $request): RedirectResponse
    {
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

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Pesan khusus hanya diberikan bila kata sandi benar, supaya
        // keberadaan akun tidak bisa ditebak lewat form ini.
        $candidate = User::where('email', $credentials['email'])->first();

        if ($candidate && $candidate->password && Hash::check($credentials['password'], $candidate->password)) {
            if (! $candidate->is_active) {
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin FlexiTag.',
                ]);
            }
        }

        $attempt = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ];

        if (! Auth::attempt($attempt, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi tidak sesuai.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = Auth::user();

        // Jika akun belum memiliki tag aktif, arahkan ke aktivasi
        if ($user && $user->role === 'user' && ! $user->tags()->where('status', 'aktif')->exists()) {
            return redirect()->route('user.activation.show', ['step' => 2])
                ->with('warning', 'Akun Anda belum memiliki gantungan kunci yang diaktivasi. Silakan masukkan kode aktivasi terlebih dahulu.');
        }

        return redirect()->intended(route('user.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Anda telah keluar.');
    }
}
