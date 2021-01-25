<?php namespace App\Singleton;

use App\Config\Database as ConfigDatabase;
use Atk4\Data\Persistence;
use App\Core\Singleton;

class Database extends Singleton
{
    protected $connection = null;

    public static function connect(): ?Persistence
    {
        $connection = self::instance()->connection;
        if (is_null($connection)) {
            $connection = ConfigDatabase::connect();
            self::instance()->connection = $connection;
        }

        return $connection;
    }
}
