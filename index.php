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
