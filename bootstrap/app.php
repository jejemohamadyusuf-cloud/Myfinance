<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Force Storage Path ke /tmp untuk kompatibilitas Vercel Serverless (Read-Only Filesystem)
$app = Application::configure(basePath: dirname(__DIR__))
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
    })->create();

/*
|--------------------------------------------------------------------------
| Override Path Storage untuk Vercel
|--------------------------------------------------------------------------
*/
$app->useStoragePath('/tmp/storage');

// Pastikan folder log dan view compiled dibuat otomatis di /tmp
if (!file_exists('/tmp/storage/logs')) {
    mkdir('/tmp/storage/logs', 0755, true);
}
if (!file_exists('/tmp/storage/framework/views')) {
    mkdir('/tmp/storage/framework/views', 0755, true);
}

return $app;