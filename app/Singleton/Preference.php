<?php namespace App\Singleton;

use App\Core\Singleton;
use App\Model\Preference as ModelPreference;

class Preference extends Singleton
{
    protected $values = [];

    public static function get($code = null)
    {
        if (array_key_exists($code, static::instance()->values)) {
            return static::instance()->values[$code];
        } else {
            $model = new ModelPreference(Database::connect());
            $status = $model->tryLoadBy('code', $code)->get();

            if (!is_null($status['id'])) {
                static::instance()->values[$code] = $status;
            }

            return $status;
        }
    }
}
