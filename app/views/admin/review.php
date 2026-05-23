<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-layout">
    <?php require 'includes/sidebar.php'; ?>

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
                            <div style="display:inline-flex; gap:0.5rem; align-items:center;">
                                <a href="/newsportal/article?id=<?= $art['id'] ?>" target="_blank" class="btn" style="background:#f1f5f9; color:#475569; padding:0.2rem 0.5rem; font-size:0.8rem;"><i class="fas fa-eye"></i> Preview</a>
                                <a href="/newsportal/admin/editor?id=<?= $art['id'] ?>" class="btn" style="background:#e2e8f0; color:#1e293b; padding:0.2rem 0.5rem; font-size:0.8rem;"><i class="fas fa-edit"></i> Edit</a>
                                <form action="/newsportal/admin/article/process" method="POST" style="display:inline-flex; gap:0.5rem; align-items:center;">
                                    <input type="hidden" name="article_id" value="<?= $art['id'] ?>">
                                    <input type="hidden" name="redirect_to" value="/newsportal/admin/review">
                                    <input type="text" name="internal_note" placeholder="Internal feedback..." style="font-size:0.75rem; padding:0.2rem 0.5rem; border:1px solid #ddd; border-radius:4px; width:150px;">
                                    <button type="submit" name="action" value="publish" class="btn btn-primary" style="padding:0.2rem 0.5rem; font-size:0.8rem;"><i class="fas fa-check"></i> Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn" style="background:#ef233c; color:#fff; padding:0.2rem 0.5rem; font-size:0.8rem;"><i class="fas fa-times"></i> Reject</button>
                                </form>
                            </div>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
