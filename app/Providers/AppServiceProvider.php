<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
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
        if (app()->environment('production')) {
            // Configurar trusted proxies para Railway
            $this->app['request']->setTrustedProxies(
                ['*'],
                \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                    \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                    \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                    \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
            );

            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
        // if (env('APP_ENV') === 'production') {
        //     URL::forceScheme('https');
        // }
        // if (app()->environment('local')) {
        //     URL::forceRootUrl(config('app.url')); // Forzar la URL del almacenamiento
        //     Storage::disk('public')->url('/');
        // }
    }
}
