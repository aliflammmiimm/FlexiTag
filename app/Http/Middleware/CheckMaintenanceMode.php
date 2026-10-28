<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Selalu izinkan endpoint kesehatan, boost debugger, dan area konsol admin
        if ($request->is('up', '_boost/*', 'admin', 'admin/*')) {
            return $next($request);
        }

        // 2. Periksa status mode maintenance dari tabel settings
        try {
            $isMaintenance = DB::table('settings')
                ->where('key', 'mode_maintenance')
                ->value('value');

            $inMaintenance = in_array((string) $isMaintenance, ['1', 'true'], true);
        } catch (\Throwable $e) {
            $inMaintenance = false;
        }

        if ($inMaintenance) {
            // Pengelola (Admin/Staff) yang sedang login tetap diizinkan mengakses seluruh situs
            if (Auth::check() && in_array(Auth::user()->role, ['admin', 'staff'], true)) {
                return $next($request);
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Sistem sedang dalam pemeliharaan berkala.',
                    'maintenance' => true,
                ], 503, ['Retry-After' => 300]);
            }

            return response()->view('errors.503', [], 503, [
                'Retry-After' => 300,
            ]);
        }

        return $next($request);
    }
}
