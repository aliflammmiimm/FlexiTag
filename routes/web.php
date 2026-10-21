<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Alias for standard Laravel auth redirection
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin Authentication & Console Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth'])->group(function () {
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

        // Message & Notification Drawer Routes (FR-ADM-09)
        Route::post('/messages/read-all', [MessageController::class, 'markAllAsRead'])->name('messages.readAll');
        Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        Route::post('/messages/{id}/deactivate-tag', [MessageController::class, 'deactivateTag'])->name('messages.deactivateTag');
    });
});
