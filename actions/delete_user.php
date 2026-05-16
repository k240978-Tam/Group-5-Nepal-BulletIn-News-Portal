<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin']);
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);

    if ($user_id <= 0) {
        $_SESSION['error_message'] = "Invalid user ID.";
        redirect('/newsportal/admin/users.php');
    }

    if ($user_id === (int)$user['id']) {
        $_SESSION['error_message'] = "You cannot delete yourself.";
        redirect('/newsportal/admin/users.php');
    }

    try {
        $pdo->beginTransaction();

        // Delete articles by this user (if not handled by cascade)
        // Note: Our schema has SET NULL, but we want to delete them for this 'advanced' feature
        $pdo->prepare("DELETE FROM articles WHERE author_id = ?")->execute([$user_id]);
        
        // Comments are ON DELETE CASCADE, but let's be explicit if needed
        $pdo->prepare("DELETE FROM comments WHERE user_id = ?")->execute([$user_id]);

        // Finally delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        log_action("Deleted user", "User ID: $user_id");

        $pdo->commit();
        $_SESSION['success_message'] = "User and all their associated content have been deleted.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
}

redirect('/newsportal/admin/users.php');
?>
