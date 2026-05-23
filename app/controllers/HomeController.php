<?php
namespace App\Controllers;
use App\Core\Controller;

class HomeController extends Controller {
    
    public function index() {
        $stmtLatest = $this->pdo->query("
            SELECT a.id, a.title, a.content, a.image_url, a.created_at, 
                   c.name as category_name, u.name as author_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.status = 'published' 
            ORDER BY a.created_at DESC 
            LIMIT 10
        ");
        $latestArticles = $stmtLatest->fetchAll();

        $stmtTrending = $this->pdo->query("
            SELECT id, title, created_at 
            FROM articles 
            WHERE status = 'published' 
            ORDER BY views DESC, created_at DESC 
            LIMIT 5
        ");
        $trendingArticles = $stmtTrending->fetchAll();

        $this->view('home/index', [
            'latestArticles' => $latestArticles,
            'trendingArticles' => $trendingArticles
        ]);
    }

    public function category() {
        $category_name = isset($_GET['name']) ? trim($_GET['name']) : '';

        if (empty($category_name)) {
            $this->view('home/category', ['error' => 'Category not specified.']);
            return;
        }

        $stmtCat = $this->pdo->prepare("SELECT id, name, description FROM categories WHERE name = ?");
        $stmtCat->execute([$category_name]);
        $category = $stmtCat->fetch();

        if (!$category) {
            $this->view('home/category', ['error' => 'Category not found.']);
            return;
        }

        $stmtArticles = $this->pdo->prepare("
            SELECT a.id, a.title, a.content, a.image_url, a.created_at, u.name as author_name 
            FROM articles a 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.category_id = ? AND a.status = 'published' 
            ORDER BY a.created_at DESC 
            LIMIT 20
        ");
        $stmtArticles->execute([$category['id']]);
        $articles = $stmtArticles->fetchAll();

        $stmtAllCats = $this->pdo->query("SELECT name, (SELECT COUNT(*) FROM articles WHERE category_id = categories.id AND status = 'published') as article_count FROM categories ORDER BY name ASC");
        $allCategories = $stmtAllCats->fetchAll();

        $this->view('home/category', [
            'category' => $category,
            'articles' => $articles,
            'allCategories' => $allCategories
        ]);
    }

    public function search() {
        $search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $searchResults = [];

        if (!empty($search_query)) {
            $searchTerm = '%' . $search_query . '%';
            $stmtSearch = $this->pdo->prepare("
                SELECT a.id, a.title, a.content, a.image_url, a.created_at, 
                       c.name as category_name, u.name as author_name 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE a.status = 'published' AND (a.title LIKE ? OR a.content LIKE ?) 
                ORDER BY a.created_at DESC 
                LIMIT 50
            ");
            $stmtSearch->execute([$searchTerm, $searchTerm]);
            $searchResults = $stmtSearch->fetchAll();
        }

        $this->view('home/search', [
            'search_query' => $search_query,
            'searchResults' => $searchResults
        ]);
    }
}
