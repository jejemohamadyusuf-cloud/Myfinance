<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Vercel Serverless Storage & View Cache Fix
|--------------------------------------------------------------------------
| Vercel memiliki sistem berkas read-only. Jika berjalan di Vercel,
| alihkan storage path dan view cache ke /tmp agar tidak error 500.
*/
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    // Buat struktur folder di /tmp jika belum ada
    $tmpStorage = '/tmp/storage';
    $directories = [
        $tmpStorage . '/framework/views',
        $tmpStorage . '/framework/cache/data',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/logs',
    ];

    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // Set environment variable tempat penyimpanan view
    $_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booted(function ($app) {
        /*
        |--------------------------------------------------------------------------
        | Override Path Storage Saat Bootstrapping
        |--------------------------------------------------------------------------
        */
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
            $app->useStoragePath('/tmp/storage');
        }
    })
    ->create();