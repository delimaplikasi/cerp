<?php namespace App\Config;

use atk4\data\Persistence;

class Database
{
    public static $user = 'postgre';
    public static $pass = '';
    public static $database = 'delimaplikasi_cerp';
    public static $port = 5432;
    public static $host = 'localhost';
    public static $migrationDirs = [
        '%%PHINX_CONFIG_DIR%%/app/Database/Migration'
    ];

    public static $seedDirs = [
        '%%PHINX_CONFIG_DIR%%/app/Database/Seed'
    ];

    public static function connect(): ?Persistence
    {
        $host = static::$host;
        $database = static::$database;
        $port = static::$port;

        return Persistence::connect("pgsql:host={$host};port={$port};dbname={$database};", static::$user, static::$pass);
    }
}
