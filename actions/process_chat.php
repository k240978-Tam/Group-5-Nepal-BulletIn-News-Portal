<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

$api_key = getenv('OPENAI_API_KEY') ?: '';

// Fetch some context from the database to "teach" the chatbot
$categories = $pdo->query("SELECT name FROM categories")->fetchAll(PDO::FETCH_COLUMN);
$article_count = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
$latest_news = $pdo->query("SELECT title FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$context = "You are the AI Assistant for Nepal Bulletin Board, a premium news portal. 
Current Site Info:
- Categories: " . implode(', ', $categories) . ".
- Total Published Articles: $article_count.
- Total Registered Users: $users_count.
- Latest News: " . implode(' | ', $latest_news) . ".

Rules:
1. Answer questions about the news portal, categories, and latest updates.
2. Be professional, helpful, and concise.
3. If asked about technical details not provided, say you are a news assistant.
4. If a user asks for articles, recommend the latest ones listed above.";

$data = [
    'model' => 'openai/gpt-3.5-turbo', 
    'messages' => [
        ['role' => 'system', 'content' => $context],
        ['role' => 'user', 'content' => $user_message]
    ]
];

$ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json',
    'HTTP-Referer: http://localhost/newsportal',
    'X-Title: Nepal Bulletin Board'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['error' => 'Connection error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

if ($http_code !== 200) {
    $err_details = json_decode($response, true);
    $err_msg = $err_details['error']['message'] ?? 'API Error ' . $http_code;
    echo json_encode(['error' => $err_msg]);
    exit;
}

$result = json_decode($response, true);
$reply = $result['choices'][0]['message']['content'] ?? 'I am sorry, I am having trouble thinking right now.';

echo json_encode(['reply' => $reply]);
