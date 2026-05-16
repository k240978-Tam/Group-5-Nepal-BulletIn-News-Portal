<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = sanitize_input($_POST['role'] ?? 'user');

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
        redirect('/newsportal/admin/users.php');
    }

    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "A user with this email already exists.";
            redirect('/newsportal/admin/users.php');
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $role]);
        
        log_action("Admin created new user", "Name: $name, Email: $email, Role: $role");
        
        $_SESSION['success_message'] = "New user account created successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
}

redirect('/newsportal/admin/users.php');
?>
