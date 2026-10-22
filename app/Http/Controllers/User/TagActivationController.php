<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class TagActivationController extends Controller
{
    /** Batas percobaan gagal per akun: 5 kali dalam 10 menit. */
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 600;

    public function show(Request $request): View
    {
        $user = $request->user();

        return view('user.activation.show', [
            'activeTagCount' => $user->tags()->where('status', 'aktif')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
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

        // Normalisasi: hapus spasi, huruf besar. user_id TIDAK pernah diambil dari input.
        $code = strtoupper(preg_replace('/\s+/', '', (string) $request->input('activation_code')));

        // Seluruh proses dalam satu transaksi dengan row lock, sehingga dua akun
        // yang mengirim kode yang sama bersamaan akan diproses berurutan.
        $result = DB::transaction(function () use ($code, $user) {
            $tag = Tag::where('activation_code', $code)->lockForUpdate()->first();

            if (! $tag) {
                return ['wrong', null];
            }

            if ($tag->status === 'nonaktif') {
                return ['disabled', null];
            }

            if ($tag->status === 'aktif') {
                return [(int) $tag->user_id === (int) $user->id ? 'own' : 'used', null];
            }

            // Hanya status 'belum_aktif' yang boleh lanjut; pastikan juga belum punya pemilik.
            if ($tag->status !== 'belum_aktif' || $tag->user_id !== null) {
                return ['used', null];
            }

            $tag->user_id = $user->id;
            $tag->status = 'aktif';
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

        // Post/Redirect/Get: refresh halaman tidak mengirim ulang form.
        return redirect()->route('user.activation.show')->with('activated', [
            'tag_code' => $tag->tag_code,
            'activated_at' => $tag->activated_at->timezone(config('app.timezone'))->format('d M Y, H:i'),
        ]);
    }

    /**
     * Gagal: catat percobaan, kembalikan status agar view menampilkan pesan yang sesuai.
     * Kode aktivasi tidak pernah dikembalikan ke input/old value untuk status selain 'throttled'.
     */
    private function fail(string $state, array $extra = []): RedirectResponse
    {
        if ($state !== 'throttled') {
            RateLimiter::hit('tag-activation:'.request()->user()->id, self::DECAY_SECONDS);
        }

        return redirect()->route('user.activation.show')
            ->with('activation_state', ['state' => $state] + $extra);
    }
}
