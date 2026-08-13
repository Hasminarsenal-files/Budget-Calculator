<?php

// Prepare storage directories in /tmp for Vercel Serverless environment
$dirs = [
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

if (!file_exists('/tmp/database.sqlite')) {
    touch('/tmp/database.sqlite');
}

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Vercel Serverless Entrypoint for Laravel 13
require __DIR__ . '/../public/index.php';

