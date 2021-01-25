<?php namespace App\Singleton;

use Aura\Web\WebFactory;
use App\Core\Singleton;

class Http extends Singleton
{
    protected ?WebFactory $factory = null;

    public static function factory(): ?WebFactory
    {
        $result = static::instance()->factory;
        if (is_null($result)) {
            $result = new WebFactory($GLOBALS);
            static::instance()->factory = $result;
        }

        return $result;
    }
}
