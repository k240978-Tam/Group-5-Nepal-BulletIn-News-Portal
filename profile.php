<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

require_login();
$user = get_logged_in_user();

// Fetch user's comments with article info
$stmt = $pdo->prepare("SELECT c.content, c.status, c.created_at, a.title, a.id as article_id 
                       FROM comments c
                       JOIN articles a ON c.article_id = a.id
                       WHERE c.user_id = ?
                       ORDER BY c.created_at DESC");
$stmt->execute([$user['id']]);
$comments = $stmt->fetchAll();

// If journalist/editor/admin — fetch their article stats
$article_stats = null;
if (in_array($user['role'], ['admin', 'editor', 'journalist'])) {
    $s = $pdo->prepare("SELECT
        COUNT(*) as total,
        SUM(status='published') as published,
        SUM(status='draft') as drafts,
        SUM(status='pending') as pending,
        COALESCE(SUM(views),0) as total_views
        FROM articles WHERE author_id = ?");
    $s->execute([$user['id']]);
    $article_stats = $s->fetch();
}

// Avatar initials
$initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($user['name'])))));
$initials  = substr($initials, 0, 2);

$role_colors = [
    'admin'      => ['bg' => '#fee2e2', 'color' => '#c0392b', 'label' => 'Administrator'],
    'editor'     => ['bg' => '#fef3c7', 'color' => '#92400e', 'label' => 'Editor'],
    'journalist' => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'label' => 'Journalist'],
    'user'       => ['bg' => '#f0fdf4', 'color' => '#166534', 'label' => 'Reader'],
];
$rc = $role_colors[$user['role']] ?? $role_colors['user'];

require_once 'includes/header.php';
?>
<style>
.profile-wrap { max-width: 900px; margin: 2.5rem auto 4rem; }
.profile-hero { background: linear-gradient(135deg, #1e293b 0%, #c0392b 100%); border-radius: 16px; padding: 2.5rem 2rem 5.5rem; position: relative; color: #fff; margin-bottom: 0; z-index: 1; }
.profile-hero h1 { font-size: 1.8rem; font-weight: 800; margin: 0 0 .25rem; }
.profile-hero p { margin: 0; opacity: .9; font-size: 1rem; }
.avatar-circle { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,.2); border: 3px solid rgba(255,255,255,.5); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 1rem; letter-spacing: .05em; }
.profile-body { background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,.12); margin-top: -3.5rem; padding: 2.5rem 2rem; position: relative; z-index: 2; }

