<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-layout">
    <?php require 'includes/sidebar.php'; ?>

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
                    <td style="padding:0.5rem;">
                        <div style="font-weight:600;"><?= htmlspecialchars(get_excerpt($art['title'], 40)) ?></div>
                        <?php if(!empty($art['internal_note'])): ?>
                            <div style="font-size:0.75rem; color:#e74c3c; margin-top:0.2rem; background:#fff5f5; padding:0.2rem 0.5rem; border-radius:4px; display:inline-block;">
                                <i class="fas fa-comment-dots"></i> <strong>Note:</strong> <?= htmlspecialchars($art['internal_note']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                    <td style="padding:0.5rem;"><?= htmlspecialchars($art['author'] ?? 'Unknown') ?></td>
                    <?php endif; ?>
                    <td style="padding:0.5rem;">
                        <span class="category-badge" style="background: <?= $art['status'] == 'published' ? '#25D366' : ($art['status'] == 'pending' ? '#FFA500' : ($art['status'] == 'rejected' ? '#FF0000' : '#8d99ae')) ?>;"><?= ucfirst($art['status']) ?></span>
                    </td>
                    <td style="padding:0.5rem;"><?= date('M j, Y', strtotime($art['created_at'])) ?></td>
                    <td style="padding:0.5rem; white-space:nowrap;">
                        <a href="/newsportal/admin/editor?id=<?= $art['id'] ?>" class="text-primary" style="margin-right:0.5rem;"><i class="fas fa-edit"></i> Edit</a>
                        
                        <?php if ($art['status'] == 'published' || in_array($user['role'], ['admin', 'editor'])): ?>
                            <a href="/newsportal/article?id=<?= $art['id'] ?>" target="_blank" style="margin-right:0.5rem; color:#2980b9;"><i class="fas fa-eye"></i> View</a>
                        <?php endif; ?>

                        <?php if ($art['status'] == 'pending' && in_array($user['role'], ['admin', 'editor'])): ?>
                            <form action="/newsportal/admin/article/process" method="POST" style="display:inline;">
                                <input type="hidden" name="article_id" value="<?= $art['id'] ?>">
                                <input type="hidden" name="action" value="publish">
                                <input type="hidden" name="redirect_to" value="/newsportal/admin">
                                <button type="submit" class="text-primary" style="background:none; border:none; cursor:pointer; padding:0; margin-right:0.5rem; color:#27ae60; font-size:inherit;">
                                    <i class="fas fa-check-circle"></i> Approve
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                            <button onclick="confirmDelete(<?= $art['id'] ?>, '<?= htmlspecialchars(addslashes(get_excerpt($art['title'], 40)), ENT_QUOTES) ?>')" 
                                style="background:none;border:none;cursor:pointer;color:#e74c3c;font-size:0.85rem;padding:0;" title="Delete article">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:2rem; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.3); text-align:center;">
        <div style="width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fas fa-trash-alt" style="font-size:1.4rem;color:#e74c3c;"></i>
        </div>
        <h4 style="margin:0 0 0.5rem;color:#1e293b;">Delete Article?</h4>
        <p style="color:#64748b;font-size:0.9rem;margin:0 0 1.5rem;" id="deleteModalMsg">This action cannot be undone.</p>
        <form id="deleteForm" action="/newsportal/admin/article/delete" method="POST">
            <input type="hidden" name="article_id" id="deleteArticleId">
            <div style="display:flex;gap:0.75rem;justify-content:center;">
                <button type="button" onclick="closeDeleteModal()" 
                    style="padding:0.6rem 1.5rem;border:1.5px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#475569;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit" 
                    style="padding:0.6rem 1.5rem;border:none;border-radius:8px;background:linear-gradient(135deg,#c0392b,#e74c3c);color:#fff;font-weight:700;cursor:pointer;">
                    <i class="fas fa-trash-alt"></i> Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    document.getElementById('deleteArticleId').value = id;
    document.getElementById('deleteModalMsg').textContent = 'You are about to permanently delete: "' + title + '". This cannot be undone.';
    var modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
// Close on backdrop click
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
