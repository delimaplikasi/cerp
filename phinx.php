<?php

use App\Config\Database;

return
[
    'paths' => [
        'migrations' => Database::$migrationDirs,
        'seeds' => Database::$seedDirs,
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'pgsql',
            'host' => Database::$host,
            'name' => Database::$database,
            'user' => Database::$user,
            'pass' => Database::$pass,
            'port' => Database::$port,
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'development_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
