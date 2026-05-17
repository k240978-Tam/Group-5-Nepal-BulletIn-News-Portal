<?php

require_once __DIR__ . '/init.php';

use App\Core\App;
use App\Core\Router;

// Instantiate the application and bind configuration.
$app = new App();

if (!defined('APP_URL')) {
    define('APP_URL', App::get('config')['url']);
}

return $app;
