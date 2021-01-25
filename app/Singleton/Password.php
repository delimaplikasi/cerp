<?php namespace App\Singleton;

use App\Config\Security;

class Password
{
    public static function hash($value = null)
    {
        return password_hash($value, Security::$passwordHash);
    }
}
