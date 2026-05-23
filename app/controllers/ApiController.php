<?php
namespace App\Controllers;
use App\Core\Controller;

class ApiController extends Controller {

    // IMPORTANT: Replace with your actual Google Gemini API Key
    private $gemini_api_key = "AIzaSyAUsXXq_yDNz1HWW1EwHvYMoQ6pcxrkdOQ";
    private $gemini_api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=";

    public function chat() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');

        if (empty($message)) {
            echo json_encode(['error' => 'Message cannot be empty.']);
            return;
        }

        // Call Gemini API
        $prompt = "You are a helpful assistant for a News Portal website. User says: " . $message;
        $response = $this->callGemini($prompt);

        if ($response) {
            echo json_encode(['reply' => $response]);
        } else {
            echo json_encode(['error' => 'Failed to connect to AI service. Please check your API key.']);
        }
    }

    public function summarize() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $article_id = (int)($input['article_id'] ?? 0);

        if ($article_id <= 0) {
            echo json_encode(['error' => 'Invalid article ID.']);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT content FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        $article = $stmt->fetch();

        if (!$article || empty(trim($article['content']))) {
            echo json_encode(['error' => 'Article content not found.']);
            return;
        }

        // Call Gemini API
        $prompt = "Please provide a concise, 3-bullet-point summary of the following news article:\n\n" . strip_tags($article['content']);
        $response = $this->callGemini($prompt);

        if ($response) {
            echo json_encode(['summary' => $response]);
        } else {
            echo json_encode(['error' => 'Failed to generate summary. Please check your API key.']);
        }
    }

    public function bulletin() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $period = $input['period'] ?? 'today';

        // Define time constraint
        if ($period === 'weekly') {
            $timeCondition = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $promptContext = "This is a weekly news bulletin. Provide a comprehensive summary of the major events from the past 7 days.";
        } else if ($period === 'monthly') {
            $timeCondition = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $promptContext = "This is a monthly news bulletin. Provide a broad overview of the key events from the past month.";
        } else {
            // Default to today
            $timeCondition = "DATE(created_at) = CURDATE()";
            $promptContext = "This is today's daily news bulletin. Summarize the most important news that happened today.";
        }

        // Fetch articles
        $stmt = $this->pdo->prepare("SELECT title, summary FROM articles WHERE status = 'published' AND $timeCondition ORDER BY created_at DESC LIMIT 15");
        $stmt->execute();
        $articles = $stmt->fetchAll();

        if (count($articles) === 0) {
            echo json_encode(['bulletin' => "No articles were published during this period. Check back later for more news!"]);
            return;
        }

        // Prepare context for Gemini
        $newsItems = [];
        foreach ($articles as $a) {
            $newsItems[] = "- " . $a['title'] . ": " . strip_tags((string)$a['summary']);
        }
        $newsText = implode("\n", $newsItems);

        $prompt = "You are a professional News Anchor for the Nepal Bulletin News Portal. \n";
        $prompt .= $promptContext . "\n\n";
        $prompt .= "Here are the headlines and excerpts from our published articles for this period:\n";
        $prompt .= $newsText . "\n\n";
        $prompt .= "Write a cohesive, engaging news digest (between 150 and 450 words) that reads naturally like a radio news broadcast. Group related stories if necessary. Do not mention that you are an AI. Speak directly to the listener.";

        $response = $this->callGemini($prompt);

        if ($response) {
            echo json_encode(['bulletin' => $response]);
        } else {
            echo json_encode(['error' => 'Failed to generate the bulletin. Please check your API key.']);
        }
    }

    private function callGemini($prompt) {
        if ($this->gemini_api_key === "YOUR_GEMINI_API_KEY_HERE") {
            return "Developer Note: You need to insert your actual Gemini API Key in App/Controllers/ApiController.php for the AI to function.";
        }

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($this->gemini_api_url . $this->gemini_api_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result, true);
        
        if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            return $json['candidates'][0]['content']['parts'][0]['text'];
        }
        
        return false;
    }
}
