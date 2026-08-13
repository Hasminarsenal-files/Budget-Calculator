<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// Prepare storage and bootstrap cache directories in /tmp for Vercel Serverless environment
$dirs = [
    '/tmp/bootstrap/cache',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/database',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set Vercel serverless environment variables to use writable /tmp and stderr logging
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('LOG_CHANNEL=stderr');
putenv('APP_MAINTENANCE_DRIVER=file');
putenv('APP_MAINTENANCE_STORE=file');

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// If DB connection is SQLite, ensure database file exists in /tmp
if (env('DB_CONNECTION') === 'sqlite') {
    $dbPath = env('DB_DATABASE', '/tmp/database.sqlite');
    if ($dbPath === '/tmp/database.sqlite') {
        $isNewDb = !file_exists($dbPath) || filesize($dbPath) === 0;
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }
        if ($isNewDb) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                error_log('Vercel SQLite migration notice: ' . $e->getMessage());
            }
        }
    }
}

$app->handleRequest(Request::capture());
