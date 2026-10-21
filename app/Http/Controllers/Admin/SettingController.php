<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Design;
use App\Models\FinancialEntry;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the System & Admin Settings page with multi-tab support (FR-ADM-07 & FR-ADM-08).
     */
    public function index(Request $request): View
    {
        $currentTab = $request->query('tab', 'general');
        $user = Auth::user();

        // 1. General & Platform Settings
        $settingsList = DB::table('settings')->pluck('value', 'key');
        $settings = (object) [
            'no_wa_cs_pusat' => $settingsList['no_wa_cs_pusat'] ?? '+62 812-3456-7890',
            'relay_wa_otomatis_aktif' => filter_var($settingsList['relay_wa_otomatis_aktif'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'notifikasi_email_pesanan' => filter_var($settingsList['notifikasi_email_pesanan'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mode_maintenance' => filter_var($settingsList['mode_maintenance'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

        // 2. User Management Sub-Module
        $searchUser = $request->query('search_user', '');
        $roleFilter = $request->query('role_filter', 'all');

        $usersQuery = User::with(['profile'])->withCount('tags')->latest('id');

        if (! empty($searchUser)) {
            $usersQuery->where(function ($q) use ($searchUser) {
                $q->where('name', 'like', "%{$searchUser}%")
                    ->orWhere('email', 'like', "%{$searchUser}%")
                    ->orWhere('no_whatsapp', 'like', "%{$searchUser}%");
            });
        }

        if ($roleFilter !== 'all') {
            $usersQuery->where('role', $roleFilter);
        }

        $users = $usersQuery->paginate(8, ['*'], 'users_page')->withQueryString();

        // 3. Motif Design Gallery Catalog Sub-Module
        $designs = Design::latest('id')->get();
        $designCategories = Design::categories();

        // 4. Financial Entries & Simple Ledger Sub-Module
        $totalPemasukan = (float) FinancialEntry::where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = (float) FinancialEntry::where('type', 'pengeluaran')->sum('amount');
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        $financialEntries = FinancialEntry::latest('entry_date')
            ->latest('id')
            ->paginate(10, ['*'], 'finance_page')
            ->withQueryString();

        return view('admin.settings', compact(
            'user',
            'settings',
            'currentTab',
            'users',
            'searchUser',
            'roleFilter',
            'designs',
            'designCategories',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoKas',
            'financialEntries'
        ));
    }

    /**
     * Update Admin credentials and platform relay configurations (FR-ADM-07).
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

        AuditLog::record('update_system_settings', 'Pengaturan sistem & kredensial admin diperbarui');

        return redirect()->route('admin.settings', ['tab' => 'general'])->with('success', 'Semua pengaturan sistem dan kredensial admin berhasil disimpan & diverifikasi.');
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

        return redirect()->route('admin.settings', ['tab' => 'general'])->with('success', 'Kata sandi administrator berhasil diperbarui.');
    }

    // =========================================================================
    // SUB-MODUL 1: MANAJEMEN PENGGUNA (FR-ADM-08)
    // =========================================================================

    /**
     * Store a new user / staff / admin account.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:user,staff,admin'],
            'jabatan_admin' => ['nullable', 'string', 'max:100'],
            'no_whatsapp' => ['nullable', 'string', 'max:25'],
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'jabatan_admin' => $validated['jabatan_admin'] ?? ($validated['role'] === 'admin' ? 'Administrator' : 'Pengguna Standar'),
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Auto-create basic profile (username max 20 chars per profiles table schema)
        $baseSlug = substr(Str::slug($newUser->name), 0, 14);
        $username = substr(rtrim($baseSlug, '-').'-'.rand(1000, 9999), 0, 20);

        Profile::create([
            'user_id' => $newUser->id,
            'display_name' => $newUser->name,
            'username' => $username,
            'theme' => 'sulawesi_heritage',
            'active_mode' => 'kampus',
        ]);

        AuditLog::record('create_user', "Pengguna baru [{$newUser->name} ({$newUser->role})] dibuat oleh admin");

        return redirect()->route('admin.settings', ['tab' => 'users'])->with('success', "Akun pengguna '{$newUser->name}' berhasil didaftarkan.");
    }

    /**
     * Update an existing user's data and credentials.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:user,staff,admin'],
            'jabatan_admin' => ['nullable', 'string', 'max:100'],
            'no_whatsapp' => ['nullable', 'string', 'max:25'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'jabatan_admin' => $validated['jabatan_admin'],
            'no_whatsapp' => $validated['no_whatsapp'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        AuditLog::record('update_user', "Data pengguna [{$user->name}] diperbarui oleh admin");

        return redirect()->route('admin.settings', ['tab' => 'users'])->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Toggle active/block status for a user.
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.settings', ['tab' => 'users'])->with('error', 'Anda tidak dapat menonaktifkan akun sendiri!');
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $statusText = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan / diblokir';
        AuditLog::record('toggle_user_status', "Akun [{$user->name}] {$statusText} oleh admin");

        return redirect()->route('admin.settings', ['tab' => 'users'])->with('success', "Status akun '{$user->name}' berhasil diubah ({$statusText}).");
    }

    /**
     * Delete user and gracefully unlink tags.
     */
    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.settings', ['tab' => 'users'])->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $userName = $user->name;

        // Gracefully unlink associated tags back to available stock
        $user->tags()->update([
            'user_id' => null,
            'status' => 'belum_aktif',
            'disabled_by' => null,
            'activated_at' => null,
        ]);

        $user->profile()->delete();
        $user->delete();

        AuditLog::record('delete_user', "Pengguna [{$userName}] dihapus dari sistem oleh admin");

        return redirect()->route('admin.settings', ['tab' => 'users'])->with('success', "Akun pengguna '{$userName}' berhasil dihapus dan tag terkait dikembalikan ke inventaris.");
    }

    // =========================================================================
    // SUB-MODUL 2: KATALOG TEMPLATE MOTIF (FR-ADM-08)
    // =========================================================================

    /**
     * Store a new motif design template.
     */
    public function storeDesign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:pribadi,organisasi,umkm,motif_lokal'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5120'],
        ]);

        $imagePath = 'designs/default_motif.png';
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('designs', 'public');
        }

        $design = Design::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        AuditLog::record('create_design', "Motif baru [{$design->name}] ditambahkan ke katalog ({$design->category})");

        return redirect()->route('admin.settings', ['tab' => 'designs'])->with('success', "Motif desain '{$design->name}' berhasil ditambahkan ke galeri.");
    }

    /**
     * Toggle active/inactive state of a design motif.
     */
    public function toggleDesignStatus(Design $design): RedirectResponse
    {
        $design->update([
            'is_active' => ! $design->is_active,
        ]);

        $statusText = $design->is_active ? 'diaktifkan' : 'dinonaktifkan';
        AuditLog::record('toggle_design_status', "Motif [{$design->name}] {$statusText}");

        return redirect()->route('admin.settings', ['tab' => 'designs'])->with('success', "Status motif '{$design->name}' berhasil diubah ke {$statusText}.");
    }

    /**
     * Delete a design template from the catalog.
     */
    public function deleteDesign(Design $design): RedirectResponse
    {
        $name = $design->name;
        $design->delete();

        AuditLog::record('delete_design', "Motif [{$name}] dihapus dari katalog desain");

        return redirect()->route('admin.settings', ['tab' => 'designs'])->with('success', "Motif '{$name}' berhasil dihapus dari katalog.");
    }

    // =========================================================================
    // SUB-MODUL 3: PEMBUKUAN KAS SEDERHANA (FR-ADM-08)
    // =========================================================================

    /**
     * Record a new financial cash flow entry.
     */
    public function storeFinancial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:pemasukan,pengeluaran'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'category' => ['required', 'string', 'max:100'],
            'entry_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $entry = FinancialEntry::create($validated);

        $formattedAmount = 'Rp '.number_format((float) $entry->amount, 0, ',', '.');
        AuditLog::record('record_financial', "Transaksi {$entry->type} sebesar {$formattedAmount} dicatat: {$entry->category}");

        return redirect()->route('admin.settings', ['tab' => 'financial'])->with('success', "Entri transaksi {$entry->type} ({$formattedAmount}) berhasil dicatat.");
    }

    /**
     * Delete a financial entry from the ledger.
     */
    public function deleteFinancial(FinancialEntry $entry): RedirectResponse
    {
        $desc = "{$entry->type} Rp ".number_format((float) $entry->amount, 0, ',', '.');
        $entry->delete();

        AuditLog::record('delete_financial', "Entri pembukuan [{$desc}] dihapus");

        return redirect()->route('admin.settings', ['tab' => 'financial'])->with('success', "Entri transaksi '{$desc}' berhasil dihapus.");
    }
}
