<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$name = isset($_GET['name']) ? sanitize_input($_GET['name']) : '';

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
} elseif (!empty($name)) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE name = ?");
    $stmt->execute([$name]);
} else {
    redirect('/index.php');
}

$category = $stmt->fetch();

if (!$category) {
    redirect('/index.php');
}

// Fetch articles for this category
$stmt = $pdo->prepare("SELECT a.id, a.title, a.summary, a.image_url, a.created_at 
                       FROM articles a 
                       WHERE a.category_id = ? AND a.status = 'published' 
                       ORDER BY a.created_at DESC");
$stmt->execute([$category['id']]);
$articles = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div style="margin: 2rem 0; text-align: center;">
    <h1 style="font-family: var(--font-serif); color: var(--dark); margin-bottom: 0.5rem;"><?= htmlspecialchars($category['name']) ?></h1>
    <?php if (!empty($category['description'])): ?>
        <p class="text-gray" style="max-width: 600px; margin: 0 auto;"><?= htmlspecialchars($category['description']) ?></p>
    <?php endif; ?>
</div>

<div class="article-grid" style="margin-bottom: 4rem;">
    <?php foreach ($articles as $article): ?>
        <a href="article.php?id=<?= $article['id'] ?>" class="article-card">
            <img src="<?= $article['image_url'] ? htmlspecialchars($article['image_url']) : 'https://placehold.co/300x200?text=News' ?>" class="article-thumb">
            <div class="article-content">
                <h4><?= htmlspecialchars($article['title']) ?></h4>
                <p class="text-gray mb-1"><?= htmlspecialchars($article['summary'] ?? get_excerpt($article['title'], 60)) ?></p>
                <div class="article-meta">
                    <span><?= time_elapsed_string($article['created_at']) ?></span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
    
    <?php if (count($articles) === 0): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
            <i class="fas fa-folder-open text-gray" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <h3 class="text-gray">No articles found in this category.</h3>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
