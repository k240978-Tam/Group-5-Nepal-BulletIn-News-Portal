<?php
session_start();

// Simple PSR-4 style autoloader
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'App\\';

    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/../app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    // Also lowercase the first directory to match 'app/core' and 'app/controllers'
    $parts = explode('\\', $relative_class);
    $parts[0] = strtolower($parts[0]);
    $file = $base_dir . implode('/', $parts) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Load helper functions
require_once __DIR__ . '/../app/core/functions.php';

// Initialize database connection globally
$GLOBALS['pdo'] = require_once __DIR__ . '/database.php';
