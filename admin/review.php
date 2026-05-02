<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin', 'editor']);
$user = get_logged_in_user();

// Fetch pending articles
$stmt = $pdo->query("SELECT a.id, a.title, u.name as author, a.created_at, c.name as category 
                     FROM articles a 
                     JOIN users u ON a.author_id = u.id 
                     JOIN categories c ON a.category_id = c.id
                     WHERE a.status = 'pending' 
                     ORDER BY a.created_at ASC");
$pending_articles = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <aside class="sidebar">
        <h3>Dashboard</h3>
        <ul>
            <li><a href="/newsportal/admin/index.php"><i class="fas fa-home"></i> Overview</a></li>
            <li><a href="/newsportal/admin/editor.php"><i class="fas fa-pen"></i> Write Article</a></li>
            <li><a href="/newsportal/admin/review.php" class="active"><i class="fas fa-tasks"></i> Review Articles</a></li>
            <li><a href="/newsportal/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="/newsportal/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <div class="dashboard-content">
        <h2 class="mb-2">Approval Queue</h2>
        
        <?php if (count($pending_articles) > 0): ?>
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--gray); text-align:left;">
                        <th style="padding:0.5rem;">Title</th>
                        <th style="padding:0.5rem;">Category</th>
                        <th style="padding:0.5rem;">Author</th>
                        <th style="padding:0.5rem;">Submitted Date</th>
                        <th style="padding:0.5rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_articles as $art): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding:0.5rem;"><?= htmlspecialchars(get_excerpt($art['title'], 40)) ?></td>
                        <td style="padding:0.5rem;"><span class="category-badge"><?= htmlspecialchars($art['category']) ?></span></td>
                        <td style="padding:0.5rem;"><?= htmlspecialchars($art['author']) ?></td>
                        <td style="padding:0.5rem;"><?= date('M j, Y', strtotime($art['created_at'])) ?></td>
                        <td style="padding:0.5rem;">
                            <form action="/newsportal/actions/process_article.php" method="POST" style="display:inline-flex; gap:0.5rem;">
                                <input type="hidden" name="article_id" value="<?= $art['id'] ?>">
                                <!-- Provide a way to view before approve, here we just edit it -->
                                <a href="/newsportal/admin/editor.php?id=<?= $art['id'] ?>" class="btn" style="background:#e2e8f0; padding:0.2rem 0.5rem; font-size:0.8rem;">Review</a>
                                <button type="submit" name="action" value="publish" class="btn btn-primary" style="padding:0.2rem 0.5rem; font-size:0.8rem;">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn" style="background:#ef233c; color:#fff; padding:0.2rem 0.5rem; font-size:0.8rem;">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 0;">
                <i class="fas fa-check-circle text-gray" style="font-size: 3rem; margin-bottom: 1rem; color: #25D366;"></i>
                <h3 class="text-gray">All caught up!</h3>
                <p class="text-gray mt-2">There are no articles pending review.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
