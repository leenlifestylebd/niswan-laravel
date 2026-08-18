<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
    }

    public function boot(): void
    {
        // সব ভিউতে $site (ব্র্যান্ড/রঙ/ডেলিভারি) পাওয়া যাবে
        View::composer('*', function ($view) {
            $view->with('site', app(SettingsService::class)->public());
        });
    }
}
