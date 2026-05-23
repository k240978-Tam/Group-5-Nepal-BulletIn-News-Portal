<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mt-4">
    <div class="col-md-10 offset-md-1">
        
        <div class="card bg-light border-0 mb-4 py-4">
            <div class="card-body text-center">
                <h3 class="mb-3">Search Results</h3>
                <form action="/newsportal/search" method="GET" class="form-inline justify-content-center">
                    <input class="form-control form-control-lg mr-2 w-50" type="search" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search news..." required>
                    <button type="submit" class="btn btn-lg btn-danger"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
        </div>

        <?php if (!empty($search_query)): ?>
            <p class="text-muted">Found <?= count($searchResults) ?> result(s) for "<strong><?= htmlspecialchars($search_query) ?></strong>"</p>
            
            <?php if (count($searchResults) > 0): ?>
                <?php foreach ($searchResults as $article): ?>
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="row no-gutters">
                            <?php if (!empty($article['image_url'])): ?>
                            <div class="col-md-4">
                                <img src="<?= htmlspecialchars($article['image_url']) ?>" class="card-img h-100" style="object-fit:cover;" alt="<?= htmlspecialchars($article['title']) ?>">
                            </div>
                            <?php endif; ?>
                            <div class="<?= !empty($article['image_url']) ? 'col-md-8' : 'col-md-12' ?>">
                                <div class="card-body">
                                    <?php if (!empty($article['category_name'])): ?>
                                        <span class="badge badge-danger mb-2"><?= htmlspecialchars($article['category_name']) ?></span>
                                    <?php endif; ?>
                                    <h4 class="card-title">
                                        <a href="/newsportal/article?id=<?= $article['id'] ?>" class="text-dark text-decoration-none">
                                            <?= htmlspecialchars($article['title']) ?>
                                        </a>
                                    </h4>
                                    <p class="card-text text-muted">
                                        <?= htmlspecialchars(get_excerpt($article['content'], 180)) ?>
                                    </p>
                                    <p class="card-text"><small class="text-muted">By <?= htmlspecialchars($article['author_name'] ?? 'Unknown') ?> &bull; <?= time_elapsed_string($article['created_at']) ?></small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-circle fa-3x mb-3 text-warning d-block"></i>
                    <h5>No matches found</h5>
                    <p>We couldn't find anything matching "<?= htmlspecialchars($search_query) ?>". Try different or more general keywords.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
