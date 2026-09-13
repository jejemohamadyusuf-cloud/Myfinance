<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register Autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());