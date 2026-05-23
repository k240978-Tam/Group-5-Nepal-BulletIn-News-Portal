<?php
// Utility functions for Nepal Bulletin News Portal

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function get_excerpt($content, $limit = 100) {
    $content = strip_tags($content);
    if (strlen($content) <= $limit) {
        return $content;
    }
    return substr($content, 0, $limit) . '...';
}

function log_action($action, $details = null) {
    global $pdo;
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $details]);
}

function ensure_ai_summary_column() {
    global $pdo;
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM articles LIKE 'ai_summary'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE articles ADD COLUMN ai_summary TEXT NULL");
        }
    } catch (Exception $e) {
        // Fallback or ignore if table alter fails
    }
}

function generate_extractive_summary($content, $sentenceCount = 3) {
    $text = strip_tags($content);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);

    if (empty($text)) {
        return '';
    }

    $sentences = preg_split('/(?<=[.!?।])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    
    if (count($sentences) <= $sentenceCount) {
        return implode(' ', $sentences);
    }

    $stopWords = [
        'the', 'a', 'an', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
        'have', 'has', 'had', 'do', 'does', 'did', 'to', 'from', 'in', 'on', 'at', 'by', 'for',
        'with', 'about', 'against', 'between', 'into', 'through', 'during', 'before', 'after',
        'above', 'below', 'up', 'down', 'out', 'off', 'over', 'under', 'again', 'further',
        'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both',
        'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only',
        'own', 'same', 'so', 'than', 'too', 'very', 'can', 'will', 'just', 'should', 'would',
        'this', 'that', 'these', 'those', 'their', 'they', 'them', 'he', 'she', 'it', 'its',
        
        'र', 'छ', 'छन्', 'का', 'को', 'मा', 'ने', 'ले', 'म', 'हामी', 'ऊ', 'तिनी', 'तिनीहरू',
        'यो', 'त्यो', 'यस', 'त्यस', 'सबै', 'जुन', 'भने', 'पनि', 'तर', 'भनेर', 'गरिएको',
        'गर्ने', 'भयो', 'गरे', 'गर्नु', 'गरेका', 'तथा', 'अथवा', 'हुन्', 'हुनुहुन्छ', 'थिइन्',
        'थिए', 'थियो', 'हो', 'होइन', 'भए', 'भएका', 'रहेका', 'रहेको', 'बारे', 'लागि', 'द्वारा'
    ];

    $wordFrequencies = [];
    foreach ($sentences as $sentence) {
        $cleanSentence = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($sentence, 'UTF-8'));
        $words = preg_split('/\s+/u', $cleanSentence, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($words as $word) {
            if (mb_strlen($word, 'UTF-8') < 3) continue;
            if (in_array($word, $stopWords)) continue;
            
            if (!isset($wordFrequencies[$word])) {
                $wordFrequencies[$word] = 0;
            }
            $wordFrequencies[$word]++;
        }
    }

    $sentenceScores = [];
    foreach ($sentences as $index => $sentence) {
        $cleanSentence = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($sentence, 'UTF-8'));
        $words = preg_split('/\s+/u', $cleanSentence, -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = count($words);

        if ($wordCount < 4) {
            $sentenceScores[$index] = -1; 
            continue;
        }

        $score = 0;
        foreach ($words as $word) {
            if (isset($wordFrequencies[$word])) {
                $score += $wordFrequencies[$word];
            }
        }

        $optimalLength = 17;
        $deviation = abs($optimalLength - $wordCount);
        $lengthFactor = 1 + ($deviation * 0.08); 
        
        $sentenceScores[$index] = $score / $lengthFactor;
    }

    arsort($sentenceScores);
    $topIndices = array_slice(array_keys($sentenceScores), 0, $sentenceCount, true);
    sort($topIndices); 

    $summarySentences = [];
    foreach ($topIndices as $index) {
        $summarySentences[] = trim($sentences[$index]);
    }

    return implode(' ', $summarySentences);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_logged_in_user() {
    global $pdo;
    if (is_logged_in() && $pdo) {
        $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}
?>
