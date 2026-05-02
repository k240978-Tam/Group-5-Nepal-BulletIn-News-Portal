<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('/newsportal/index.php');
}

// Fetch Article
$stmt = $pdo->prepare("SELECT a.*, u.name as author_name, c.name as category_name 
                       FROM articles a 
                       LEFT JOIN users u ON a.author_id = u.id 
                       LEFT JOIN categories c ON a.category_id = c.id 
                       WHERE a.id = ? AND a.status = 'published'");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    $_SESSION['error_message'] = "Article not found.";
    redirect('/newsportal/index.php');
}

// Update views
$pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?")->execute([$id]);

// Fetch comments
$stmt = $pdo->prepare("SELECT c.*, u.name as user_name 
                       FROM comments c 
                       LEFT JOIN users u ON c.user_id = u.id 
                       WHERE c.article_id = ? AND c.status = 'approved' 
                       ORDER BY c.created_at DESC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

// Fetch Related Articles
$stmt = $pdo->prepare("SELECT id, title, image_url, created_at 
                       FROM articles 
                       WHERE category_id = ? AND id != ? AND status = 'published' 
                       ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$article['category_id'], $id]);
$related_articles = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="article-header">
    <span class="category-badge mb-1"><?= htmlspecialchars($article['category_name']) ?></span>
    <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
    
    <div class="article-author-meta">
        <div>
            <strong><?= htmlspecialchars($article['author_name']) ?></strong><br>
            <span style="font-size: 0.9rem;"><?= date('F j, Y', strtotime($article['created_at'])) ?> &bull; <?= ceil(str_word_count(strip_tags($article['content'])) / 200) ?> min read</span>
        </div>
        <div style="margin-left: auto;">
            <!-- Social Share Icons (Placeholder) -->
            <button class="btn btn-primary" style="padding: 0.3rem 0.6rem;"><i class="fab fa-facebook-f"></i></button>
            <button class="btn" style="background:#1DA1F2; color:#fff; padding: 0.3rem 0.6rem;"><i class="fab fa-twitter"></i></button>
            <button class="btn" style="background:#25D366; color:#fff; padding: 0.3rem 0.6rem;"><i class="fab fa-whatsapp"></i></button>
        </div>
    </div>
</div>

<?php if ($article['image_url']): ?>
    <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="Featured Image" class="article-featured-image">
<?php endif; ?>

<div class="article-body">
    <?= $article['content'] // Assuming content is trusted HTML from rich text editor ?>
</div>

<hr style="margin: 3rem 0; border: 0; border-top: 1px solid #ddd;">

<!-- Comments Section -->
<div style="max-width: 800px; margin: 0 auto;">
    <h3 class="mb-2">Comments (<?= count($comments) ?>)</h3>
    
    <?php if (is_logged_in()): ?>
        <form action="/newsportal/actions/process_comment.php" method="POST" class="mb-2">
            <input type="hidden" name="article_id" value="<?= $id ?>">
            <div class="form-group">
                <textarea name="content" class="form-control" rows="3" placeholder="Write your comment here..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Comment</button>
        </form>
    <?php else: ?>
        <div class="alert alert-danger" style="background-color: var(--light); color: var(--dark); border-color: var(--gray);">
            Please <a href="/newsportal/login.php" class="text-primary font-weight-500">login</a> or <a href="/newsportal/register.php" class="text-primary font-weight-500">register</a> to leave a comment.
        </div>
    <?php endif; ?>
    
    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem;">
        <?php foreach ($comments as $comment): ?>
            <div style="background: var(--white); padding: 1rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                    <span class="text-gray"><?= time_elapsed_string($comment['created_at']) ?></span>
                </div>
                <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (count($comments) === 0): ?>
            <p class="text-gray">No comments yet. Be the first to share your thoughts!</p>
        <?php endif; ?>
    </div>
</div>

<!-- Related Articles -->
<?php if (count($related_articles) > 0): ?>
<div style="margin-top: 4rem;">
    <h3 class="mb-2">You Might Also Like</h3>
    <div class="row">
        <?php foreach ($related_articles as $related): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 article-card" style="transition: transform 0.2s;">
                <a href="article.php?id=<?= $related['id'] ?>" style="text-decoration:none; color:inherit;">
                    <img src="<?= $related['image_url'] ? htmlspecialchars($related['image_url']) : 'https://placehold.co/300x200?text=News' ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title" style="font-family: var(--font-serif); font-size: 1.1rem; line-height: 1.4; color: var(--dark);"><?= htmlspecialchars($related['title']) ?></h5>
                        <div class="mt-auto d-flex justify-content-between text-muted" style="font-size: 0.8rem;">
                            <span><?= time_elapsed_string($related['created_at']) ?></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
