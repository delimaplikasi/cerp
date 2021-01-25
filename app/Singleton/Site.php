<?php namespace App\Singleton;

use App\Config\Site as ConfigSite;
use App\Core\Singleton;

class Site extends Singleton
{
    public static function url($url)
    {
        return ConfigSite::$url . $url;
    }
}
