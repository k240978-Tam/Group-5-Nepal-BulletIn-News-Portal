<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-8">
        <h2 class="mb-2 pb-2 border-bottom border-danger">News in <?= htmlspecialchars($category['name']) ?></h2>
        <?php if (!empty($category['description'])): ?>
            <p class="text-muted mb-4"><?= htmlspecialchars($category['description']) ?></p>
        <?php endif; ?>
        
        <?php if (count($articles) > 0): ?>
            <div class="row">
                <?php foreach ($articles as $article): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <?php if (!empty($article['image_url'])): ?>
                                <img src="<?= htmlspecialchars($article['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="/newsportal/article?id=<?= $article['id'] ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted flex-grow-1">
                                    <?= htmlspecialchars(get_excerpt($article['content'], 100)) ?>
                                </p>
                                <div class="mt-auto">
                                    <small class="text-muted">
                                        <?= time_elapsed_string($article['created_at']) ?> &bull; By <?= htmlspecialchars($article['author_name'] ?? 'Unknown') ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No articles found in this category yet.</div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <!-- Sidebar -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-danger text-white font-weight-bold">
                <i class="fas fa-list mr-2"></i> Categories
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($allCategories as $cat): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?= ($cat['name'] === $category['name']) ? 'bg-light' : '' ?>">
                        <a href="/newsportal/category?name=<?= urlencode($cat['name']) ?>" class="text-dark text-decoration-none <?= ($cat['name'] === $category['name']) ? 'font-weight-bold text-danger' : '' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                        <span class="badge badge-secondary badge-pill"><?= $cat['article_count'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
