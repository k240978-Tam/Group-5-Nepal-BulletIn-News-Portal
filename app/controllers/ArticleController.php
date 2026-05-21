<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;

class ArticleController extends Controller
{
    public function show(int $id = null)
    {
        if ($id === null) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        }

        if ($id <= 0) {
            $this->redirect('');
        }

        require_once ROOT_PATH . '/includes/functions.php';
        ensure_ai_summary_column();

        $articleModel = new Article();
        $article = $articleModel->findByIdWithDetails($id);

        if (!$article) {
            $_SESSION['error_message'] = "Article not found.";
            $this->redirect('');
        }

        // Security check for non-published articles
        $isPreview = false;
        if ($article['status'] !== 'published') {
            if (!is_logged_in()) {
                $_SESSION['error_message'] = "This article is not yet published.";
                $this->redirect('');
            }

            $user_id = $_SESSION['user_id'];
            $role = $_SESSION['role'];

            if (!in_array($role, ['admin', 'editor']) && $user_id != $article['author_id']) {
                $_SESSION['error_message'] = "This article is not yet published.";
                $this->redirect('');
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

    public function summarize()
    {
        header('Content-Type: application/json');
        
        $articleId = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
        if ($articleId <= 0) {
            echo json_encode(['error' => 'Invalid article ID.']);
            return;
        }

        require_once ROOT_PATH . '/includes/functions.php';
        ensure_ai_summary_column();

        try {
            $articleModel = new \App\Models\Article();
            $article = $articleModel->findByIdWithDetails($articleId);

            if (!$article) {
                echo json_encode(['error' => 'Article not found.']);
                return;
            }

            if (!empty($article['ai_summary'])) {
                echo json_encode(['summary' => $article['ai_summary'], 'cached' => true]);
                return;
            }

            // Generate using same hybrid logic!
            $apiKey = getenv('OPENAI_API_KEY');
            if (empty($apiKey) && file_exists(ROOT_PATH . '/.env')) {
                $envContent = file_get_contents(ROOT_PATH . '/.env');
                if (preg_match('/^OPENAI_API_KEY\s*=\s*["\']?([^"\']+)["\']?/m', $envContent, $matches)) {
                    $apiKey = trim($matches[1]);
                }
            }

            $summary = '';
            $methodUsed = 'local';

            if (!empty($apiKey)) {
                $endpoint = 'https://openrouter.ai/api/v1/chat/completions';
                $prompt = "You are a professional assistant on a premium news portal. Provide a concise and highly informative summary of the following news article. Your output MUST consist of exactly 3 bullet points, each starting with '• '. Keep each bullet point brief (10-25 words max). CRITICAL: You MUST write the summary in the SAME language as the article (e.g. if the article is in Nepali, write the summary in Nepali; if English, write in English).\n\nArticle Title: " . $article['title'] . "\n\nArticle Content:\n" . strip_tags($article['content']);

                $data = [
                    'model' => 'google/gemini-2.5-flash:free',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.3
                ];

                $ch = curl_init($endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                    'HTTP-Referer: http://localhost/newsportal',
                    'X-Title: Nepal Bulletin Board'
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if (!$curlError && $httpCode === 200) {
                    $responseData = json_decode($response, true);
                    $aiText = $responseData['choices'][0]['message']['content'] ?? '';
                    if (!empty($aiText)) {
                        $summary = trim($aiText);
                        $methodUsed = 'api';
                    }
                }
            }

            if (empty($summary)) {
                $extractedText = generate_extractive_summary($article['content'], 3);
                $sentences = preg_split('/(?<=[.!?।])\s+/u', $extractedText, -1, PREG_SPLIT_NO_EMPTY);
                $bullets = [];
                foreach ($sentences as $sentence) {
                    $trimmed = trim($sentence);
                    if (!empty($trimmed)) {
                        $bullets[] = "• " . $trimmed;
                    }
                }
                $summary = implode("\n", $bullets);
                $methodUsed = 'local';
            }

            $db = \App\Core\Database::getInstance()->getConnection();
            $updateStmt = $db->prepare("UPDATE articles SET ai_summary = ? WHERE id = ?");
            $updateStmt->execute([$summary, $articleId]);

            echo json_encode([
                'summary' => $summary,
                'cached' => false,
                'method' => $methodUsed
            ]);

        } catch (\Exception $e) {
            echo json_encode(['error' => 'An error occurred during summarization: ' . $e->getMessage()]);
        }
    }

    public function category(int $id = null)
    {
        $name = null;
        if ($id === null) {
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
            } elseif (isset($_GET['name'])) {
                $name = sanitize_input($_GET['name']);
            }
        }

        $categoryModel = new Category();
        if ($id > 0) {
            $category = $categoryModel->find($id);
        } elseif (!empty($name)) {
            $category = $categoryModel->findByName($name);
        } else {
            $this->redirect('');
        }

        if (!$category) {
            $this->redirect('');
        }

        $articleModel = new Article();
        $articles = $articleModel->getArticlesByCategory($category['id'], 50); // Fetch up to 50 for now

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
