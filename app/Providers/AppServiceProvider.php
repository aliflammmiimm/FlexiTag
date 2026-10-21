<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer for Admin layout to provide real-time messages & notification drawer data
        View::composer('layouts.admin', function ($view) {
            $unreadMessagesCount = 0;
            $adminNotifications = collect();

            if (Schema::hasTable('messages')) {
                $unreadMessagesCount = DB::table('messages')
                    ->whereNull('read_at')
                    ->whereIn('type', ['abuse_report', 'contact'])
                    ->count();

                $adminNotifications = DB::table('messages')
                    ->whereIn('type', ['abuse_report', 'contact'])
                    ->orderBy('created_at', 'desc')
                    ->take(30)
                    ->get();
            }

            $view->with([
                'unreadMessagesCount' => $unreadMessagesCount,
                'adminNotifications' => $adminNotifications,
            ]);
        });
    }
}
