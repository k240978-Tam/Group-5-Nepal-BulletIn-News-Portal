<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;

class ArticleController extends Controller
{
    public function show(int $id)
    {
        if ($id <= 0) {
            $this->redirect(APP_URL . '/');
        }

        $articleModel = new Article();
        $article = $articleModel->findByIdWithDetails($id);

        if (!$article) {
            $_SESSION['error_message'] = "Article not found.";
            $this->redirect(APP_URL . '/');
        }

        // Security check for non-published articles
        $isPreview = false;
        if ($article['status'] !== 'published') {
            if (!is_logged_in()) {
                $_SESSION['error_message'] = "This article is not yet published.";
                $this->redirect(APP_URL . '/');
            }
            
            $user_id = $_SESSION['user_id'];
            $role = $_SESSION['role'];
            
            if (!in_array($role, ['admin', 'editor']) && $user_id != $article['author_id']) {
                $_SESSION['error_message'] = "This article is not yet published.";
                $this->redirect(APP_URL . '/');
            }
            $isPreview = true;
        }

        // Increment views
        $articleModel->incrementViews($id);

        // Fetch related data
        $tags = $articleModel->getTags($id);
        $comments = $articleModel->getComments($id);
        $related = $articleModel->getRelatedArticles($article['category_id'], $id);
        $recent_news = $articleModel->getRecentNewsExcluding(5, $id);

        $this->render('pages/article', [
            'article' => $article,
            'isPreview' => $isPreview,
            'tags' => $tags,
            'comments' => $comments,
            'related' => $related,
            'recent_news' => $recent_news
        ]);
    }

    public function category(int $id)
    {
        if ($id <= 0) {
            $this->redirect(APP_URL . '/');
        }

        $categoryModel = new Category();
        $category = $categoryModel->findById($id);

        if (!$category) {
            $this->redirect(APP_URL . '/');
        }

        $articleModel = new Article();
        $articles = $articleModel->getArticlesByCategory($id, 50); // Fetch up to 50 for now

        $this->render('pages/category', [
            'category' => $category,
            'articles' => $articles
        ]);
    }

    public function search()
    {
        $query = sanitize_input($_GET['q'] ?? '');
        
        $articleModel = new Article();
        $articles = $articleModel->search($query);

        $this->render('pages/search', [
            'query' => $query,
            'articles' => $articles
        ]);
    }
}
