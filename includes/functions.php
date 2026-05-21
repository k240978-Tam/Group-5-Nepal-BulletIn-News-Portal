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

/**
 * Ensures that the ai_summary column exists in the articles table.
 */
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

/**
 * Generates a high-quality local extractive summary from an article's content.
 * Works offline, 100% free, and handles both English and Nepali text.
 */
function generate_extractive_summary($content, $sentenceCount = 3) {
    // 1. Clean the input text
    $text = strip_tags($content);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);

    if (empty($text)) {
        return '';
    }

    // 2. Sentence tokenization (supports English .!? and Nepali ।)
    $sentences = preg_split('/(?<=[.!?।])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    
    // If we have fewer sentences than requested, return the whole text
    if (count($sentences) <= $sentenceCount) {
        return implode(' ', $sentences);
    }

    // 3. Compile stop words for filtering
    $stopWords = [
        // English stop words
        'the', 'a', 'an', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
        'have', 'has', 'had', 'do', 'does', 'did', 'to', 'from', 'in', 'on', 'at', 'by', 'for',
        'with', 'about', 'against', 'between', 'into', 'through', 'during', 'before', 'after',
        'above', 'below', 'up', 'down', 'out', 'off', 'over', 'under', 'again', 'further',
        'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both',
        'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only',
        'own', 'same', 'so', 'than', 'too', 'very', 'can', 'will', 'just', 'should', 'would',
        'this', 'that', 'these', 'those', 'their', 'they', 'them', 'he', 'she', 'it', 'its',
        
        // Nepali stop words
        'र', 'छ', 'छन्', 'का', 'को', 'मा', 'ने', 'ले', 'म', 'हामी', 'ऊ', 'तिनी', 'तिनीहरू',
        'यो', 'त्यो', 'यस', 'त्यस', 'सबै', 'जुन', 'भने', 'पनि', 'तर', 'भनेर', 'गरिएको',
        'गर्ने', 'भयो', 'गरे', 'गर्नु', 'गरेका', 'तथा', 'अथवा', 'हुन्', 'हुनुहुन्छ', 'थिइन्',
        'थिए', 'थियो', 'हो', 'होइन', 'भए', 'भएका', 'रहेका', 'रहेको', 'बारे', 'लागि', 'द्वारा'
    ];

    // 4. Calculate word frequencies (excluding stop words and punctuation)
    $wordFrequencies = [];
    foreach ($sentences as $sentence) {
        // Clean sentence to words
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

    // 5. Score sentences based on word frequencies and optimal length heuristic
    $sentenceScores = [];
    foreach ($sentences as $index => $sentence) {
        $cleanSentence = preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($sentence, 'UTF-8'));
        $words = preg_split('/\s+/u', $cleanSentence, -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = count($words);

        if ($wordCount < 4) {
            $sentenceScores[$index] = -1; // Ignore very short sentences (headers, captions)
            continue;
        }

        $score = 0;
        foreach ($words as $word) {
            if (isset($wordFrequencies[$word])) {
                $score += $wordFrequencies[$word];
            }
        }

        // Length heuristic: Sweet spot is 12-22 words. Penalize too short or too long.
        $optimalLength = 17;
        $deviation = abs($optimalLength - $wordCount);
        $lengthFactor = 1 + ($deviation * 0.08); // penalize deviation from optimal
        
        $sentenceScores[$index] = $score / $lengthFactor;
    }

    // 6. Select top sentences and sort them back into chronological order
    arsort($sentenceScores);
    $topIndices = array_slice(array_keys($sentenceScores), 0, $sentenceCount, true);
    sort($topIndices); // Sort back to chronological order of the original text

    $summarySentences = [];
    foreach ($topIndices as $index) {
        $summarySentences[] = trim($sentences[$index]);
    }

    return implode(' ', $summarySentences);
}
?>
