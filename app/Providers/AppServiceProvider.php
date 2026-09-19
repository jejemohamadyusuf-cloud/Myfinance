<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

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
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
            Vite::useBuildDirectory('build');

            $vercelManifest = '/var/task/user/public/build/manifest.json';
            if (file_exists($vercelManifest)) {
                Vite::useManifestFileName($vercelManifest);
            }
        }
    }
}
