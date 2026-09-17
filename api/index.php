<?php

// [KOREKSI MULAI]: Hancurkan pengaruh SEMUA file cache Windows yang terbawa ke Vercel
$tmpStorage = '/tmp/storage';
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

// Override SEMUA jalur cache inti Laravel agar membaca dari /tmp yang bersih
$caches = [
    'APP_SERVICES_CACHE' => 'services.php',
    'APP_PACKAGES_CACHE' => 'packages.php',
    'APP_CONFIG_CACHE'   => 'config.php',
    'APP_ROUTES_CACHE'   => 'routes.php',
    'APP_EVENTS_CACHE'   => 'events.php',
];

foreach ($caches as $envKey => $file) {
    putenv("$envKey=$tmpStorage/bootstrap/cache/$file");
    $_ENV[$envKey] = "$tmpStorage/bootstrap/cache/$file";
    $_SERVER[$envKey] = "$tmpStorage/bootstrap/cache/$file";
}

putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
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
    $request->headers->set('Accept', 'application/json'); 
    
    $response = $kernel->handle($request);
    $response->send();
    
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    echo "<h1>CRITICAL ERROR</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " (Line " . $e->getLine() . ")</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}