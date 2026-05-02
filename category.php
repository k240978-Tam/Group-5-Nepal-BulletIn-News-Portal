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
    redirect('/newsportal/index.php');
}

$category = $stmt->fetch();

if (!$category) {
    redirect('/newsportal/index.php');
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

<div class="row mb-5">
    <?php foreach ($articles as $article): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 article-card" style="transition: transform 0.2s;">
                <a href="article.php?id=<?= $article['id'] ?>" style="text-decoration:none; color:inherit;">
                    <img src="<?= $article['image_url'] ? htmlspecialchars($article['image_url']) : 'https://placehold.co/300x200?text=News' ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
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
            <i class="fas fa-folder-open text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <h3 class="text-muted">No articles found in this category.</h3>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
