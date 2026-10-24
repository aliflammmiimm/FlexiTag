<?php

use App\Http\Middleware\EnsureTagIsActivated;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserRole::class,
            'tag.activated' => EnsureTagIsActivated::class,
        ]);

        // Tamu yang membuka area Admin diarahkan ke login Admin;
        // selain itu ke login User.
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('admin', 'admin/*')
            ? route('admin.login')
            : route('login'));

        // Pengguna yang sudah login dan membuka halaman khusus tamu
        // (login/daftar) diarahkan sesuai role dan status aktivasi tag.
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            $role = $user?->role;

            if (in_array($role, ['admin', 'staff'], true)) {
                return route('admin.dashboard');
            }

            if ($user && ! $user->tags()->where('status', 'aktif')->exists()) {
                return route('user.activation.show', ['step' => 2]);
            }

            return route('user.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
