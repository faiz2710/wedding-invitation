<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Di Vercel, Caddy/FrankenPHP menerima HTTP dari proxy Vercel sehingga
        // Laravel duluan menganggap request-nya HTTP. Paksa skema HTTPS di
        // produksi agar URL yang digenerate (CSRF, redirect, asset) tetap https.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
