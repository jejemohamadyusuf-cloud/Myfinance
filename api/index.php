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

// 2. Kunci SEMUA driver inti & matikan APP_DEBUG secara mutlak
$secureEnvs = [
    'APP_ENV'                => 'production',
    'APP_DEBUG'              => 'false',
    'APP_SERVICES_CACHE'     => "$tmpStorage/bootstrap/cache/services.php",
    'APP_PACKAGES_CACHE'     => "$tmpStorage/bootstrap/cache/packages.php",
    'APP_CONFIG_CACHE'       => "$tmpStorage/bootstrap/cache/config.php",
    'APP_ROUTES_CACHE'       => "$tmpStorage/bootstrap/cache/routes.php",
    'APP_EVENTS_CACHE'       => "$tmpStorage/bootstrap/cache/events.php",
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

// 3. Load aplikasi Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Kunci storage ke /tmp
$app->useStoragePath($tmpStorage);

// 5. Jalankan Request dengan penanganan error aman
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    // Catat detail error secara diam-diam ke Log Vercel (hanya admin yang bisa lihat)
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    // Tampilkan halaman aman tanpa membocorkan data sensitif ke pengunjung
    http_response_code(500);
    echo "<h1>500 | Terjadi Kesalahan Server</h1><p>Silakan coba beberapa saat lagi.</p>";
}
