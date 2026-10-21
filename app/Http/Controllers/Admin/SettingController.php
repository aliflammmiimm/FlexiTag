<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the System & Admin Settings page.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Get or initialize system settings
        $settings = DB::table('system_settings')->first();
        if (! $settings) {
            $id = DB::table('system_settings')->insertGetId([
                'relay_wa_otomatis_aktif' => true,
                'notifikasi_email_pesanan' => true,
                'mode_maintenance' => false,
                'no_wa_cs_pusat' => '+62 812-3456-7890',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $settings = DB::table('system_settings')->find($id);
        }

        return view('admin.settings', compact('user', 'settings'));
    }

    /**
     * Update Admin credentials and platform relay configurations.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'jabatan_admin' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'no_wa_cs_pusat' => ['required', 'string', 'max:30'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $user->update([
                'name' => $validated['name'],
                'jabatan_admin' => $validated['jabatan_admin'],
                'email' => $validated['email'],
            ]);
        }

        DB::table('system_settings')->updateOrInsert(
            ['id' => 1],
            [
                'no_wa_cs_pusat' => $validated['no_wa_cs_pusat'],
                'relay_wa_otomatis_aktif' => $request->boolean('relay_wa_otomatis_aktif'),
                'notifikasi_email_pesanan' => $request->boolean('notifikasi_email_pesanan'),
                'mode_maintenance' => $request->boolean('mode_maintenance'),
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'Semua pengaturan sistem dan kredensial admin berhasil disimpan & diverifikasi.');
    }

    /**
     * Update Administrator password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return back()->with('success', 'Kata sandi administrator berhasil diperbarui.');
    }
}
