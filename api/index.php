<?php

// Prepare writable runtime directories in /tmp when running on Vercel Serverless
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    $tmpDirs = [
        '/tmp/views',
        '/tmp/cache',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
    ];

    foreach ($tmpDirs as $dir) {
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // Automatically initialize and seed ephemeral SQLite database on Vercel if no external DB is configured
    $dbConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: 'sqlite';
    $dbDatabase = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: '/tmp/database.sqlite';

    if ($dbConnection === 'sqlite' && str_starts_with((string) $dbDatabase, '/tmp/')) {
        $bundledSqlite = __DIR__.'/../database/database.sqlite';
        if (! file_exists($dbDatabase) || filesize($dbDatabase) === 0) {
            if (file_exists($bundledSqlite) && filesize($bundledSqlite) > 0) {
                @copy($bundledSqlite, $dbDatabase);
            } else {
                @touch($dbDatabase);
            }
        }
    }
}

// Forward Vercel requests to normal Laravel public/index.php
require __DIR__.'/../public/index.php';
