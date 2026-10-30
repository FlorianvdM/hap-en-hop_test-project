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
        // Op de echte website (APP_ENV=production) maken we alle links, plaatjes
        // en formulieren met https. Lokaal en in de tests gebeurt dit niet.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}