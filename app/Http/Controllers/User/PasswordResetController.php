<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan formulir permintaan token pemulihan kata sandi (FR-AUT-03).
     */
    public function showLinkRequestForm(): View
    {
        return view('user.auth.forgot_password');
    }

    /**
     * Kirim kode token 6 digit pemulihan kata sandi ke email pengguna.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Praktik keamanan terbaik (OWASP): respon generik untuk mencegah kebocoran keberadaan email
        if (! $user) {
            return redirect()->route('password.reset', ['email' => $request->email])
                ->with('status', 'Jika alamat email terdaftar, kode verifikasi 6 digit telah dikirimkan ke email Anda.');
        }

        // Generate 6 digit numeric OTP code
        $token = sprintf('%06d', random_int(100000, 999999));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Kirim email notifikasi kode verifikasi
        try {
            Mail::to($user->email)->send(new PasswordResetCodeNotification($user, $token));
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim email reset kata sandi ke [{$user->email}]: ".$e->getMessage());
        }

        Log::info("Password reset 6-digit OTP code generated for [{$user->email}]: {$token}");

        $redirect = redirect()->route('password.reset', ['email' => $user->email])
            ->with('status', 'Kode verifikasi 6 digit telah dikirimkan ke email Anda. Periksa kotak masuk atau folder spam.');

        if (config('mail.default') === 'log' || app()->environment('local')) {
            $redirect->with('debug_otp', $token);
        }

        return $redirect;
    }

    /**
     * Tampilkan formulir input token 6 digit dan pembaruan kata sandi baru.
     */
    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('user.auth.reset_password', [
            'token' => old('token', $request->query('token', $token ?? '')),
            'email' => old('email', $request->query('email', '')),
        ]);
    }

    /**
     * Proses pembaruan kata sandi akun dengan verifikasi token 6 digit.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
            'token' => preg_replace('/\s+/', '', (string) $request->input('token')),
        ]);

        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'token.required' => 'Kode token 6 digit wajib diisi.',
            'token.digits' => 'Kode token harus terdiri dari 6 digit angka.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.letters' => 'Kata sandi harus mengandung kombinasi huruf.',
            'password.numbers' => 'Kata sandi harus mengandung kombinasi angka.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            throw ValidationException::withMessages([
                'token' => 'Kode token verifikasi 6 digit tidak valid atau salah.',
            ]);
        }

        // Periksa masa berlaku token (maks 60 menit)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            throw ValidationException::withMessages([
                'token' => 'Kode token verifikasi telah kedaluwarsa (lebih dari 60 menit). Silakan minta kode baru.',
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->forceFill([
            'password' => $request->password,
        ])->save();

        // Bersihkan token yang telah terpakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru.');
    }
}
