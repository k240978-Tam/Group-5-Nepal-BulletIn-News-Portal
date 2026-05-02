<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $content = sanitize_input($_POST['content'] ?? '');
    
    if ($article_id <= 0 || empty($content)) {
        $_SESSION['error_message'] = "Invalid comment data.";
        redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
    }

    // Usually comments are pending, but for simplicity here we auto-approve or depend on role
    $status = 'approved'; 
    
    try {
        $stmt = $pdo->prepare("INSERT INTO comments (article_id, user_id, content, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$article_id, $_SESSION['user_id'], $content, $status]);
        
        $_SESSION['success_message'] = "Your comment has been posted.";
        redirect("/article.php?id=$article_id");
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error posting comment.";
        redirect("/article.php?id=$article_id");
    }
} else {
    redirect('/index.php');
}
?>
