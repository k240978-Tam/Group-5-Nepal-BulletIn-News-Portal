<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_login();
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        $_SESSION['error_message'] = "Name and Email are required.";
        redirect('/newsportal/profile.php');
    }

    try {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "This email is already registered to another account.";
            redirect('/newsportal/profile.php');
        }

        if (!empty($password)) {
            // Update with password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $email, $hashed_password, $user['id']]);
        } else {
            // Update without password
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $user['id']]);
        }

        // Update session data
        $_SESSION['user_name'] = $name;
        $_SESSION['email'] = $email;

        $_SESSION['success_message'] = "Profile updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
    }

    redirect('/newsportal/profile.php');
} else {
    redirect('/newsportal/profile.php');
}
?>
