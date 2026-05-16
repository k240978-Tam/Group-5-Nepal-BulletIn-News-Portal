<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', ROOT_PATH . '/resources/views');

require_once __DIR__ . '/autoload.php';

// Try to load helpers
$helpers = glob(APP_PATH . '/helpers/*.php');
foreach ($helpers as $helper) {
    require_once $helper;
}

// In the future we will load .env here and establish DB connection
