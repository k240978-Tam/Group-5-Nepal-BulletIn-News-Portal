<div class="row mb-4">
    <div class="col-12">
        <h2 class="category-header border-bottom pb-2 mb-4">
            Search Results for: <span class="text-danger">"<?= htmlspecialchars($query) ?>"</span>
        </h2>
    </div>
</div>

<div class="row">
    <?php if (empty($articles)): ?>
        <div class="col-12">
            <div class="alert alert-info">No articles found matching your search.</div>
        </div>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 article-card border-0 shadow-sm">
                    <?php if ($article['image_url']): ?>
                        <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($article['image_url'], '/') ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="badge badge-danger mb-2"><?= htmlspecialchars($article['category_name']) ?></span>
                        <h4 class="card-title">
                            <a href="<?= \App\Core\App::get('config')['url'] ?>/article?id=<?= $article['id'] ?>" class="text-dark text-decoration-none">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                        </h4>
                        <p class="card-text text-muted"><?= htmlspecialchars(substr($article['summary'], 0, 100)) ?>...</p>
                    </div>
                    <div class="card-footer bg-white border-0 text-muted small">
                        <i class="far fa-clock"></i> <?= date('F j, Y', strtotime($article['created_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
