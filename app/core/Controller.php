<?php
namespace App\Core;

class Controller {
    protected $pdo;

    public function __construct() {
        $this->pdo = require __DIR__ . '/../../config/database.php';
    }

    /**
     * Render a view with data
     */
    protected function view($viewPath, $data = []) {
        // Extract data to make variables available in the view
        extract($data);
        
        $file = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (file_exists($file)) {
            require $file;
        } else {
            die("View not found: " . $viewPath);
        }
    }

    /**
     * Redirect to a specific URL
     */
    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }
    
    /**
     * Ensure user has required role
     */
    protected function requireRole($roles = []) {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/newsportal/login');
        }
        
        if (!empty($roles)) {
            if (!in_array($_SESSION['role'], $roles)) {
                die("Unauthorized access.");
            }
        }
    }
}
