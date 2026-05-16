<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin']);
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $new_role = sanitize_input($_POST['role'] ?? '');

    if ($user_id <= 0 || !in_array($new_role, ['admin', 'editor', 'journalist', 'user'])) {
        $_SESSION['error_message'] = "Invalid input.";
        redirect('/newsportal/admin/users.php');
    }

    // Prevent admin from changing their own role (safety)
    if ($user_id === (int)$user['id']) {
        $_SESSION['error_message'] = "You cannot change your own role.";
        redirect('/newsportal/admin/users.php');
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
        log_action("Updated user role", "User ID: $user_id, New Role: $new_role");
        $_SESSION['success_message'] = "User role updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
}

redirect('/newsportal/admin/users.php');
?>
