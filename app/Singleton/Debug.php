<?php namespace App\Singleton;

use App\Core\Singleton;
use DebugBar\StandardDebugBar;

class Debug extends Singleton
{
    protected $vars = [];
    protected ?StandardDebugBar $debugBar = null;

    public static function add($debug = null)
    {
        static::instance()->vars[] = $debug;
    }

    public static function get()
    {
        return static::instance()->vars;
    }

    public static function setBar(?StandardDebugBar $value = null)
    {
        static::instance()->debugBar = $value;
    }

    public static function getBar(): ?StandardDebugBar
    {
        return static::instance()->debugBar;
    }
}
