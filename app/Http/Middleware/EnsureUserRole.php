<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses berdasarkan role (mis. role:admin,staff atau role:user).
 * Juga menolak akun yang sudah dinonaktifkan (is_active = false).
 */
class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $isAdminArea = $request->is('admin', 'admin/*');
        $loginRoute = $isAdminArea ? 'admin.login' : 'login';

        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route($loginRoute));
        }

        // Akun nonaktif / diblokir: akhiri sesi dan kembali ke halaman login.
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route($loginRoute)
                ->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin FlexiTag.']);
        }

        if (! in_array($user->role, $roles, true)) {
            // Jika akun admin/staff mengakses area user, izinkan akses (karena admin juga bisa memiliki tag pribadi).
            if (! $isAdminArea && in_array($user->role, ['admin', 'staff'], true)) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
