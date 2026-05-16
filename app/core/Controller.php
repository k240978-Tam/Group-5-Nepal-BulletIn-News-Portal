<?php

namespace App\Core;

class Controller
{
    protected $layout = 'layouts.main';

    protected function view($name, $data = [])
    {
        extract($data);
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $name) . '.php';
        
        if (!file_exists($viewFile)) {
            die("View {$name} not found!");
        }

        // Buffer the view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Require the layout and inject the content
        if ($this->layout) {
            $layoutFile = VIEW_PATH . '/' . str_replace('.', '/', $this->layout) . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    protected function redirect($path)
    {
        $url = App::get('config')['url'] . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit();
    }
}
