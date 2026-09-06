<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    define('LARAVEL_START', microtime(true));

    // Binding folder temporary /tmp untuk lingkungan Serverless
    $dirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/cache',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    // Memaksa Laravel menggunakan /tmp untuk seluruh file cache sistem
    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');

    // ------------------------------------------------------------------
    // PENGAMANAN DRIVER SERVERLESS (Mencegah Manager Menerima Nilai Null)
    // ------------------------------------------------------------------
    putenv('LOG_CHANNEL=stack');
    putenv('SESSION_DRIVER=file');
    putenv('CACHE_STORE=file');
    putenv('QUEUE_CONNECTION=sync');
    // ------------------------------------------------------------------

    // Memuat aplikasi menggunakan dirname() agar path aman
    require dirname(__DIR__) . '/vendor/autoload.php';
    $app = require_once dirname(__DIR__) . '/bootstrap/app.php';

    $app->handleRequest(Illuminate\Http\Request::capture());

} catch (\Throwable $e) {
    echo "<h1>System Error Log:</h1>";
    echo "<b>Message:</b> " . $e->getMessage() . "<br><br>";
    echo "<b>File:</b> " . $e->getFile() . " (Line: " . $e->getLine() . ")<br><br>";
    echo "<b>Stack Trace:</b><br><pre>" . $e->getTraceAsString() . "</pre><br><br>";
}