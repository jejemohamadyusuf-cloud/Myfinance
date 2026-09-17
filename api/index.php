<?php

// 1. Buat direktori /tmp untuk Serverless Vercel
$tmpDirs = [
    '/tmp/storage/logs',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. TIMPA PAKSA konfigurasi agar aman dari cache & delay Vercel
putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('APP_CONFIG_CACHE=/tmp/storage/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/storage/bootstrap/cache/routes.php');

$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['LOG_CHANNEL'] = 'stderr';

// 3. Load aplikasi Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Kunci storage ke /tmp tepat sebelum Kernel berjalan
$app->useStoragePath('/tmp/storage');

// 5. Jalankan Request (dengan penangkap error final)
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $request = Illuminate\Http\Request::capture();
    // [TAMBAHAN BARU]: Paksa request sebagai API/JSON
    $request->headers->set('Accept', 'application/json'); 
    
    $response = $kernel->handle($request);
    $response->send();
    
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    echo "<h1>CRITICAL ERROR</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " (Line " . $e->getLine() . ")</p>";
}