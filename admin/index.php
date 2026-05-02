<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin', 'editor', 'journalist']);
$user = get_current_user();

// Statistics
$stats = [];
if (in_array($user['role'], ['admin', 'editor'])) {
    $stats['total_articles'] = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $stats['pending_articles'] = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'pending'")->fetchColumn();
    $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    // Recent articles for admin
    $stmt = $pdo->query("SELECT a.id, a.title, a.status, u.name as author, a.created_at 
                         FROM articles a 
                         JOIN users u ON a.author_id = u.id 
                         ORDER BY a.created_at DESC LIMIT 10");
    $recent_articles = $stmt->fetchAll();
} else {
    // Journalist specific stats
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ?");
    $stmt->execute([$user['id']]);
    $stats['my_articles'] = $stmt->fetchColumn();
    
    // Journalist recent articles
    $stmt = $pdo->prepare("SELECT id, title, status, created_at FROM articles WHERE author_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user['id']]);
    $recent_articles = $stmt->fetchAll();
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <h3>Dashboard</h3>
        <p class="text-gray mb-2"><?= htmlspecialchars($user['name']) ?><br>(<?= ucfirst(htmlspecialchars($user['role'])) ?>)</p>
        <ul>
            <li><a href="/admin/index.php" class="active"><i class="fas fa-home"></i> Overview</a></li>
            <li><a href="/admin/editor.php"><i class="fas fa-pen"></i> Write Article</a></li>
            <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                <li><a href="/admin/review.php"><i class="fas fa-tasks"></i> Review Articles (<?= $stats['pending_articles'] ?? 0 ?>)</a></li>
            <?php endif; ?>
            <li><a href="/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="dashboard-content">
        <h2 class="mb-2">Overview</h2>
        
        <div style="display:flex; gap:1rem; margin-bottom:2rem;">
            <?php foreach ($stats as $key => $value): ?>
                <div style="flex:1; background:var(--light); padding:1.5rem; border-radius:var(--radius-md); text-align:center;">
                    <h3 style="font-size:2rem; color:var(--primary);"><?= $value ?></h3>
                    <p class="text-gray"><?= ucwords(str_replace('_', ' ', $key)) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <h3 class="mb-2">Recent Articles</h3>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--gray); text-align:left;">
                    <th style="padding:0.5rem;">Title</th>
                    <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                    <th style="padding:0.5rem;">Author</th>
                    <?php endif; ?>
                    <th style="padding:0.5rem;">Status</th>
                    <th style="padding:0.5rem;">Date</th>
                    <th style="padding:0.5rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_articles as $art): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding:0.5rem;"><?= htmlspecialchars(get_excerpt($art['title'], 40)) ?></td>
                    <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                    <td style="padding:0.5rem;"><?= htmlspecialchars($art['author'] ?? 'Unknown') ?></td>
                    <?php endif; ?>
                    <td style="padding:0.5rem;">
                        <span class="category-badge" style="background: <?= $art['status'] == 'published' ? '#25D366' : ($art['status'] == 'pending' ? '#FFA500' : ($art['status'] == 'rejected' ? '#FF0000' : '#8d99ae')) ?>;"><?= ucfirst($art['status']) ?></span>
                    </td>
                    <td style="padding:0.5rem;"><?= date('M j, Y', strtotime($art['created_at'])) ?></td>
                    <td style="padding:0.5rem;">
                        <a href="/admin/editor.php?id=<?= $art['id'] ?>" class="text-primary"><i class="fas fa-edit"></i> Edit</a>
                        <?php if ($art['status'] == 'published'): ?>
                            <a href="/article.php?id=<?= $art['id'] ?>" target="_blank" style="margin-left:0.5rem;"><i class="fas fa-eye"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($recent_articles) === 0): ?>
                    <tr><td colspan="5" style="text-align:center; padding:1rem;">No articles found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
