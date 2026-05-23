<?php
if (!isset($user)) {
    $user = get_logged_in_user();
}

$pending_articles_count = 0;
if ($user && in_array($user['role'], ['admin', 'editor'])) {
    global $pdo;
    if ($pdo) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'pending'");
        $pending_articles_count = $stmt->fetchColumn();
    }
}

$uri = $_SERVER['REQUEST_URI'];
$isActiveDashboard = (strpos($uri, '/admin') !== false && !preg_match('/\/admin\/(editor|review|media|users|settings|logs)/', $uri));
$isActiveEditor = (strpos($uri, '/admin/editor') !== false);
$isActiveReview = (strpos($uri, '/admin/review') !== false);
$isActiveMedia = (strpos($uri, '/admin/media') !== false);
$isActiveUsers = (strpos($uri, '/admin/users') !== false);
$isActiveSettings = (strpos($uri, '/admin/settings') !== false);
$isActiveLogs = (strpos($uri, '/admin/logs') !== false);
$isActiveProfile = (strpos($uri, '/profile') !== false);
?>
<aside class="sidebar">
    <h3>Dashboard</h3>
    <?php
    $avatar = !empty($user['profile_picture']) ? '/newsportal/uploads/' . htmlspecialchars($user['profile_picture']) : null;
    if ($avatar): ?>
        <img src="<?= $avatar ?>" style="width:60px; height:60px; border-radius:50%; object-fit:cover; margin-bottom:0.5rem; border:2px solid #e2e8f0;">
    <?php endif; ?>
    <p class="text-gray mb-2"><?= htmlspecialchars($user['name'] ?? 'Guest') ?><br>(<?= ucfirst(htmlspecialchars($user['role'] ?? 'guest')) ?>)</p>
    <ul>
        <?php if ($user && in_array($user['role'], ['admin', 'editor', 'journalist'])): ?>
        <li><a href="/newsportal/admin" class="<?= $isActiveDashboard ? 'active' : '' ?>"><i class="fas fa-home"></i> Overview</a></li>
        <li><a href="/newsportal/admin/editor" class="<?= $isActiveEditor ? 'active' : '' ?>"><i class="fas fa-pen"></i> Write Article</a></li>
        <?php endif; ?>
        <?php if ($user && in_array($user['role'], ['admin', 'editor'])): ?>
        <li><a href="/newsportal/admin/review" class="<?= $isActiveReview ? 'active' : '' ?>"><i class="fas fa-tasks"></i> Review Articles (<?= $pending_articles_count ?>)</a></li>
        <li><a href="/newsportal/admin/media" class="<?= $isActiveMedia ? 'active' : '' ?>"><i class="fas fa-images"></i> Media Library</a></li>
        <?php endif; ?>
        <?php if ($user && $user['role'] === 'admin'): ?>
            <li><a href="/newsportal/admin/users" class="<?= $isActiveUsers ? 'active' : '' ?>"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="/newsportal/admin/settings" class="<?= $isActiveSettings ? 'active' : '' ?>"><i class="fas fa-cog"></i> Site Settings</a></li>
            <li><a href="/newsportal/admin/logs" class="<?= $isActiveLogs ? 'active' : '' ?>"><i class="fas fa-history"></i> Audit Logs</a></li>
        <?php endif; ?>
        <li><a href="/newsportal/profile" class="<?= $isActiveProfile ? 'active' : '' ?>"><i class="fas fa-user"></i> My Profile</a></li>
        <li><a href="/newsportal/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>
