<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTagIsActivated
{
    /**
     * Memastikan pengguna memiliki setidaknya satu tag NFC aktif sebelum
     * dapat mengakses dashboard pengguna dan pengelolaannya.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Pengguna wajib memiliki setidaknya satu tag yang pernah diaktivasi (status aktif atau nonaktif)
        $hasActiveTag = $user->tags()
            ->whereIn('status', ['aktif', 'nonaktif'])
            ->exists();

        if (! $hasActiveTag) {
            // Akun admin dan staff yang belum memiliki tag aktif diarahkan ke Dashboard Admin
            if (in_array($user->role, ['admin', 'staff'], true)) {
                return redirect()->route('admin.dashboard')
                    ->with('info', 'Anda dialihkan ke Dashboard Admin karena akun Anda adalah pengelola.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Gantungan kunci FlexiTag Anda belum diaktivasi. Silakan masukkan kode aktivasi.',
                    'redirect' => route('user.activation.show', ['step' => 2]),
                ], 403);
            }

            return redirect()
                ->route('user.activation.show', ['step' => 2])
                ->with('warning', 'Gantungan kunci FlexiTag Anda belum diaktivasi. Silakan masukkan kode aktivasi terlebih dahulu untuk mengakses dashboard.');
        }

        return $next($request);
    }
}
