<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        redirect('/register.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Please enter a valid email address.";
        redirect('/register.php');
    }

    if (strlen($password) < 8) {
        $_SESSION['error_message'] = "Password must be at least 8 characters.";
        redirect('/register.php');
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        redirect('/register.php');
    }

    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "Email is already registered.";
            redirect('/register.php');
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$name, $email, $hashed_password]);

        // Auto login
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['name'] = $name;
        $_SESSION['role'] = 'user';
        $_SESSION['success_message'] = "Registration successful. Welcome!";

        redirect('/index.php');

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "A system error occurred. Please try again later.";
        redirect('/register.php');
    }
} else {
    redirect('/register.php');
}
?>
