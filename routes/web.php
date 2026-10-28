<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\LostModeController;
use App\Http\Controllers\User\OrderProcessController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\PaymentCallbackController;
use App\Http\Controllers\User\PublicController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\TagActivationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Area publik & User
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'landing'])->name('home');

// Modul Pemesanan Custom & Pelacakan Publik (FR-ORD)
Route::get('/pesan', [OrderProcessController::class, 'create'])->name('order.create');
Route::post('/pesan', [OrderProcessController::class, 'store'])->name('order.store');
Route::get('/pesan/sukses/{order_no}', [OrderProcessController::class, 'success'])->name('order.success');
Route::post('/pesan/{order_no}/snap-token', [OrderProcessController::class, 'getSnapToken'])->name('order.snap_token');
Route::get('/pesan/{order_no}/check-status', [OrderProcessController::class, 'checkStatus'])->name('order.check_status');
Route::post('/pesan/{order_no}/metode-tunai', [OrderProcessController::class, 'selectCash'])->name('order.select_cash');
Route::post('/pesan/{order_no}/metode-online', [OrderProcessController::class, 'selectOnline'])->name('order.select_online');
Route::post('/pesan/check-email', [OrderProcessController::class, 'checkEmail'])->name('order.check_email');
Route::get('/lacak-pesanan', [OrderProcessController::class, 'track'])->name('order.track');

// Midtrans Payment Gateway Webhook Callback
Route::post('/midtrans/callback', [PaymentCallbackController::class, 'handle'])->name('midtrans.callback');
Route::post('/api/midtrans/callback', [PaymentCallbackController::class, 'handle'])->name('midtrans.callback.api');

Route::middleware('guest')->group(function () {
    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    Route::get('/daftar', [UserAuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [UserAuthController::class, 'register'])->name('register.submit');

    // FR-AUT-03: Lupa & Reset Kata Sandi
    Route::get('/lupa-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token?}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [UserAuthController::class, 'logout'])->name('logout');
    Route::middleware('tag.activated')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::post('/dashboard/switch-mode', [UserDashboardController::class, 'switchMode'])->name('user.dashboard.switchMode');
        Route::post('/dashboard/profile', [UserDashboardController::class, 'updateProfile'])->name('user.dashboard.updateProfile');
        Route::post('/dashboard/privacy', [UserDashboardController::class, 'updatePrivacy'])->name('user.dashboard.updatePrivacy');
        Route::get('/dashboard/mode/kampus', [UserDashboardController::class, 'editKampus'])->name('user.profile.kampus');
        Route::post('/dashboard/mode/kampus', [UserDashboardController::class, 'updateKampus'])->name('user.profile.kampus.update');
        Route::get('/dashboard/mode/kerja', [UserDashboardController::class, 'editKerja'])->name('user.profile.kerja');
        Route::post('/dashboard/mode/kerja', [UserDashboardController::class, 'updateKerja'])->name('user.profile.kerja.update');
        Route::get('/dashboard/mode/umkm', [UserDashboardController::class, 'editUmkm'])->name('user.profile.umkm');
        Route::post('/dashboard/mode/umkm', [UserDashboardController::class, 'updateUmkm'])->name('user.profile.umkm.update');

        Route::get('/dashboard/mode/umkm/produk/tambah', [UserDashboardController::class, 'createProduct'])->name('user.profile.umkm.product.create');
        Route::post('/dashboard/mode/umkm/produk/tambah', [UserDashboardController::class, 'storeProduct'])->name('user.profile.umkm.product.store');
        Route::get('/dashboard/mode/umkm/produk/{id}/edit', [UserDashboardController::class, 'editProduct'])->name('user.profile.umkm.product.edit');
        Route::post('/dashboard/mode/umkm/produk/{id}/edit', [UserDashboardController::class, 'updateProduct'])->name('user.profile.umkm.product.update');
        Route::post('/dashboard/mode/umkm/produk/{id}/hapus', [UserDashboardController::class, 'deleteProduct'])->name('user.profile.umkm.product.destroy');

        Route::get('/dashboard/merch', [UserDashboardController::class, 'merch'])->name('user.merch');
        Route::post('/dashboard/merch/tambah-tag', [UserDashboardController::class, 'claimTag'])->name('user.merch.claimTag');
        Route::post('/dashboard/merch/tag/{id}/toggle-status', [UserDashboardController::class, 'toggleTagStatus'])->name('user.merch.toggleTagStatus');
        Route::post('/dashboard/merch/tag/{id}/mode', [UserDashboardController::class, 'updateTagMode'])->name('user.merch.updateTagMode');
    });

    Route::get('/profil-saya', [UserDashboardController::class, 'myProfile'])->name('user.my_profile');
    Route::get('/analytics', [App\Http\Controllers\User\AnalyticsController::class, 'index'])->name('user.analytics');
    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('user.settings');
    Route::post('/pengaturan/password', [SettingsController::class, 'updatePassword'])->name('user.settings.password');
    Route::post('/pengaturan/privacy', [SettingsController::class, 'updatePrivacy'])->name('user.settings.privacy');
    Route::post('/pengaturan/feedback', [SettingsController::class, 'submitFeedback'])->name('user.settings.feedback');
    Route::get('/mode-hilang', [LostModeController::class, 'edit'])->name('user.lost-mode.edit');
    Route::post('/mode-hilang', [LostModeController::class, 'update'])->name('user.lost-mode.update');
    Route::post('/mode-hilang/pesan/{message}/read', [LostModeController::class, 'markMessageAsRead'])->name('user.lost-mode.messages.read');
    Route::post('/mode-hilang/pesan/read-all', [LostModeController::class, 'markAllMessagesAsRead'])->name('user.lost-mode.messages.readAll');
});

