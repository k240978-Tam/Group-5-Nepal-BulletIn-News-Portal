<?php
namespace App\Core;

class Router {
    private $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function get($uri, $controller) {
        $this->routes['GET'][$uri] = $controller;
    }

    public function post($uri, $controller) {
        $this->routes['POST'][$uri] = $controller;
    }

    public function dispatch($uri, $method) {
        // Remove query string from URI
        $uri = explode('?', $uri)[0];
        // Strip trailing slash
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        if (array_key_exists($uri, $this->routes[$method])) {
            $action = $this->routes[$method][$uri];
            $this->callAction(...explode('@', $action));
        } else {
            http_response_code(404);
            die("404 Not Found");
        }
    }

    private function callAction($controllerName, $actionName) {
        $controllerClass = "App\\Controllers\\" . $controllerName;
        
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $actionName)) {
                return $controller->$actionName();
            }
        }
        
        die("Action {$controllerName}@{$actionName} not found.");
    }
}
