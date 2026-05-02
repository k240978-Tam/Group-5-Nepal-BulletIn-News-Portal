<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

require_login();

$user = get_logged_in_user();

// Fetch User's Comments
$stmt = $pdo->prepare("SELECT c.content, c.status, c.created_at, a.title, a.id as article_id 
                       FROM comments c
                       JOIN articles a ON c.article_id = a.id
                       WHERE c.user_id = ?
                       ORDER BY c.created_at DESC");
$stmt->execute([$user['id']]);
$comments = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <h3>My Profile</h3>
        <p class="text-gray mb-2"><?= htmlspecialchars($user['name']) ?><br><?= htmlspecialchars($user['role']) ?></p>
        <ul>
            <li><a href="/newsportal/profile.php" class="active"><i class="fas fa-comment"></i> My Comments</a></li>
            <?php if (in_array($user['role'], ['admin', 'editor', 'journalist'])): ?>
            <li><a href="/newsportal/admin/index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <?php endif; ?>
            <li><a href="/newsportal/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="dashboard-content">
        <h2 class="mb-2">My Comments</h2>
        
        <?php if (count($comments) > 0): ?>
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                <?php foreach ($comments as $comment): ?>
                    <div style="border: 1px solid var(--gray); border-radius: var(--radius-sm); padding: 1rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                            <strong>On: <a href="/newsportal/article.php?id=<?= $comment['article_id'] ?>" class="text-primary"><?= htmlspecialchars($comment['title']) ?></a></strong>
                            <span class="category-badge" style="background: <?= $comment['status'] == 'approved' ? '#25D366' : ($comment['status'] == 'pending' ? '#FFA500' : '#FF0000') ?>;"><?= ucfirst($comment['status']) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                        <small class="text-gray mt-2" style="display:block;"><?= date('F j, Y, g:i a', strtotime($comment['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray">You haven't posted any comments yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
