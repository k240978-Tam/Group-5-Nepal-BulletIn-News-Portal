<?php
if (!isset($user)) {
    $user = get_logged_in_user();
}

$pending_articles_count = 0;
if (in_array($user['role'], ['admin', 'editor'])) {
    if (isset($stats['pending_articles'])) {
        $pending_articles_count = $stats['pending_articles'];
    } else {
        global $pdo;
        $stmt = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'pending'");
        $pending_articles_count = $stmt->fetchColumn();
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <h3>Dashboard</h3>
    <?php
    $avatar = !empty($user['profile_picture']) ? '/newsportal/uploads/' . htmlspecialchars($user['profile_picture']) : null;
    if ($avatar): ?>
        <img src="<?= $avatar ?>" style="width:60px; height:60px; border-radius:50%; object-fit:cover; margin-bottom:0.5rem; border:2px solid #e2e8f0;">
    <?php endif; ?>
    <p class="text-gray mb-2"><?= htmlspecialchars($user['name']) ?><br>(<?= ucfirst(htmlspecialchars($user['role'])) ?>)</p>
    <ul>
        <?php if (in_array($user['role'], ['admin', 'editor', 'journalist'])): ?>
        <li><a href="/newsportal/admin/index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Overview</a></li>
        <li><a href="/newsportal/admin/editor.php" class="<?= $current_page == 'editor.php' ? 'active' : '' ?>"><i class="fas fa-pen"></i> Write Article</a></li>
        <?php endif; ?>
        <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
        <li><a href="/newsportal/admin/review.php" class="<?= $current_page == 'review.php' ? 'active' : '' ?>"><i class="fas fa-tasks"></i> Review Articles (<?= $pending_articles_count ?>)</a></li>
        <li><a href="/newsportal/admin/media.php" class="<?= $current_page == 'media.php' ? 'active' : '' ?>"><i class="fas fa-images"></i> Media Library</a></li>
        <?php endif; ?>
        <?php if ($user['role'] === 'admin'): ?>
            <li><a href="/newsportal/admin/users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="/newsportal/admin/settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Site Settings</a></li>
            <li><a href="/newsportal/admin/logs.php" class="<?= $current_page == 'logs.php' ? 'active' : '' ?>"><i class="fas fa-history"></i> Audit Logs</a></li>
        <?php endif; ?>
        <li><a href="/newsportal/profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> My Profile</a></li>
        <li><a href="/newsportal/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>