/* Stats row */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-box { background: #f8fafc; border-radius: 10px; padding: 1rem; text-align: center; border: 1px solid #f1f5f9; }
.stat-box .num { font-size: 1.6rem; font-weight: 800; color: #c0392b; line-height: 1; }
.stat-box .lbl { font-size: .72rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-top: .3rem; }

/* Info card */
.info-card { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; margin-bottom: 2rem; }
.info-row { background: #f8fafc; border-radius: 8px; padding: .75rem 1rem; }
.info-row .key { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; margin-bottom: .2rem; }
.info-row .val { font-size: .9rem; font-weight: 600; color: #1e293b; }
.role-badge { display: inline-block; padding: .25rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }

/* Comments */
.section-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem; display: flex; align-items: center; gap: .5rem; }
.section-title span { background: #fee2e2; color: #c0392b; font-size: .72rem; padding: .15rem .5rem; border-radius: 20px; font-weight: 700; }
.comment-card { border: 1px solid #f1f5f9; border-radius: 10px; padding: 1rem 1.2rem; margin-bottom: .85rem; transition: box-shadow .2s; position: relative; }
.comment-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }
.comment-card .art-link { font-size: .82rem; font-weight: 700; color: #c0392b; text-decoration: none; }
.comment-card .art-link:hover { text-decoration: underline; }
.comment-card .comment-text { color: #374151; font-size: .9rem; margin: .4rem 0; line-height: 1.55; }
.comment-card .meta { font-size: .73rem; color: #94a3b8; }
.status-dot { display: inline-flex; align-items: center; gap: .3rem; font-size: .7rem; font-weight: 700; padding: .2rem .5rem; border-radius: 20px; position: absolute; top: 1rem; right: 1rem; }
.st-approved { background: #dcfce7; color: #166534; }
.st-pending  { background: #fef3c7; color: #92400e; }
.st-rejected { background: #fee2e2; color: #991b1b; }

/* Action buttons */
.action-btn { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; border-radius: 8px; font-size: .82rem; font-weight: 600; text-decoration: none; transition: opacity .2s, transform .1s; }
.action-btn:hover { opacity: .88; transform: translateY(-1px); }
.btn-dash { background: linear-gradient(135deg, #c0392b, #e74c3c); color: #fff; }
.btn-logout { background: #f1f5f9; color: #475569; }
.empty-state { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
.empty-state i { font-size: 2.5rem; opacity: .4; margin-bottom: .75rem; }

@media(max-width: 600px) { .info-card { grid-template-columns: 1fr; } .stat-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="profile-wrap">
    <!-- Hero Banner -->
    <div class="profile-hero">
        <div class="avatar-circle"><?= htmlspecialchars($initials) ?></div>
        <h1><?= htmlspecialchars($user['name']) ?></h1>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </div>

    <!-- Main Body -->
    <div class="profile-body">

        <!-- Quick Actions -->
        <div style="display:flex; gap:.75rem; margin-bottom:2rem; flex-wrap:wrap;">
            <?php if (in_array($user['role'], ['admin','editor','journalist'])): ?>
                <a href="/newsportal/admin/index.php" class="action-btn btn-dash"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="/newsportal/admin/editor.php" class="action-btn" style="background:#f8fafc;border:1.5px solid #e2e8f0;color:#374151;"><i class="fas fa-pen"></i> Write Article</a>
            <?php endif; ?>
            <button onclick="openEditModal()" class="action-btn" style="background:#f1f5f9; color:#475569; border:none; cursor:pointer;"><i class="fas fa-user-edit"></i> Edit Profile</button>
            <a href="/newsportal/logout.php" class="action-btn btn-logout" style="margin-left:auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Account Info -->
        <div class="info-card">
            <div class="info-row">
                <div class="key"><i class="fas fa-user"></i> Full Name</div>
                <div class="val"><?= htmlspecialchars($user['name']) ?></div>
            </div>
            <div class="info-row">
                <div class="key"><i class="fas fa-envelope"></i> Email</div>
                <div class="val" style="font-size:.85rem;"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <div class="info-row">
                <div class="key"><i class="fas fa-shield-alt"></i> Role</div>
                <div class="val">
                    <span class="role-badge" style="background:<?= $rc['bg'] ?>;color:<?= $rc['color'] ?>;"><?= $rc['label'] ?></span>
                </div>
            </div>
            <div class="info-row">
                <div class="key"><i class="fas fa-calendar-alt"></i> Member Since</div>
                <div class="val"><?= isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'N/A' ?></div>
            </div>
        </div>

        <!-- Article Stats (for writers) -->
        <?php if ($article_stats): ?>
        <div style="margin-bottom:2rem;">
            <div class="section-title"><i class="fas fa-chart-bar" style="color:#c0392b;"></i> My Article Stats</div>
            <div class="stat-grid">
                <div class="stat-box">
                    <div class="num"><?= $article_stats['total'] ?></div>
                    <div class="lbl">Total</div>
                </div>
                <div class="stat-box">
                    <div class="num" style="color:#16a085;"><?= $article_stats['published'] ?></div>
                    <div class="lbl">Published</div>
                </div>
                <div class="stat-box">
                    <div class="num" style="color:#f59e0b;"><?= $article_stats['pending'] ?></div>
                    <div class="lbl">Pending</div>
                </div>
                <div class="stat-box">
                    <div class="num" style="color:#64748b;"><?= $article_stats['drafts'] ?></div>
                    <div class="lbl">Drafts</div>
                </div>
                <div class="stat-box">
                    <div class="num" style="color:#8e44ad;"><?= number_format($article_stats['total_views']) ?></div>
                    <div class="lbl">Total Views</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- My Comments -->
        <div class="section-title">
            <i class="fas fa-comments" style="color:#c0392b;"></i>
            My Comments
            <span><?= count($comments) ?></span>
        </div>

        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $c): ?>
            <div class="comment-card">
                <?php
                    $sc = $c['status'] == 'approved' ? 'st-approved' : ($c['status'] == 'pending' ? 'st-pending' : 'st-rejected');
                    $si = $c['status'] == 'approved' ? 'fa-check-circle' : ($c['status'] == 'pending' ? 'fa-clock' : 'fa-times-circle');
                ?>
                <span class="status-dot <?= $sc ?>"><i class="fas <?= $si ?>"></i> <?= ucfirst($c['status']) ?></span>
                <div style="margin-bottom:.3rem;">
                    <span style="font-size:.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">On:</span>
                    <a href="/newsportal/article.php?id=<?= $c['article_id'] ?>" class="art-link"><?= htmlspecialchars($c['title']) ?></a>
                </div>
                <p class="comment-text"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
                <div class="meta"><i class="fas fa-clock"></i> <?= date('F j, Y \a\t g:i a', strtotime($c['created_at'])) ?></div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div><i class="far fa-comment-dots"></i></div>
                <p>You haven't posted any comments yet.</p>
                <a href="/newsportal/index.php" class="action-btn btn-dash" style="display:inline-flex;margin-top:.5rem;"><i class="fas fa-newspaper"></i> Browse Articles</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div style="background:#fff; border-radius:16px; padding:2rem; max-width:480px; width:90%; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); position: relative;">
        <button onclick="closeEditModal()" style="position:absolute; top:1.25rem; right:1.25rem; background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer;">&times;</button>
        
        <h3 style="margin:0 0 1.5rem; color:#1e293b; display:flex; align-items:center; gap:.5rem;">
            <i class="fas fa-user-edit" style="color:#c0392b;"></i> Edit Profile
        </h3>
        
        <form action="/newsportal/actions/update_profile.php" method="POST">
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:.4rem;">Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required 
                    style="width:100%; padding:.75rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.9rem;">
            </div>
            
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:.4rem;">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required 
                    style="width:100%; padding:.75rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.9rem;">
            </div>
            
            <div style="margin-bottom:1.5rem; padding-top:1rem; border-top:1px solid #f1f5f9;">
                <label style="display:block; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:.4rem;">New Password (leave blank to keep current)</label>
                <input type="password" name="password" placeholder="••••••••"
                    style="width:100%; padding:.75rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.9rem;">
            </div>
            
            <div style="display:flex; gap:1rem;">
                <button type="button" onclick="closeEditModal()" 
                    style="flex:1; padding:.75rem; border:1.5px solid #e2e8f0; border-radius:8px; background:#fff; color:#475569; font-weight:600; cursor:pointer;">
                    Cancel
                </button>
                <button type="submit" 
                    style="flex:1; padding:.75rem; border:none; border-radius:8px; background:linear-gradient(135deg, #c0392b, #e74c3c); color:#fff; font-weight:700; cursor:pointer; box-shadow: 0 4px 12px rgba(192,57,43,0.2);">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal() {
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeEditModal();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
