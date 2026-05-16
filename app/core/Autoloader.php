<?php
namespace App\Core;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            // Project-specific namespace prefix
            $prefix = 'App\\';

            // Base directory for the namespace prefix
            $base_dir = BASE_PATH . '/app/';

            // Does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // No, move to the next registered autoloader
                return;
            }

            // Get the relative class name
            $relative_class = substr($class, $len);

            // Replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            // We use strtolower on the first part so App\Core\Router becomes app/core/Router.php
            
            $parts = explode('\\', $relative_class);
            $class_name = array_pop($parts);
            
            $dir_path = '';
            if (count($parts) > 0) {
                $dir_path = strtolower(implode('/', $parts)) . '/';
            }
            
            $file = $base_dir . $dir_path . $class_name . '.php';

            // If the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        });
    }
}

// Register the autoloader
Autoloader::register();
