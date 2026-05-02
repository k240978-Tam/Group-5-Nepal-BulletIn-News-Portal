<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin', 'editor', 'journalist']);
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $action = $_POST['action'] ?? '';
    
    // For Approve/Reject from review.php
    if ($article_id > 0 && in_array($action, ['publish', 'reject']) && empty($_POST['title'])) {
        require_role(['admin', 'editor']);
        $stmt = $pdo->prepare("UPDATE articles SET status = ? WHERE id = ?");
        $stmt->execute([$action, $article_id]);
        $_SESSION['success_message'] = "Article " . ($action == 'publish' ? 'approved' : 'rejected') . " successfully.";
        redirect('/newsportal/admin/review.php');
    }

    // Creating / Editing
    $title = sanitize_input($_POST['title'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $summary = sanitize_input($_POST['summary'] ?? '');
    // Note: We don't sanitize content stringently here to allow basic HTML, usually you'd use a purifier library.
    $content = $_POST['content'] ?? '';
    
    // Handle image upload (basic simulation, saving to local /uploads/ directory)
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = '/uploads/' . $filename;
        }
    }

    $status = 'draft';
    if ($action == 'publish' && in_array($user['role'], ['admin', 'editor'])) $status = 'published';
    if ($action == 'pending') $status = 'pending';

    try {
        if ($article_id > 0) {
            // Update
            // Don't overwrite image if new one wasn't uploaded
            if ($image_url) {
                $stmt = $pdo->prepare("UPDATE articles SET title = ?, category_id = ?, summary = ?, content = ?, image_url = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $category_id, $summary, $content, $image_url, $status, $article_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE articles SET title = ?, category_id = ?, summary = ?, content = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $category_id, $summary, $content, $status, $article_id]);
            }
            $_SESSION['success_message'] = "Article updated successfully.";
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO articles (title, content, summary, image_url, author_id, category_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $content, $summary, $image_url, $user['id'], $category_id, $status]);
            $_SESSION['success_message'] = "Article created successfully.";
        }
        redirect('/newsportal/admin/index.php');
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        redirect('/newsportal/admin/editor.php' . ($article_id > 0 ? "?id=$article_id" : ''));
    }

} else {
    redirect('/newsportal/admin/index.php');
}
?>
