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
            URL::forceScheme('https');

            // Configurar para Railway
            if (request()->header('X-Forwarded-Proto') === 'https') {
                request()->server->set('HTTPS', 'on');
                request()->server->set('SERVER_PORT', 443);
            }

            // Forzar root URL
            URL::forceRootUrl(env('APP_URL'));
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
