<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return $this->view('auth.login');
    }

    public function login()
    {
        // Simple login logic for now
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return $this->redirect('profile');
        }

        return $this->view('auth.login', ['error' => 'Invalid credentials']);
    }

    public function showRegistrationForm()
    {
        return $this->view('auth.register');
    }

    public function register()
    {
        // Handle registration
        // ...
        return $this->redirect('login');
    }

    public function logout()
    {
        session_start();
        session_destroy();
        return $this->redirect('');
    }
}
