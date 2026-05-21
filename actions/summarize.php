<?php
/**
 * AJAX Endpoint for Generating and Fetching AI Summary
 * Handles both procedural requests and can be called directly or via controller.
 */

header('Content-Type: application/json');

// Bootstrapping: Load database and helper functions
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure the db schema is up to date (adds ai_summary column automatically)
ensure_ai_summary_column();

$articleId = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
if ($articleId <= 0) {
    echo json_encode(['error' => 'Invalid article ID.']);
    exit;
}

try {
    // 1. Check database cache
    $stmt = $pdo->prepare("SELECT title, content, ai_summary FROM articles WHERE id = ?");
    $stmt->execute([$articleId]);
    $article = $stmt->fetch();

    if (!$article) {
        echo json_encode(['error' => 'Article not found.']);
        exit;
    }

    if (!empty($article['ai_summary'])) {
        echo json_encode(['summary' => $article['ai_summary'], 'cached' => true]);
        exit;
    }

    // 2. No cache found, let's find the OpenRouter API Key
    $apiKey = getenv('OPENAI_API_KEY');
    if (empty($apiKey) && file_exists(__DIR__ . '/../.env')) {
        $envContent = file_get_contents(__DIR__ . '/../.env');
        if (preg_match('/^OPENAI_API_KEY\s*=\s*["\']?([^"\']+)["\']?/m', $envContent, $matches)) {
            $apiKey = trim($matches[1]);
        }
    }

    $summary = '';
    $methodUsed = 'local';

    if (!empty($apiKey)) {
        // We have an API key! Attempt to use OpenRouter's completely free Gemini 2.5 Flash model
        $endpoint = 'https://openrouter.ai/api/v1/chat/completions';
        
        $prompt = "You are a professional assistant on a premium news portal. "
                . "Provide a concise and highly informative summary of the following news article. "
                . "Your output MUST consist of exactly 3 bullet points, each starting with '• ' (the bullet point character). "
                . "Keep each bullet point brief (10-25 words max). "
                . "CRITICAL: You MUST write the summary in the SAME language as the article (e.g. if the article is in Nepali, write the summary in Nepali; if English, write in English).\n\n"
                . "Article Title: " . $article['title'] . "\n\n"
                . "Article Content:\n" . strip_tags($article['content']);

        $data = [
            'model' => 'google/gemini-2.5-flash:free',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
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

    // 3. Fallback to Local NLP Extractor if API is not set up, fails, or is rate-limited
    if (empty($summary)) {
        $extractedText = generate_extractive_summary($article['content'], 3);
        
        // Format extracted sentences cleanly into bullet points to match the premium style
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

    // 4. Save to database cache
    $updateStmt = $pdo->prepare("UPDATE articles SET ai_summary = ? WHERE id = ?");
    $updateStmt->execute([$summary, $articleId]);

    echo json_encode([
        'summary' => $summary,
        'cached' => false,
        'method' => $methodUsed
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred during summarization: ' . $e->getMessage()]);
}
