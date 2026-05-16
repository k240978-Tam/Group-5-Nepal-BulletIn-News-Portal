<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Core\Database;

class PagesController extends Controller
{
    public function home()
    {
        $db = Database::getInstance()->getConnection();

        // Fetch hero articles (top 3 latest)
        $stmt = $db->query("SELECT a.id, a.title, a.summary, a.image_url, c.name as category_name 
                             FROM articles a 
                             LEFT JOIN categories c ON a.category_id = c.id 
                             WHERE a.status = 'published' 
                             ORDER BY a.created_at DESC LIMIT 3");
        $hero_articles = $stmt->fetchAll();

        // Recent Nepal News Across All Categories
        $stmt = $db->query("SELECT a.id, a.title, a.summary, a.image_url, a.created_at, c.name as category_name
                             FROM articles a
                             LEFT JOIN categories c ON a.category_id = c.id
                             WHERE a.status = 'published' AND a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                             ORDER BY a.created_at DESC
                             LIMIT 6");
        $recent_nepal_news = $stmt->fetchAll();

        // Fetch categories for rows
        $stmt = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll();

        $category_articles = [];
        foreach ($categories as $cat) {
            $stmt = $db->prepare("SELECT a.id, a.title, a.image_url, a.created_at 
                                   FROM articles a 
                                   WHERE a.category_id = ? AND a.status = 'published' 
                                   ORDER BY a.created_at DESC LIMIT 4");
            $stmt->execute([$cat['id']]);
            $articles = $stmt->fetchAll();
            if (count($articles) > 0) {
                $category_articles[$cat['id']] = $articles;
            }
        }

        return $this->view('pages.home', [
            'hero_articles' => $hero_articles,
            'recent_nepal_news' => $recent_nepal_news,
            'categories' => $categories,
            'category_articles' => $category_articles
        ]);
    }
}
