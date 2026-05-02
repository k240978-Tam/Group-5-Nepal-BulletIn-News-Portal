<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin', 'editor', 'journalist']);
$user = get_logged_in_user();

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null;

if ($article_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch();
    
    // Ensure journalists can only edit their own drafts/rejected articles
    if ($user['role'] == 'journalist' && $article['author_id'] != $user['id']) {
        $_SESSION['error_message'] = "You do not have permission to edit this article.";
        redirect('/newsportal/admin/index.php');
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <h3>Dashboard</h3>
        <ul>
            <li><a href="/newsportal/admin/index.php"><i class="fas fa-home"></i> Overview</a></li>
            <li><a href="/newsportal/admin/editor.php" class="active"><i class="fas fa-pen"></i> Write Article</a></li>
            <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                <li><a href="/newsportal/admin/review.php"><i class="fas fa-tasks"></i> Review Articles</a></li>
            <?php endif; ?>
            <li><a href="/newsportal/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="/newsportal/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="dashboard-content">
        <h2 class="mb-2"><?= $article ? 'Edit Article' : 'Write New Article' ?></h2>
        
        <form action="/newsportal/actions/process_article.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="article_id" value="<?= $article ? $article['id'] : '' ?>">
            
            <div class="form-group">
                <label for="title">Headline</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= $article ? htmlspecialchars($article['title']) : '' ?>" required>
            </div>
            
            <div style="display:flex; gap:1rem;">
                <div class="form-group" style="flex:1;">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category...</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($article && $article['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="flex:1;">
                    <label for="image">Featured Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    <?php if($article && $article['image_url']): ?>
                        <small class="text-gray">Current: <a href="<?= htmlspecialchars($article['image_url']) ?>" target="_blank">View Image</a>. Upload new to replace.</small>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="summary">Summary (Optional)</label>
                <textarea id="summary" name="summary" class="form-control" rows="2"><?= $article ? htmlspecialchars($article['summary']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="content">Article Content</label>
                <!-- Using simple textarea for now, could be upgraded to Quill or TinyMCE -->
                <textarea id="content" name="content" class="form-control" rows="15" required><?= $article ? htmlspecialchars($article['content']) : '' ?></textarea>
                <small class="text-gray">You can use basic HTML tags for formatting (e.g., &lt;p&gt;, &lt;b&gt;, &lt;i&gt;, &lt;h3&gt;)</small>
            </div>
            
            <div style="display:flex; gap:1rem; margin-top:2rem;">
                <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                    <button type="submit" name="action" value="publish" class="btn btn-primary">Publish Immediately</button>
                    <button type="submit" name="action" value="draft" class="btn text-gray" style="background:#e2e8f0;">Save Draft</button>
                <?php else: ?>
                    <button type="submit" name="action" value="pending" class="btn btn-primary">Submit for Review</button>
                    <button type="submit" name="action" value="draft" class="btn text-gray" style="background:#e2e8f0;">Save Draft</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
