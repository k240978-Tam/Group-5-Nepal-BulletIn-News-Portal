<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error_message'] = "Please log in to access this page.";
        redirect('/newsportal/login.php');
    }
}

function require_role($allowed_roles) {
    require_login();
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        $_SESSION['error_message'] = "You do not have permission to access this page.";
        redirect('/newsportal/index.php');
    }
}

function get_logged_in_user() {
    global $pdo;
    if (is_logged_in()) {
        $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}
?>
