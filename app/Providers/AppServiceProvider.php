<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AIService;
use App\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\Barryvdh\DomPDF\ServiceProvider::class);
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            $forwardedProto = request()->server('HTTP_X_FORWARDED_PROTO');

            if (config('app.force_https') || $forwardedProto === 'https') {
                URL::forceScheme('https');
            }
        }

        View::composer('layouts.admin', function ($view) {
            $unreadCount = 0;
            $recentNotifications = collect();

            if (auth()->check()) {
                $unreadCount = Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();

                $recentNotifications = Notification::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            }

            $view->with(compact('unreadCount', 'recentNotifications'));
        });
    }
}
