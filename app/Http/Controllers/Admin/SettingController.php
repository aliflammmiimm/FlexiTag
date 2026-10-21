<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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

        // Get system settings from key-value settings table
        $settingsList = DB::table('settings')->pluck('value', 'key');

        $settings = (object) [
            'no_wa_cs_pusat' => $settingsList['no_wa_cs_pusat'] ?? '+62 812-3456-7890',
            'relay_wa_otomatis_aktif' => filter_var($settingsList['relay_wa_otomatis_aktif'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'notifikasi_email_pesanan' => filter_var($settingsList['notifikasi_email_pesanan'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mode_maintenance' => filter_var($settingsList['mode_maintenance'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

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
            'no_wa_cs_pusat' => ['required', 'string', 'max:30', 'regex:/^(\+628|628)[0-9]{8,13}$/'],
        ], [
            'no_wa_cs_pusat.regex' => 'Nomor WhatsApp CS harus diawali kode negara (+628... atau 628...).',
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

        $items = [
            'no_wa_cs_pusat' => $validated['no_wa_cs_pusat'],
            'relay_wa_otomatis_aktif' => $request->boolean('relay_wa_otomatis_aktif') ? '1' : '0',
            'notifikasi_email_pesanan' => $request->boolean('notifikasi_email_pesanan') ? '1' : '0',
            'mode_maintenance' => $request->boolean('mode_maintenance') ? '1' : '0',
        ];

        foreach ($items as $k => $v) {
            DB::table('settings')->updateOrInsert(['key' => $k], ['value' => $v, 'updated_at' => now()]);
        }

        AuditLog::record('update_system_settings', 'Pengaturan sistem & nomor WhatsApp CS diperbarui oleh admin');

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

            AuditLog::record('update_admin_password', "Kata sandi administrator [{$user->email}] diperbarui");
        }

        return back()->with('success', 'Kata sandi administrator berhasil diperbarui.');
    }
}
