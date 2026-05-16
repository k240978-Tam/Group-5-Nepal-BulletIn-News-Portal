<?php

namespace App\Core;

class Request
{
    public static function uri()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        // Strip out the subdirectory if it exists (e.g. 'newsportal')
        $base = 'newsportal'; // from config preferably, hardcoded for now
        if (strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
            $uri = trim($uri, '/');
        }
        return $uri;
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
