<div class="row">
    <div class="col-lg-8">
        <!-- Article Content -->
        <article class="bg-white p-4 rounded shadow-sm mb-4">
            <?php if ($isPreview): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> This is a preview. This article is currently <strong><?= htmlspecialchars($article['status']) ?></strong>.
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <span class="badge badge-danger"><?= htmlspecialchars($article['category_name']) ?></span>
                <span class="text-muted ml-2"><i class="far fa-clock"></i> <?= date('F j, Y, g:i a', strtotime($article['created_at'])) ?></span>
                <span class="text-muted ml-2"><i class="far fa-eye"></i> <?= number_format($article['views']) ?> views</span>
            </div>

            <h1 class="h2 font-weight-bold mb-3"><?= htmlspecialchars($article['title']) ?></h1>
            
            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                <div class="mr-3">
                    <?php if (!empty($article['author_avatar'])): ?>
                        <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($article['author_avatar'], '/') ?>" class="rounded-circle" width="50" height="50" alt="Author">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <h6 class="mb-0 font-weight-bold">By <?= htmlspecialchars($article['author_name']) ?></h6>
                    <small class="text-muted">Journalist</small>
                </div>
            </div>

            <?php if ($article['image_url']): ?>
                <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($article['image_url'], '/') ?>" class="img-fluid rounded mb-4 w-100" alt="<?= htmlspecialchars($article['title']) ?>" style="max-height: 500px; object-fit: cover;">
            <?php endif; ?>

            <div class="article-body" style="font-size: 1.1rem; line-height: 1.8;">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>

            <?php if (!empty($tags)): ?>
                <div class="mt-4 pt-3 border-top">
                    <h5 class="mb-3">Tags:</h5>
                    <?php foreach ($tags as $tag): ?>
                        <span class="badge badge-light border mr-1 p-2"><?= htmlspecialchars($tag['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <!-- Comments Section -->
        <div class="bg-white p-4 rounded shadow-sm mb-4" id="comments">
            <h4 class="border-bottom pb-2 mb-4">Comments (<?= count($comments) ?>)</h4>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="<?= \App\Core\App::get('config')['url'] ?>/comment" method="POST" class="mb-4">
                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                    <div class="form-group">
                        <textarea class="form-control" name="content" rows="3" placeholder="Leave a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Post Comment</button>
                </form>
            <?php else: ?>
                <div class="alert alert-light border mb-4">
                    Please <a href="<?= \App\Core\App::get('config')['url'] ?>/login" class="text-danger font-weight-bold">login</a> to leave a comment.
                </div>
            <?php endif; ?>

            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="media mb-4 pb-3 border-bottom">
                            <?php if (!empty($comment['user_avatar'])): ?>
                                <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($comment['user_avatar'], '/') ?>" class="mr-3 rounded-circle" width="45" height="45" alt="User">
                            <?php else: ?>
                                <div class="mr-3 bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 45px; height: 45px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <div class="media-body">
                                <h6 class="mt-0 font-weight-bold"><?= htmlspecialchars($comment['user_name']) ?> <small class="text-muted ml-2"><?= date('M j, Y, g:i a', strtotime($comment['created_at'])) ?></small></h6>
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Related Articles -->
        <?php if (!empty($related)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="font-weight-bold border-bottom pb-2">Related News</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($related as $rel): ?>
                        <div class="media mb-3 pb-3 border-bottom">
                            <?php if ($rel['image_url']): ?>
                                <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($rel['image_url'], '/') ?>" class="mr-3 rounded" width="80" height="60" style="object-fit: cover;" alt="...">
                            <?php endif; ?>
                            <div class="media-body">
                                <h6 class="mt-0 mb-1" style="font-size: 0.95rem; line-height: 1.3;">
                                    <a href="<?= \App\Core\App::get('config')['url'] ?>/article?id=<?= $rel['id'] ?>" class="text-dark font-weight-bold text-decoration-none">
                                        <?= htmlspecialchars($rel['title']) ?>
                                    </a>
                                </h6>
                                <small class="text-muted"><?= date('M j, Y', strtotime($rel['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Recent News -->
        <?php if (!empty($recent_news)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="font-weight-bold border-bottom pb-2">Recent Updates</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recent_news as $news): ?>
                            <li class="mb-3 pb-2 border-bottom">
                                <span class="text-danger small font-weight-bold text-uppercase d-block mb-1"><?= htmlspecialchars($news['category_name']) ?></span>
                                <a href="<?= \App\Core\App::get('config')['url'] ?>/article?id=<?= $news['id'] ?>" class="text-dark font-weight-bold text-decoration-none d-block mb-1" style="line-height: 1.3;">
                                    <?= htmlspecialchars($news['title']) ?>
                                </a>
                                <small class="text-muted"><i class="far fa-clock"></i> <?= date('g:i a, M j', strtotime($news['created_at'])) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
