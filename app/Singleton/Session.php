<?php namespace App\Singleton;

use Aura\Session\Segment;
use Aura\Session\Session as AuraSession;
use Aura\Session\SessionFactory;
use App\Core\Singleton;

class Session extends Singleton
{
    protected ?AuraSession $manager = null;

    public static function start()
    {
        if (is_null(self::manager())) {
            static::instance()->manager = (new SessionFactory())->newInstance($_COOKIE);
        }
    }

    public static function clear()
    {
        if (!is_null(self::manager())) {
            self::manager()->clear();
        }
    }

    public static function clearFlash()
    {
        if (!is_null(self::manager())) {
            self::manager()->clearFlash();
        }
    }

    public static function destroy()
    {
        if (!is_null(self::manager())) {
            self::manager()->destroy();
        }
    }

    public static function regenerate()
    {
        if (!is_null(self::manager())) {
            static::instance()->manager->regenerateId();
        }
    }

    public static function of($name): ?Segment
    {
        if (is_null(self::manager())) {
            self::start();
        }

        return is_null(self::manager()) ? null : self::manager()->getSegment($name);
    }

    public static function manager(): ?AuraSession
    {
        return static::instance()->manager;
    }
}
