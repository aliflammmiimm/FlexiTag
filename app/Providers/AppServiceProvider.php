<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        if (! app()->runningInConsole()) {
            if (request()->header('x-forwarded-proto') === 'https' || str_contains((string) request()->getHttpHost(), 'ngrok')) {
                URL::forceScheme('https');
            }
        }

        // View Composer for Admin layout to provide real-time messages & notification drawer data
        View::composer('layouts.admin', function ($view) {
            $unreadMessagesCount = 0;
            $adminNotifications = collect();

            if (Schema::hasTable('messages')) {
                $adminTypes = [
                    Message::TYPE_ABUSE_REPORT,
                    Message::TYPE_CONTACT,
                    Message::TYPE_USER_FEEDBACK,
                    Message::TYPE_NEW_ORDER,
                    Message::TYPE_LOW_STOCK,
                ];

                $unreadMessagesCount = DB::table('messages')
                    ->whereNull('read_at')
                    ->whereIn('type', $adminTypes)
                    ->count();

                $adminNotifications = Message::whereIn('type', $adminTypes)
                    ->with('order')
                    ->orderBy('created_at', 'desc')
                    ->take(40)
                    ->get();
            }

            $view->with([
                'unreadMessagesCount' => $unreadMessagesCount,
                'adminNotifications' => $adminNotifications,
            ]);
        });
    }
}
