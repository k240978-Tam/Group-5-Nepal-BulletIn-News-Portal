<?php

/**
 * Front Controller
 */

require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\App;
use App\Core\Request;
use App\Core\Router;

try {
    Router::load(ROOT_PATH . '/routes/web.php')
        ->direct(Request::uri(), Request::method());
} catch (Exception $e) {
    // Basic error handling for now
    die("Application Error: " . $e->getMessage());
}
