<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        redirect('/newsportal/login.php');
    }

    try {
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            log_action("User login", "User ID: " . $user['id']);

            redirect('/newsportal/index.php');
        } else {
            $_SESSION['error_message'] = "Invalid email or password.";
            redirect('/newsportal/login.php');
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "A system error occurred. Please try again later.";
        redirect('/newsportal/login.php');
    }
} else {
    redirect('/newsportal/login.php');
}
?>
