<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

// Fetch breaking news
$stmt = $pdo->query("SELECT id, title FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 5");
$breaking_news = $stmt->fetchAll();

// Fetch hero articles (top 3 latest)
$stmt = $pdo->query("SELECT a.id, a.title, a.summary, a.image_url, c.name as category_name 
                     FROM articles a 
                     LEFT JOIN categories c ON a.category_id = c.id 
                     WHERE a.status = 'published' 
                     ORDER BY a.created_at DESC LIMIT 3");
$hero_articles = $stmt->fetchAll();

// Fetch categories for rows
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<?php if (count($hero_articles) > 0): ?>
<section class="hero">
    <div class="hero-grid">
        <!-- Main Hero -->
        <?php $main_hero = $hero_articles[0]; ?>
        <a href="article.php?id=<?= $main_hero['id'] ?>" class="hero-card">
            <img src="<?= $main_hero['image_url'] ? htmlspecialchars($main_hero['image_url']) : 'https://placehold.co/800x400?text=News' ?>" alt="Hero Image">
            <div class="hero-overlay">
                <span class="category-badge"><?= htmlspecialchars($main_hero['category_name']) ?></span>
                <h2><?= htmlspecialchars($main_hero['title']) ?></h2>
                <p><?= htmlspecialchars($main_hero['summary'] ?? get_excerpt($main_hero['title'], 60)) ?></p>
            </div>
        </a>
        
        <!-- Side Heros -->
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <?php for ($i = 1; $i < count($hero_articles); $i++): $side_hero = $hero_articles[$i]; ?>
            <a href="article.php?id=<?= $side_hero['id'] ?>" class="hero-card small">
                <img src="<?= $side_hero['image_url'] ? htmlspecialchars($side_hero['image_url']) : 'https://placehold.co/400x200?text=News' ?>" alt="Side Hero">
                <div class="hero-overlay">
                    <span class="category-badge"><?= htmlspecialchars($side_hero['category_name']) ?></span>
                    <h2><?= htmlspecialchars($side_hero['title']) ?></h2>
                </div>
            </a>
            <?php endfor; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Recent Nepal News Across All Categories -->
<?php
$stmt = $pdo->query("SELECT a.id, a.title, a.summary, a.image_url, a.created_at, c.name as category_name
                     FROM articles a
                     LEFT JOIN categories c ON a.category_id = c.id
                     WHERE a.status = 'published' AND a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                     ORDER BY a.created_at DESC
                     LIMIT 6");
$recent_nepal_news = $stmt->fetchAll();
?>

<?php if (count($recent_nepal_news) > 0): ?>
<section class="category-row">
    <div class="section-title">
        <h3>Recent Nepal News</h3>
        <span class="text-gray" style="font-size: 0.95rem;">Latest articles from all categories published in the last 7 days.</span>
    </div>
    
    <div class="row">
        <?php foreach ($recent_nepal_news as $article): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 article-card" style="transition: transform 0.2s;">
                <a href="article.php?id=<?= $article['id'] ?>" style="text-decoration:none; color:inherit;">
                    <img src="<?= $article['image_url'] ? htmlspecialchars($article['image_url']) : 'https://placehold.co/300x200?text=News' ?>" alt="Recent Article" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <span class="badge badge-danger mb-2" style="align-self: flex-start;"><?= htmlspecialchars($article['category_name'] ?? 'Nepal') ?></span>
                        <h5 class="card-title" style="font-family: var(--font-serif); color: var(--dark);"><?= htmlspecialchars($article['title']) ?></h5>
                        <p class="card-text text-muted mb-3"><?= htmlspecialchars($article['summary'] ?? get_excerpt($article['title'], 70)) ?></p>
                        <div class="mt-auto text-muted" style="font-size: 0.8rem;">
                            <span><?= time_elapsed_string($article['created_at']) ?></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Category Rows -->
<?php foreach ($categories as $cat): ?>
    <?php
    $stmt = $pdo->prepare("SELECT a.id, a.title, a.image_url, a.created_at 
                           FROM articles a 
                           WHERE a.category_id = ? AND a.status = 'published' 
                           ORDER BY a.created_at DESC LIMIT 4");
    $stmt->execute([$cat['id']]);
    $cat_articles = $stmt->fetchAll();
    
    if (count($cat_articles) > 0):
    ?>
    <section class="category-row">
        <div class="section-title">
            <h3><?= htmlspecialchars($cat['name']) ?></h3>
            <a href="category.php?id=<?= $cat['id'] ?>" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
        </div>
        
        <div class="row">
            <?php foreach ($cat_articles as $article): ?>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm border-0 article-card" style="transition: transform 0.2s;">
                    <a href="article.php?id=<?= $article['id'] ?>" style="text-decoration:none; color:inherit;">
                        <img src="<?= $article['image_url'] ? htmlspecialchars($article['image_url']) : 'https://placehold.co/300x200?text=News' ?>" alt="Thumbnail" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title" style="font-family: var(--font-serif); font-size: 1.1rem; line-height: 1.4; color: var(--dark);"><?= htmlspecialchars($article['title']) ?></h5>
                            <div class="mt-auto d-flex justify-content-between text-muted" style="font-size: 0.8rem;">
                                <span><?= time_elapsed_string($article['created_at']) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
<?php endforeach; ?>

<?php require_once 'includes/footer.php'; ?>

<!-- Chatbot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <button id="chatbot-toggle" class="chatbot-toggle">
        <i class="fas fa-comment-dots"></i>
    </button>
    <div id="chatbot-window" class="chatbot-window" style="display: none;">
        <div class="chatbot-header">
            <h5>Nepal Bulletin Assistant</h5>
            <button id="chatbot-close" class="chatbot-close">&times;</button>
        </div>
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="message bot">Hello! I'm the Nepal Bulletin Assistant. How can I help you today?</div>
        </div>
        <div class="chatbot-input-area">
            <input type="text" id="chatbot-input" placeholder="Type a message...">
            <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 25px;
    right: 25px;
    z-index: 9999;
    font-family: var(--font-sans, sans-serif);
}
.chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    color: white;
    border: none;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(192, 57, 43, 0.4);
    cursor: pointer;
    transition: transform 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chatbot-toggle:hover {
    transform: scale(1.05);
}
.chatbot-window {
    position: absolute;
    bottom: 75px;
    right: 0;
    width: 340px;
    height: 450px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}
.chatbot-header {
    background: linear-gradient(135deg, #c0392b, #e74c3c);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.chatbot-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}
.chatbot-close {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    opacity: 0.8;
    line-height: 1;
}
.chatbot-close:hover {
    opacity: 1;
}
.chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.message {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
}
.message.bot {
    background: #e2e8f0;
    color: #1e293b;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}
.message.user {
    background: #c0392b;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}
.chatbot-input-area {
    padding: 12px;
    background: white;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
}
.chatbot-input-area input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
}
.chatbot-input-area input:focus {
    border-color: #c0392b;
}
.chatbot-input-area button {
    background: #c0392b;
    color: white;
    border: none;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.chatbot-input-area button:hover {
    background: #a93226;
}
.chatbot-loading {
    font-size: 12px;
    color: #94a3b8;
    align-self: flex-start;
    margin-left: 5px;
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const chatWindow = document.getElementById('chatbot-window');
    const sendBtn = document.getElementById('chatbot-send');
    const chatInput = document.getElementById('chatbot-input');
    const messagesContainer = document.getElementById('chatbot-messages');

    // Add loading indicator element
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'chatbot-loading';
    loadingIndicator.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Typing...';
    messagesContainer.appendChild(loadingIndicator);

    toggleBtn.addEventListener('click', () => {
        chatWindow.style.display = chatWindow.style.display === 'none' ? 'flex' : 'none';
        if (chatWindow.style.display === 'flex') chatInput.focus();
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.style.display = 'none';
    });

    function appendMessage(text, sender) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${sender}`;
        msgDiv.textContent = text;
        messagesContainer.insertBefore(msgDiv, loadingIndicator);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        appendMessage(text, 'user');
        chatInput.value = '';
        loadingIndicator.style.display = 'block';
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        try {
            const response = await fetch('/newsportal/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();
            loadingIndicator.style.display = 'none';

            if (data.error) {
                appendMessage(data.error, 'bot');
            } else if (data.reply) {
                appendMessage(data.reply, 'bot');
            } else {
                appendMessage('Sorry, I received an invalid response.', 'bot');
            }
        } catch (error) {
            loadingIndicator.style.display = 'none';
            appendMessage('Connection error. Please try again later.', 'bot');
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
</script>
