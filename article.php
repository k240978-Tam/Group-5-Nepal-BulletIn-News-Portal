<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) redirect('/newsportal/index.php');

$stmt = $pdo->prepare("SELECT a.*, u.name as author_name, c.name as category_name 
                       FROM articles a 
                       LEFT JOIN users u ON a.author_id = u.id 
                       LEFT JOIN categories c ON a.category_id = c.id 
                       WHERE a.id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    $_SESSION['error_message'] = "Article not found.";
    redirect('/newsportal/index.php');
}

// Security check: Only admins, editors, and the author can see non-published articles
if ($article['status'] !== 'published') {
    $user = get_logged_in_user();
    if (!$user || (!in_array($user['role'], ['admin', 'editor']) && $user['id'] != $article['author_id'])) {
        $_SESSION['error_message'] = "This article is not yet published.";
        redirect('/newsportal/index.php');
    }
    $isPreview = true;
} else {
    $isPreview = false;
}

// Increment views
$pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$id]);

// Fetch tags
$tagStmt = $pdo->prepare("SELECT t.name FROM tags t JOIN article_tags at2 ON t.id = at2.tag_id WHERE at2.article_id = ?");
$tagStmt->execute([$id]);
$tags = array_column($tagStmt->fetchAll(), 'name');

// Fetch comments
$stmt = $pdo->prepare("SELECT c.*, u.name as user_name FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = ? AND c.status = 'approved' ORDER BY c.created_at DESC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

// Related articles
$stmt = $pdo->prepare("SELECT id, title, image_url, created_at FROM articles WHERE category_id = ? AND id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$article['category_id'], $id]);
$related = $stmt->fetchAll();

// Recent News for Sidebar
$stmt = $pdo->prepare("SELECT id, title, image_url, created_at FROM articles WHERE id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$id]);
$recent_news = $stmt->fetchAll();

$accent   = htmlspecialchars($article['article_color'] ?? '#c0392b');
$img_pos  = $article['image_position'] ?? 'center';
$lang_map = ['en' => '🇬🇧 English', 'ne' => '🇳🇵 Nepali', 'bilingual' => '🌐 Bilingual'];
$type_map = ['standard'=>'','opinion'=>'💬 Opinion','interview'=>'🎤 Interview','photo_essay'=>'📷 Photo Essay','breaking'=>'🔴 Breaking'];
$word_count = str_word_count(strip_tags($article['content']));
$read_min   = max(1, ceil($word_count / 200));

// Meta description for SEO
$meta_desc = htmlspecialchars($article['meta_description'] ?? get_excerpt($article['content'], 155));

// Image position CSS
$img_styles = [
    'left'   => 'float:left; margin:0 1.5rem 1rem 0; max-width:45%;',
    'right'  => 'float:right; margin:0 0 1rem 1.5rem; max-width:45%;',
    'center' => 'display:block; margin:0 auto 1rem; max-width:100%;',
    'full'   => 'width:100%; margin-bottom:1rem;',
];
$img_style = $img_styles[$img_pos] ?? $img_styles['center'];

require_once 'includes/header.php';
?>
<!-- Dynamic accent color -->
<style>
:root { --accent: <?= $accent ?>; }
.article-title { color: var(--dark); }
.article-body h1,.article-body h2,.article-body h3,.article-body h4 { color: var(--accent); }
.article-body a { color: var(--accent); }
.article-body blockquote { border-left: 4px solid var(--accent); background: #fafafa; padding: 1rem 1.25rem; margin: 1.5rem 0; border-radius: 0 6px 6px 0; color: #555; font-style: italic; }
.category-badge { background: var(--accent); color: #fff; }
.art-type-badge { display:inline-block; font-size:.7rem; font-weight:700; padding:.2rem .55rem; border-radius:20px; background: color-mix(in srgb, var(--accent) 15%, white); color: var(--accent); margin-left:.5rem; vertical-align:middle; text-transform:uppercase; letter-spacing:.05em; }
.tag-link { display:inline-block; background:#f1f5f9; color:#475569; font-size:.75rem; padding:.2rem .6rem; border-radius:20px; text-decoration:none; margin:.2rem; transition:background .2s; }
.tag-link:hover { background: color-mix(in srgb, var(--accent) 15%, white); color: var(--accent); }
.lang-badge { display:inline-flex; align-items:center; gap:.25rem; font-size:.72rem; color:#64748b; background:#f8fafc; border:1px solid #e2e8f0; padding:.2rem .5rem; border-radius:20px; }
.img-caption { text-align:center; font-size:.78rem; color:#64748b; font-style:italic; margin-top:.35rem; margin-bottom:1rem; }
.featured-img-wrap { margin-bottom:1.5rem; }
.clearfix::after { content:''; display:table; clear:both; }
</style>

<div class="row mt-4">
    <div class="col-lg-8 pr-lg-5">
        <div class="article-header">
    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.5rem">
        <span class="category-badge"><?= htmlspecialchars($article['category_name']) ?></span>
        <?php if (!empty($article['article_type']) && $article['article_type'] !== 'standard'): ?>
            <span class="art-type-badge"><?= $type_map[$article['article_type']] ?? '' ?></span>
        <?php endif; ?>
        <?php if (!empty($article['language']) && $article['language'] !== 'en'): ?>
            <span class="lang-badge"><?= $lang_map[$article['language']] ?? '' ?></span>
        <?php endif; ?>
        <?php if (!empty($article['is_featured'])): ?>
            <span style="font-size:.72rem;color:#f59e0b;background:#fef3c7;border:1px solid #fde68a;padding:.2rem .5rem;border-radius:20px;">⭐ Featured</span>
        <?php endif; ?>
    </div>

    <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>

    <?php if ($isPreview): ?>
        <div style="background:#fff7ed; border:1px solid #ffedd5; color:#9a3412; padding:.75rem 1rem; border-radius:8px; margin-top:1rem; display:flex; align-items:center; gap:.5rem; font-size:.9rem;">
            <i class="fas fa-eye"></i>
            <strong>Preview Mode:</strong> This article is currently <u><?= $article['status'] ?></u> and not visible to the public.
        </div>
    <?php endif; ?>

    <div class="article-author-meta">
        <div>
            <strong><?= htmlspecialchars($article['author_name']) ?></strong><br>
            <span style="font-size:.88rem;color:#64748b">
                <?= date('F j, Y', strtotime($article['created_at'])) ?>
                &bull; <?= $read_min ?> min read
                &bull; <?= number_format($article['views']) ?> views
            </span>
        </div>
        <div style="margin-left:auto; display:flex; gap:.4rem;">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn btn-primary" style="padding:.3rem .6rem"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($article['title']) ?>" target="_blank" class="btn" style="background:#1DA1F2;color:#fff;padding:.3rem .6rem"><i class="fab fa-twitter"></i></a>
            <a href="https://wa.me/?text=<?= urlencode($article['title'].' http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn" style="background:#25D366;color:#fff;padding:.3rem .6rem"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>
</div>

<?php if ($article['image_url']): ?>
<div class="featured-img-wrap">
    <img src="<?= htmlspecialchars($article['image_url']) ?>"
         alt="<?= htmlspecialchars($article['title']) ?>"
         style="<?= $img_style ?> border-radius:8px;">
    <?php if (!empty($article['image_caption'])): ?>
        <p class="img-caption"><?= htmlspecialchars($article['image_caption']) ?></p>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- AI Smart Summary Component -->
<style>
.ai-summary-container {
    perspective: 1000px;
    margin-bottom: 2rem;
}
.ai-summary-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.12), 0 2px 8px -1px rgba(148, 163, 184, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.ai-summary-card:hover {
    box-shadow: 0 10px 25px -3px rgba(148, 163, 184, 0.18), 0 4px 12px -2px rgba(148, 163, 184, 0.12);
    border-color: color-mix(in srgb, var(--accent, #c0392b) 25%, rgba(226, 232, 240, 0.8));
}
.ai-summary-header {
    margin-bottom: 0.75rem;
    border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    padding-bottom: 0.75rem;
}
.ai-summary-badge {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, color-mix(in srgb, var(--accent, #c0392b) 10%, white) 0%, color-mix(in srgb, var(--accent, #c0392b) 20%, white) 100%);
    color: var(--accent, #c0392b);
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.35rem 0.8rem;
    border-radius: 30px;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.4);
    border: 1px solid color-mix(in srgb, var(--accent, #c0392b) 30%, rgba(255,255,255,0));
}
.ai-summary-badge .shimmer-effect {
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
    transform: skewX(-25deg);
    animation: badge-shimmer 3s infinite;
}
@keyframes badge-shimmer {
    0% { left: -150%; }
    30% { left: 150%; }
    100% { left: 150%; }
}
.ai-summary-body {
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 500px;
    overflow: hidden;
}
.ai-summary-body.collapsed {
    max-height: 0 !important;
    padding-top: 0;
    padding-bottom: 0;
    margin-top: 0;
}
.summary-bullets-content {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    padding: 0.25rem 0;
    animation: fadeInSummary 0.5s ease-out forwards;
}
@keyframes fadeInSummary {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.summary-bullet-item {
    gap: 0.75rem;
}
.summary-bullet-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent, #c0392b);
    font-size: 0.45rem;
    margin-top: 0.55rem;
    opacity: 0.85;
}
.summary-bullet-text {
    font-size: 0.96rem;
    line-height: 1.6;
    color: #334155;
    font-weight: 450;
}
.generate-summary-btn {
    background: linear-gradient(135deg, var(--accent, #c0392b) 0%, color-mix(in srgb, var(--accent, #c0392b) 85%, black) 100%);
    color: white !important;
    border: none;
    border-radius: 30px;
    font-size: 0.88rem;
    font-weight: 600;
    padding: 0.55rem 1.35rem;
    box-shadow: 0 4px 12px rgba(192, 57, 43, 0.25);
    cursor: pointer;
    transition: all 0.2s ease;
}
.generate-summary-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(192, 57, 43, 0.35);
}
.generate-summary-btn:active {
    transform: translateY(0);
}

/* Skeleton Loader */
.summary-skeleton {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 0.5rem 0;
}
.skeleton-line {
    height: 12px;
    background: #e2e8f0;
    border-radius: 6px;
    width: 100%;
}
.skeleton-line.shimmer {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: skeleton-pulse 1.5s infinite;
}
@keyframes skeleton-pulse {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<div class="ai-summary-container">
    <div class="ai-summary-card">
        <div class="ai-summary-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center" style="gap: 0.75rem;">
                <span class="ai-summary-badge">
                    <span class="shimmer-effect"></span>
                    <i class="fas fa-magic mr-1"></i> Smart Summary
                </span>
                <small class="text-muted summary-status-info" style="font-size: 0.78rem; font-weight: 500;">
                    <?php if (!empty($article['ai_summary'])): ?>
                        ✓ Cached
                    <?php endif; ?>
                </small>
            </div>
            <div class="d-flex align-items-center" style="gap: 0.5rem;">
                <button class="btn btn-sm btn-outline-secondary read-summary-btn" id="read-summary-btn" style="border-radius: 20px; font-size: 0.78rem; padding: 0.2rem 0.65rem; display: <?= !empty($article['ai_summary']) ? 'inline-flex' : 'none' ?>;" title="Listen to Summary">
                    <i class="fas fa-volume-up mr-1" id="read-summary-icon"></i> <span id="read-summary-text">Listen</span>
                </button>
                <button class="btn btn-sm btn-outline-secondary toggle-summary-btn" id="toggle-summary-btn" style="border-radius: 20px; font-size: 0.78rem; padding: 0.2rem 0.65rem;">
                    <i class="fas fa-chevron-up mr-1" id="toggle-summary-icon"></i> <span id="toggle-summary-text">Hide</span>
                </button>
            </div>
        </div>
        
        <div class="ai-summary-body" id="ai-summary-body">
            <!-- If summary already exists in DB, render it! -->
            <?php if (!empty($article['ai_summary'])): ?>
                <div class="summary-bullets-content">
                    <?php 
                    $bullets = explode("\n", $article['ai_summary']);
                    foreach ($bullets as $bullet):
                        $clean = ltrim(trim($bullet), '•* ');
                        if (empty($clean)) continue;
                    ?>
                        <div class="summary-bullet-item d-flex align-items-start">
                            <span class="summary-bullet-icon"><i class="fas fa-circle"></i></span>
                            <span class="summary-bullet-text"><?= htmlspecialchars($clean) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Placeholder / Trigger -->
                <div id="summary-placeholder-state" class="py-2">
                    <p class="text-muted mb-3" style="font-size: 0.95rem;">Get a quick, informative 3-point summary of this article instantly.</p>
                    <button class="btn generate-summary-btn" id="generate-summary-btn">
                        <i class="fas fa-wand-magic-sparkles mr-2"></i> Generate Smart Summary
                    </button>
                </div>
                
                <!-- Skeleton Loader -->
                <div id="summary-skeleton-loader" class="summary-skeleton" style="display: none;">
                    <div class="skeleton-line shimmer"></div>
                    <div class="skeleton-line shimmer" style="width: 85%;"></div>
                    <div class="skeleton-line shimmer" style="width: 90%;"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generate-summary-btn');
    const toggleBtn = document.getElementById('toggle-summary-btn');
    const toggleIcon = document.getElementById('toggle-summary-icon');
    const toggleText = document.getElementById('toggle-summary-text');
    const summaryBody = document.getElementById('ai-summary-body');
    const placeholderState = document.getElementById('summary-placeholder-state');
    const skeletonLoader = document.getElementById('summary-skeleton-loader');
    const statusInfo = document.querySelector('.summary-status-info');
    
    // Voice Reader variables
    const readBtn = document.getElementById('read-summary-btn');
    const readIcon = document.getElementById('read-summary-icon');
    const readText = document.getElementById('read-summary-text');
    let isSpeaking = false;
    let currentUtterance = null;

    // Warm up speech synthesis voices cache immediately on load
    if ('speechSynthesis' in window) {
        window.speechSynthesis.getVoices();
        if (window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = () => {
                window.speechSynthesis.getVoices();
            };
        }
    }

    // Helper to find the most premium, natural, enhanced neural voice on client browser
    function getAdvancedVoice(lang) {
        const voices = window.speechSynthesis.getVoices();
        if (!voices || voices.length === 0) return null;

        const langPattern = lang.split('-')[0].toLowerCase();
        const langVoices = voices.filter(v => v.lang.toLowerCase().startsWith(langPattern));

        if (langVoices.length === 0) return null;

        // Keywords in voice names indicating state-of-the-art neural/high-quality profiles
        const premiumKeywords = ['natural', 'premium', 'google', 'siri', 'enhanced', 'samantha', 'daniel', 'karen', 'moira'];
        
        langVoices.sort((a, b) => {
            const aName = a.name.toLowerCase();
            const bName = b.name.toLowerCase();
            
            let aIdx = premiumKeywords.findIndex(kw => aName.includes(kw));
            let bIdx = premiumKeywords.findIndex(kw => bName.includes(kw));
            
            if (aIdx === -1) aIdx = 999;
            if (bIdx === -1) bIdx = 999;
            
            return aIdx - bIdx;
        });

        return langVoices[0];
    }

    // Toggle Expand/Collapse
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isCollapsed = summaryBody.classList.contains('collapsed');
            if (isCollapsed) {
                summaryBody.classList.remove('collapsed');
                toggleIcon.className = 'fas fa-chevron-up mr-1';
                toggleText.textContent = 'Hide';
            } else {
                summaryBody.classList.add('collapsed');
                toggleIcon.className = 'fas fa-chevron-down mr-1';
                toggleText.textContent = 'Show';
            }
        });
    }

    // Voice Reader speech trigger
    if (readBtn) {
        readBtn.addEventListener('click', function() {
            if (isSpeaking) {
                stopSpeaking();
            } else {
                startSpeaking();
            }
        });
    }

    function startSpeaking() {
        const bulletElements = summaryBody.querySelectorAll('.summary-bullet-text');
        if (bulletElements.length === 0) return;

        let speechText = "";
        bulletElements.forEach((el, i) => {
            speechText += "Point " + (i + 1) + ": " + el.textContent + ". ";
        });

        if (!speechText) return;

        window.speechSynthesis.cancel(); // Terminate current plays

        currentUtterance = new SpeechSynthesisUtterance(speechText);
        
        // Dynamic Lang Detector: Check for Devanagari (Nepali) Unicode block
        const isNepali = /[\u0900-\u097F]/.test(speechText);
        const langCode = isNepali ? 'ne-NP' : 'en-US';
        
        currentUtterance.lang = langCode;
        currentUtterance.rate = 0.95; // Slower, clear professional reading speed

        // Bind advanced natural voice if available
        const bestVoice = getAdvancedVoice(langCode);
        if (bestVoice) {
            currentUtterance.voice = bestVoice;
        }

        currentUtterance.onstart = function() {
            isSpeaking = true;
            readBtn.classList.remove('btn-outline-secondary');
            readBtn.classList.add('btn-danger');
            readIcon.className = 'fas fa-stop mr-1';
            readText.textContent = 'Stop';
        };

        currentUtterance.onend = function() {
            resetReadButton();
        };

        currentUtterance.onerror = function() {
            resetReadButton();
        };

        window.speechSynthesis.speak(currentUtterance);
    }

    function stopSpeaking() {
        window.speechSynthesis.cancel();
        resetReadButton();
    }

    function resetReadButton() {
        isSpeaking = false;
        if (readBtn) {
            readBtn.classList.remove('btn-danger');
            readBtn.classList.add('btn-outline-secondary');
            readIcon.className = 'fas fa-volume-up mr-1';
            readText.textContent = 'Listen';
        }
    }

    // Cancel speech on page unload so it doesn't leak
    window.addEventListener('beforeunload', () => {
        window.speechSynthesis.cancel();
    });

    // AJAX Generator
    if (generateBtn) {
        generateBtn.addEventListener('click', async function() {
            placeholderState.style.display = 'none';
            skeletonLoader.style.display = 'flex';
            if (statusInfo) statusInfo.textContent = 'Generating...';

            try {
                const response = await fetch('/newsportal/actions/summarize.php?article_id=<?= $id ?>');
                const data = await response.json();

                skeletonLoader.style.display = 'none';

                if (data.error) {
                    if (statusInfo) statusInfo.textContent = 'Error';
                    summaryBody.innerHTML = `<div class="alert alert-danger mb-0">${data.error}</div>`;
                } else if (data.summary) {
                    if (statusInfo) {
                        statusInfo.textContent = data.method === 'api' ? '✓ Generated by AI' : '✓ Extracted by Smart NLP';
                    }
                    
                    // Render bullets
                    const bullets = data.summary.split('\n');
                    let html = '<div class="summary-bullets-content">';
                    bullets.forEach(bullet => {
                        let clean = bullet.trim();
                        if (clean.startsWith('•') || clean.startsWith('*')) {
                            clean = clean.substring(1).trim();
                        }
                        if (clean.length > 0) {
                            html += `
                                <div class="summary-bullet-item d-flex align-items-start">
                                    <span class="summary-bullet-icon"><i class="fas fa-circle"></i></span>
                                    <span class="summary-bullet-text">${escapeHtml(clean)}</span>
                                </div>
                            `;
                        }
                    });
                    html += '</div>';
                    summaryBody.innerHTML = html;

                    // Show the speech reader button now that text content is rendered!
                    if (readBtn) {
                        readBtn.style.display = 'inline-flex';
                    }
                } else {
                    if (statusInfo) statusInfo.textContent = 'Failed';
                    summaryBody.innerHTML = `<div class="alert alert-danger mb-0">Failed to generate summary.</div>`;
                }
            } catch (error) {
                skeletonLoader.style.display = 'none';
                if (statusInfo) statusInfo.textContent = 'Error';
                summaryBody.innerHTML = `<div class="alert alert-danger mb-0">Connection failed. Please try again.</div>`;
            }
        });
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>

<!-- Google Translate Widget -->
<div id="google_translate_element" style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end; align-items: center; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
    <i class="fas fa-language" style="color: var(--accent); font-size: 1.2rem; margin-right: 0.5rem;"></i>
    <span style="font-size: 0.85rem; font-weight: 600; color: #64748b; margin-right: 1rem;">Translate Article:</span>
</div>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<div class="article-body clearfix">
    <?= $article['content'] ?>
</div>

<!-- Tags -->
<?php if (!empty($tags)): ?>
<div style="margin-top:2rem; padding-top:1rem; border-top:1px solid #f1f5f9;">
    <span style="font-size:.78rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">Tags:</span>
    <?php foreach($tags as $tag): ?>
        <a href="/newsportal/search.php?q=<?= urlencode($tag) ?>" class="tag-link">#<?= htmlspecialchars($tag) ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<hr style="margin:3rem 0;border:0;border-top:1px solid #e2e8f0;">

<!-- Comments -->
<div>
    <h3 class="mb-2">Comments (<?= count($comments) ?>)</h3>
    <?php if (is_logged_in()): ?>
        <form action="/newsportal/actions/process_comment.php" method="POST" class="mb-2">
            <input type="hidden" name="article_id" value="<?= $id ?>">
            <div class="form-group">
                <textarea name="content" class="form-control" rows="3" placeholder="Write your comment here..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Comment</button>
        </form>
    <?php else: ?>
        <div class="alert alert-danger" style="background:var(--light);color:var(--dark);border-color:var(--gray)">
            Please <a href="/newsportal/login.php" class="text-primary">login</a> or <a href="/newsportal/register.php" class="text-primary">register</a> to comment.
        </div>
    <?php endif; ?>
    <div style="display:flex;flex-direction:column;gap:1.5rem;margin-top:2rem">
        <?php foreach($comments as $c): ?>
        <div style="background:var(--white);padding:1rem;border-radius:var(--radius-md);box-shadow:var(--shadow-sm)">
            <div style="display:flex;justify-content:space-between;margin-bottom:.5rem">
                <strong><?= htmlspecialchars($c['user_name']) ?></strong>
                <span class="text-gray"><?= time_elapsed_string($c['created_at']) ?></span>
            </div>
            <p><?= nl2br(htmlspecialchars($c['content'])) ?></p>
        </div>
        <?php endforeach; ?>
        <?php if(!$comments): ?><p class="text-gray">No comments yet. Be the first!</p><?php endif; ?>
    </div>
</div>

<!-- Related Articles -->
<?php if ($related): ?>
<div style="margin-top:4rem">
    <h3 class="mb-2">You Might Also Like</h3>
    <div class="row">
        <?php foreach($related as $r): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 article-card">
                <a href="/newsportal/article.php?id=<?= $r['id'] ?>" style="text-decoration:none;color:inherit">
                    <img src="<?= $r['image_url'] ? htmlspecialchars($r['image_url']) : 'https://placehold.co/300x200?text=News' ?>" class="card-img-top" style="height:180px;object-fit:cover">
                    <div class="card-body">
                        <h5 class="card-title" style="font-family:var(--font-serif);font-size:1rem;line-height:1.4"><?= htmlspecialchars($r['title']) ?></h5>
                        <small class="text-muted"><?= time_elapsed_string($r['created_at']) ?></small>
                    </div>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
    </div> <!-- End col-lg-8 -->
    
    <!-- Sidebar -->
    <div class="col-lg-4 mt-5 mt-lg-0">
        <div class="sidebar-recent-news sticky-top" style="top: 100px; padding-top: 1rem;">
            <h4 class="mb-4" style="border-bottom: 2px solid var(--accent); padding-bottom: 0.5rem; display: inline-block; font-family: var(--font-serif);">Recent News</h4>
            <div class="recent-news-list">
                <?php foreach($recent_news as $news): ?>
                <div class="d-flex mb-4 align-items-center">
                    <a href="/newsportal/article.php?id=<?= $news['id'] ?>" class="flex-shrink-0">
                        <img src="<?= $news['image_url'] ? htmlspecialchars($news['image_url']) : 'https://placehold.co/100x100?text=News' ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; box-shadow: var(--shadow-sm);">
                    </a>
                    <div>
                        <h6 class="mb-1" style="font-size: 0.95rem; line-height: 1.3;">
                            <a href="/newsportal/article.php?id=<?= $news['id'] ?>" class="text-dark font-weight-bold" style="text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color=''">
                                <?= htmlspecialchars($news['title']) ?>
                            </a>
                        </h6>
                        <small class="text-muted"><i class="far fa-clock mr-1"></i><?= time_elapsed_string($news['created_at']) ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div> <!-- End col-lg-4 -->
</div> <!-- End row -->

<?php require_once 'includes/footer.php'; ?>
