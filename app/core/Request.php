<?php

namespace App\Core;

class Request
{
    public static function uri()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Normalize the URI when the app is served from a subdirectory or public folder.
        $scriptDir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($scriptDir && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
            $uri = trim($uri, '/');
        }

        $base = trim(parse_url(App::get('config')['url'], PHP_URL_PATH), '/');
        if ($base && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
            $uri = trim($uri, '/');
        }

        if (strpos($uri, 'public/') === 0) {
            $uri = substr($uri, strlen('public/'));
            $uri = trim($uri, '/');
        }

        if (substr($uri, -9) === 'index.php') {
            $uri = substr($uri, 0, -9);
            $uri = trim($uri, '/');
        }

        return $uri;
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
