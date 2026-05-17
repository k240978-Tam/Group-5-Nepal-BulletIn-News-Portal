<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\App;

class ChatController extends Controller
{
    public function sendMessage()
    {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? '';

        if (empty($message)) {
            echo json_encode(['error' => 'Message cannot be empty.']);
            return;
        }

        // Use the OpenRouter API key from environment configuration
        $apiKey = \App\Core\App::get('config')['openai_api_key'] ?? '';
        
        // Fetch context from the database
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $categories = $db->query("SELECT name FROM categories")->fetchAll(\PDO::FETCH_COLUMN);
            $article_count = $db->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
            $latest_news = $db->query("SELECT title FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 5")->fetchAll(\PDO::FETCH_COLUMN);
            $users_count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            
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
        } catch (\Exception $e) {
            $context = "You are a helpful, concise, and friendly news assistant for Nepal Bulletin.";
        }

        $endpoint = 'https://openrouter.ai/api/v1/chat/completions';
        
        $data = [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $context
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ]
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'HTTP-Referer: ' . (\App\Core\App::get('config')['url'] ?? 'http://localhost/newsportal'),
            'X-Title: Nepal Bulletin Board'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo json_encode(['error' => 'Failed to communicate with AI: ' . $curlError]);
            return;
        }

        $responseData = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMsg = $responseData['error']['message'] ?? 'Unknown API Error';
            echo json_encode(['error' => 'API Error: ' . $errorMsg]);
            return;
        }

        $reply = $responseData['choices'][0]['message']['content'] ?? 'No response received.';
        
        echo json_encode(['reply' => $reply]);
    }
}
