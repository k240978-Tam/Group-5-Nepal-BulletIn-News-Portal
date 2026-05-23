<?php
namespace App\Controllers;
use App\Core\Controller;

class ArticleController extends Controller {

    public function show() {
        $article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($article_id <= 0) {
            $this->view('article/show', ['error' => 'Invalid article ID.']);
            return;
        }

        $stmt = $this->pdo->prepare("
            SELECT a.*, c.name as category_name, u.name as author_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.id = ? AND a.status = 'published'
        ");
        $stmt->execute([$article_id]);
        $article = $stmt->fetch();

        if (!$article) {
            $this->view('article/show', ['error' => 'Article not found or is not published yet.']);
            return;
        }

        $this->pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$article_id]);

        $stmtComments = $this->pdo->prepare("
            SELECT c.content, c.created_at, u.name as user_name 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.article_id = ? AND c.status = 'approved' 
            ORDER BY c.created_at DESC
        ");
        $stmtComments->execute([$article_id]);
        $comments = $stmtComments->fetchAll();

        $this->view('article/show', [
            'article_id' => $article_id,
            'article' => $article,
            'comments' => $comments
        ]);
    }

    public function comment() {
        $this->requireRole(); // Must be logged in
        
        $article_id = (int)($_POST['article_id'] ?? 0);
        $comment_text = trim($_POST['comment'] ?? '');
        $user_id = $_SESSION['user_id'];

        if ($article_id <= 0 || empty($comment_text)) {
            $_SESSION['error_message'] = "Invalid comment data.";
            $this->redirect('/newsportal/article?id=' . $article_id);
        }

        $stmt = $this->pdo->prepare("INSERT INTO comments (article_id, user_id, content, status) VALUES (?, ?, ?, 'approved')");
        $stmt->execute([$article_id, $user_id, $comment_text]);
        
        $_SESSION['success_message'] = "Your comment has been posted.";
        $this->redirect('/newsportal/article?id=' . $article_id);
    }
}
