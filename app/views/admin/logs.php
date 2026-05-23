<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-layout">
    <?php require 'includes/sidebar.php'; ?>

    <div class="dashboard-content">
        <h2 class="mb-2">Audit Logs</h2>
        <p class="text-gray mb-3">Monitor all critical system actions performed by users.</p>

        <div class="ed-card" style="padding:0; overflow-x:auto; background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.08);">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                        <th style="padding:1rem;">User</th>
                        <th style="padding:1rem;">Action</th>
                        <th style="padding:1rem;">Details</th>
                        <th style="padding:1rem;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $l): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding:1rem;">
                            <?php if ($l['user_name']): ?>
                                <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($l['user_name']) ?></div>
                                <div style="font-size:0.75rem; color:#64748b;"><?= htmlspecialchars($l['user_email']) ?></div>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-style:italic;">System / Guest</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:1rem;">
                            <span style="background:#e2e8f0; color:#475569; padding:0.2rem 0.6rem; border-radius:20px; font-size:0.7rem; font-weight:700; text-transform:uppercase;">
                                <?= htmlspecialchars($l['action']) ?>
                            </span>
                        </td>
                        <td style="padding:1rem; font-size:0.85rem; color:#475569; max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($l['details']) ?>">
                            <?= htmlspecialchars($l['details']) ?>
                        </td>
                        <td style="padding:1rem; font-size:0.85rem; color:#64748b;">
                            <?= time_elapsed_string($l['created_at']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <tr><td colspan="4" style="text-align:center; padding:3rem; color:#94a3b8;">No logs recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
