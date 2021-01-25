<?php namespace App\Singleton\Http;

use Aura\Web\Request;
use Aura\Web\Request\Client;
use Aura\Web\Request\Files;
use Aura\Web\Request\Headers;
use Aura\Web\Request\Method;
use Aura\Web\Request\Params;
use Aura\Web\Request\Url;
use Aura\Web\Request\Values;
use App\Core\Singleton;
use App\Singleton\Http;

class IncomingRequest extends Singleton
{
    protected ?Request $request = null;

    public static function request(): Request
    {
        $result = static::instance()->request;
        if (is_null($result)) {
            $result = Http::factory()->newRequest();
            static::instance()->request = $result;
        }

        return $result;
    }

    public static function cookie(): Values
    {
        return self::request()->cookies;
    }

    public static function env(): Values
    {
        return self::request()->env;
    }

    public static function file(): Files
    {
        return self::request()->files;
    }

    public static function query(): Values
    {
        return self::request()->query;
    }

    public static function post(): Values
    {
        return self::request()->post;
    }

    public static function server(): Values
    {
        return self::request()->server;
    }

    public static function client(): Client
    {
        return self::request()->client;
    }

    public static function header(): Headers
    {
        return self::request()->headers;
    }

    public static function method(): Method
    {
        return self::request()->method;
    }

    public static function param(): Params
    {
        return self::request()->params;
    }

    public static function url(): Url
    {
        return self::request()->url;
    }
}
