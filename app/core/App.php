<?php

namespace App\Core;

class App
{
    protected static $container = [];

    public function __construct()
    {
        // Load configurations
        self::bind('config', require ROOT_PATH . '/config/app.php');
    }

    public static function bind($key, $value)
    {
        self::$container[$key] = $value;
    }

    public static function get($key)
    {
        if (!array_key_exists($key, self::$container)) {
            throw new \Exception("No {$key} is bound in the container.");
        }
        return self::$container[$key];
    }

    public function run()
    {
        // Route the request
        Router::load(ROOT_PATH . '/routes/web.php')
              ->direct(Request::uri(), Request::method());
    }
}
