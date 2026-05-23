<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-8">
        <h2 class="mb-4 pb-2 border-bottom border-danger">Latest News</h2>
        
        <?php if (count($latestArticles) > 0): ?>
            <?php foreach ($latestArticles as $article): ?>
                <div class="card mb-4 shadow-sm border-0">
                    <?php if (!empty($article['image_url'])): ?>
                        <img src="<?= htmlspecialchars($article['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>" style="max-height: 400px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if (!empty($article['category_name'])): ?>
                            <span class="badge badge-danger mb-2"><?= htmlspecialchars($article['category_name']) ?></span>
                        <?php endif; ?>
                        
                        <h3 class="card-title h4">
                            <a href="/newsportal/article?id=<?= $article['id'] ?>" class="text-dark text-decoration-none">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                        </h3>
                        
                        <p class="card-text text-muted">
                            <?= htmlspecialchars(get_excerpt($article['content'], 150)) ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                By <?= htmlspecialchars($article['author_name'] ?? 'Unknown') ?> &bull; 
                                <?= time_elapsed_string($article['created_at']) ?>
                            </small>
                            <a href="/newsportal/article?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-danger">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No news articles found. Please check back later.</div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <!-- Sidebar -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-danger text-white font-weight-bold">
                <i class="fas fa-fire mr-2"></i> Trending News
            </div>
            <ul class="list-group list-group-flush">
                <?php if (count($trendingArticles) > 0): ?>
                    <?php foreach ($trendingArticles as $trend): ?>
                        <li class="list-group-item">
                            <a href="/newsportal/article?id=<?= $trend['id'] ?>" class="text-dark font-weight-bold text-decoration-none">
                                <?= htmlspecialchars($trend['title']) ?>
                            </a>
                            <br>
                            <small class="text-muted"><i class="far fa-clock"></i> <?= time_elapsed_string($trend['created_at']) ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-muted">No trending articles yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
