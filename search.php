<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

$query = isset($_GET['q']) ? sanitize_input($_GET['q']) : '';

if (empty($query)) {
    redirect('/newsportal/index.php');
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

<div class="row mb-5">
    <?php foreach ($articles as $article): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 article-card" style="transition: transform 0.2s;">
                <a href="article.php?id=<?= $article['id'] ?>" style="text-decoration:none; color:inherit;">
                    <img src="<?= $article['image_url'] ? htmlspecialchars($article['image_url']) : 'https://placehold.co/300x200?text=News' ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <span class="badge badge-danger mb-2" style="align-self: flex-start;"><?= htmlspecialchars($article['category_name']) ?></span>
                        <h5 class="card-title" style="font-family: var(--font-serif); color: var(--dark);"><?= htmlspecialchars($article['title']) ?></h5>
                        <p class="card-text text-muted mb-3"><?= htmlspecialchars($article['summary'] ?? get_excerpt($article['title'], 60)) ?></p>
                        <div class="mt-auto text-muted" style="font-size: 0.8rem;">
                            <span><?= time_elapsed_string($article['created_at']) ?></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (count($articles) === 0): ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-search text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <h3 class="text-muted">No articles found matching your query.</h3>
            <p class="text-muted mt-2">Try different keywords or browse our categories.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
