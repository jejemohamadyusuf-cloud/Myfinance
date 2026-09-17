<?php

$tmpStorage = '/tmp/storage';

// 1. Pembuatan folder /tmp terintegrasi
$tmpDirs = [
    "$tmpStorage/logs",
    "$tmpStorage/framework/views",
    "$tmpStorage/framework/cache/data",
    "$tmpStorage/framework/sessions",
    "$tmpStorage/bootstrap/cache",
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// [KOREKSI MULAI]: Kunci SEMUA driver inti agar kebal dari variabel kosong di Vercel
$secureEnvs = [
    'APP_SERVICES_CACHE'     => "$tmpStorage/bootstrap/cache/services.php",
    'APP_PACKAGES_CACHE'     => "$tmpStorage/bootstrap/cache/packages.php",
    'APP_CONFIG_CACHE'       => "$tmpStorage/bootstrap/cache/config.php",
    'APP_ROUTES_CACHE'       => "$tmpStorage/bootstrap/cache/routes.php",
    'APP_EVENTS_CACHE'       => "$tmpStorage/bootstrap/cache/events.php",
    
    // Fix mutlak untuk error Maintenance Mode (ArgumentCountError)
    'APP_MAINTENANCE_DRIVER' => 'file', 
    'CACHE_STORE'            => 'array',
    'CACHE_DRIVER'           => 'array',
    'SESSION_DRIVER'         => 'cookie',
    'LOG_CHANNEL'            => 'stderr',
    'QUEUE_CONNECTION'       => 'sync',
    'VIEW_COMPILED_PATH'     => "$tmpStorage/framework/views",
];

foreach ($secureEnvs as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}
// [KOREKSI SELESAI]

// 3. Load aplikasi Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Kunci storage ke /tmp
$app->useStoragePath($tmpStorage);

// 5. Jalankan Request
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $request = Illuminate\Http\Request::capture();
    
    // Hapus sementara header Accept JSON jika ingin melihat tampilan web asli
    // $request->headers->set('Accept', 'application/json'); 
    
    $response = $kernel->handle($request);
    $response->send();
    
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    echo "<h1>CRITICAL ERROR</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " (Line " . $e->getLine() . ")</p>";
    echo "<pre style='background:#f3f4f6; padding:10px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}