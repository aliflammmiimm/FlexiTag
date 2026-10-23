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
use App\Http\Controllers\User\PublicController;
use App\Http\Controllers\User\TagActivationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Area publik & User
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'landing'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    Route::get('/daftar', [UserAuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [UserAuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [UserAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/profil-saya', [UserDashboardController::class, 'myProfile'])->name('user.my_profile');
    Route::post('/dashboard/switch-mode', [UserDashboardController::class, 'switchMode'])->name('user.dashboard.switchMode');
    Route::post('/dashboard/profile', [UserDashboardController::class, 'updateProfile'])->name('user.dashboard.updateProfile');
    Route::post('/dashboard/privacy', [UserDashboardController::class, 'updatePrivacy'])->name('user.dashboard.updatePrivacy');
    Route::get('/analytics', [\App\Http\Controllers\User\AnalyticsController::class, 'index'])->name('user.analytics');
    Route::get('/pengaturan', [\App\Http\Controllers\User\SettingsController::class, 'index'])->name('user.settings');
    Route::post('/pengaturan/password', [\App\Http\Controllers\User\SettingsController::class, 'updatePassword'])->name('user.settings.password');
    Route::post('/pengaturan/privacy', [\App\Http\Controllers\User\SettingsController::class, 'updatePrivacy'])->name('user.settings.privacy');
    Route::get('/mode-hilang', [\App\Http\Controllers\User\LostModeController::class, 'edit'])->name('user.lost-mode.edit');
    Route::post('/mode-hilang', [\App\Http\Controllers\User\LostModeController::class, 'update'])->name('user.lost-mode.update');
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
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
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
        Route::delete('/settings/financial/{entry}', [SettingController::class, 'deleteFinancial'])->name('settings.financial.destroy');

        // Message & Notification Drawer Routes (FR-ADM-09)
        Route::post('/messages/read-all', [MessageController::class, 'markAllAsRead'])->name('messages.readAll');
        Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        Route::post('/messages/{id}/deactivate-tag', [MessageController::class, 'deactivateTag'])->name('messages.deactivateTag');
    });
});