Route::get('/aktivasi-tag', [TagActivationController::class, 'show'])
    ->name('user.activation.show');

Route::post('/aktivasi-tag/auth', [TagActivationController::class, 'authStep'])
    ->name('user.activation.auth');

Route::post('/aktivasi-tag', [TagActivationController::class, 'store'])
    ->name('user.activation.store');

Route::post('/aktivasi-tag/profil', [TagActivationController::class, 'profileStep'])
    ->name('user.activation.profile');

Route::get('/u/{tag_code}', [PublicController::class, 'showTagProfile'])
    ->name('public.tag');

Route::get('/u/{tag_code}/vcard', [PublicController::class, 'downloadVCard'])
    ->name('public.tag.vcard');

Route::get('/u/{tag_code}/cv', [PublicController::class, 'downloadCv'])
    ->name('public.tag.cv');

Route::get('/u/{tag_code}/l/{link_id}', [PublicController::class, 'clickLink'])
    ->name('public.tag.link');

// FR-LST-01, FR-LST-02, FR-LST-04: Kirim pesan anonim/aman dari penemu barang (maks 3 per 15 menit)
Route::post('/u/{tag_code}/lapor-temuan', [PublicController::class, 'submitLostItemMessage'])
    ->middleware('throttle:3,15')
    ->name('public.tag.lost_message');

// FR-PUB-12: Laporkan Penyalahgunaan Profil Publik ke Admin (maks 5 per 15 menit)
Route::post('/u/{tag_code}/lapor-abuse', [PublicController::class, 'submitAbuseReport'])
    ->middleware('throttle:5,15')
    ->name('public.tag.abuse_report');

// FR-ADM-09: Formulir Kontak Landing Page ke Admin Drawer (maks 5 per 15 menit)
Route::post('/kontak', [PublicController::class, 'submitContact'])
    ->middleware('throttle:5,15')
    ->name('contact.submit');

/*
|--------------------------------------------------------------------------
| Area Admin (tidak diubah, kecuali pembatasan role admin/staff)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'role:admin,staff'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/generate', [BatchController::class, 'index'])->name('generate');
        Route::post('/generate', [BatchController::class, 'store'])->name('generate.store');
        Route::get('/tags', [TagController::class, 'index'])->name('tags');
        Route::get('/tags/export-csv', [TagController::class, 'exportCsv'])->name('tags.exportCsv');
        Route::post('/tags/{tag}/block', [TagController::class, 'block'])->name('tags.block');
        Route::post('/tags/{tag}/unblock', [TagController::class, 'unblock'])->name('tags.unblock');
        Route::post('/tags/{tag}/unlink', [TagController::class, 'unlink'])->name('tags.unlink');
        Route::post('/tags/{tag}/pair', [TagController::class, 'pair'])->name('tags.pair');
        Route::post('/tags/{tag}/mode', [TagController::class, 'updateMode'])->name('tags.updateMode');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/resend-activation', [OrderController::class, 'resendActivation'])->name('orders.resendActivation');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export-csv', [AnalyticsController::class, 'exportCsv'])->name('analytics.exportCsv');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');

        // Sub-modul Pengaturan & Administrasi Lanjutan (FR-ADM-08)
        Route::post('/settings/users', [SettingController::class, 'storeUser'])->name('settings.users.store');
        Route::put('/settings/users/{user}', [SettingController::class, 'updateUser'])->name('settings.users.update');
        Route::post('/settings/users/{user}/toggle-status', [SettingController::class, 'toggleUserStatus'])->name('settings.users.toggleStatus');
        Route::delete('/settings/users/{user}', [SettingController::class, 'deleteUser'])->name('settings.users.destroy');

        Route::post('/settings/designs', [SettingController::class, 'storeDesign'])->name('settings.designs.store');
        Route::post('/settings/designs/{design}/toggle-status', [SettingController::class, 'toggleDesignStatus'])->name('settings.designs.toggleStatus');
        Route::delete('/settings/designs/{design}', [SettingController::class, 'deleteDesign'])->name('settings.designs.destroy');

        Route::post('/settings/financial', [SettingController::class, 'storeFinancial'])->name('settings.financial.store');
        Route::put('/settings/financial/{entry}', [SettingController::class, 'updateFinancial'])->name('settings.financial.update');
        Route::post('/settings/financial/sync-orders', [SettingController::class, 'syncOrdersFinancial'])->name('settings.financial.syncOrders');
        Route::get('/settings/financial/export-csv', [SettingController::class, 'exportFinancialCsv'])->name('settings.financial.exportCsv');
        Route::delete('/settings/financial/{entry}', [SettingController::class, 'deleteFinancial'])->name('settings.financial.destroy');

        // Message & Notification Drawer Routes (FR-ADM-09)
        Route::post('/messages/read-all', [MessageController::class, 'markAllAsRead'])->name('messages.readAll');
        Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        Route::post('/messages/{id}/deactivate-tag', [MessageController::class, 'deactivateTag'])->name('messages.deactivateTag');
    });
});
