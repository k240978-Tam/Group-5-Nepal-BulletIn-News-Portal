<?php
namespace App\Controllers;
use App\Core\Controller;

class AuthController extends Controller {

    public function showLogin() {
        $this->view('auth/login');
    }

    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error_message'] = "Email and password are required.";
            $this->redirect('/newsportal/login');
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['success_message'] = "Welcome back, " . htmlspecialchars($user['name']) . "!";
            
            if (in_array($user['role'], ['admin', 'editor', 'journalist'])) {
                $this->redirect('/newsportal/admin');
            } else {
                $this->redirect('/newsportal/');
            }
        } else {
            $_SESSION['error_message'] = "Invalid email or password.";
            $this->redirect('/newsportal/login');
        }
    }

    public function showRegister() {
        $this->view('auth/register');
    }

    public function register() {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error_message'] = "All fields are required.";
            $this->redirect('/newsportal/register');
        }

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "Email address is already in use.";
            $this->redirect('/newsportal/register');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$name, $email, $hashedPassword]);
        
        $_SESSION['success_message'] = "Registration successful! Please login.";
        $this->redirect('/newsportal/login');
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /newsportal/');
        exit;
    }
}
