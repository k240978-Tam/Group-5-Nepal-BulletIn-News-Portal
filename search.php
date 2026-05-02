<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

$query = isset($_GET['q']) ? sanitize_input($_GET['q']) : '';

if (empty($query)) {
    redirect('/index.php');
}

// Search articles
$searchTerm = "%$query%";
$stmt = $pdo->prepare("SELECT a.id, a.title, a.summary, a.image_url, a.created_at, c.name as category_name
                       FROM articles a 
                       LEFT JOIN categories c ON a.category_id = c.id
                       WHERE (a.title LIKE ? OR a.content LIKE ?) AND a.status = 'published' 
                       ORDER BY a.created_at DESC");
$stmt->execute([$searchTerm, $searchTerm]);
$articles = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div style="margin: 2rem 0;">
    <h2 style="font-family: var(--font-serif); color: var(--dark);">Search Results</h2>
    <p class="text-gray" style="font-size: 1.1rem;"><?= count($articles) ?> result(s) found for "<strong><?= htmlspecialchars($query) ?></strong>"</p>
</div>

<div class="article-grid" style="margin-bottom: 4rem;">
    <?php foreach ($articles as $article): ?>
        <a href="article.php?id=<?= $article['id'] ?>" class="article-card">
            <img src="<?= $article['image_url'] ? htmlspecialchars($article['image_url']) : 'https://placehold.co/300x200?text=News' ?>" class="article-thumb">
            <div class="article-content">
                <span class="category-badge mb-1" style="align-self: flex-start;"><?= htmlspecialchars($article['category_name']) ?></span>
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
            <i class="fas fa-search text-gray" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <h3 class="text-gray">No articles found matching your query.</h3>
            <p class="text-gray mt-2">Try different keywords or browse our categories.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
