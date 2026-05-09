<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin', 'editor']);
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/newsportal/admin/index.php');
}

$article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;

if ($article_id <= 0) {
    $_SESSION['error_message'] = "Invalid article ID.";
    redirect('/newsportal/admin/index.php');
}

// Verify article exists
$stmt = $pdo->prepare("SELECT id, title FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    $_SESSION['error_message'] = "Article not found.";
    redirect('/newsportal/admin/index.php');
}

try {
    // article_tags and comments are deleted via CASCADE foreign keys
    $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$article_id]);
    $_SESSION['success_message'] = "Article \"" . htmlspecialchars($article['title']) . "\" has been permanently deleted.";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Could not delete article: " . $e->getMessage();
}

redirect('/newsportal/admin/index.php');
?>
