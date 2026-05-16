<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $articleModel = new Article();
        $categoryModel = new Category();

        $breaking_news = $articleModel->getBreakingNews();
        $hero_articles = $articleModel->getHeroArticles();
        $recent_nepal_news = $articleModel->getRecentNews();
        $categories = $categoryModel->getAll();

        // Pass these variables to the view
        $this->render('pages/home', [
            'breaking_news' => $breaking_news,
            'hero_articles' => $hero_articles,
            'recent_nepal_news' => $recent_nepal_news,
            'categories' => $categories,
            'articleModel' => $articleModel // To fetch category-specific articles in the view
        ]);
    }
}
