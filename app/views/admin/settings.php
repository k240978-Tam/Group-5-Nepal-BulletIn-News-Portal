<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-layout">
    <?php require 'includes/sidebar.php'; ?>

    <div class="dashboard-content">
        <h2 class="mb-2">Site Configuration</h2>
        <p class="text-gray mb-3">Manage your portal's branding and global preferences.</p>

        <form action="" method="POST" class="ed-card" style="max-width: 600px; background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.08); padding:1.25rem;">
            <div class="form-group mb-3">
                <label style="font-weight:600; font-size:0.85rem;">Site Name</label>
                <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'Nepal Bulletin') ?>" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <label style="font-weight:600; font-size:0.85rem;">Site Tagline</label>
                <input type="text" name="site_tagline" value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>" class="form-control">
            </div>

            <div class="form-group mb-3">
                <label style="font-weight:600; font-size:0.85rem;">Accent Theme Color</label>
                <div style="display:flex; align-items:center; gap:1rem;">
                    <input type="color" name="accent_color" value="<?= htmlspecialchars($settings['accent_color'] ?? '#c0392b') ?>" style="width:50px; height:40px; border:none; cursor:pointer;">
                    <code><?= htmlspecialchars($settings['accent_color'] ?? '#c0392b') ?></code>
                </div>
                <small class="text-gray">This color will be used for buttons, links, and badges across the portal.</small>
            </div>

            <div style="margin: 1.5rem 0; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer;">
                    <div class="ts" style="position:relative; width:44px; height:24px;">
                        <input type="checkbox" name="allow_registration" value="1" <?= ($settings['allow_registration'] ?? '1') == '1' ? 'checked' : '' ?> style="opacity:0; width:0; height:0;">
                        <span class="tslider" style="position:absolute; inset:0; background:#cbd5e1; border-radius:24px; transition:0.3s; cursor:pointer;"></span>
                        <style>input:checked + .tslider { background: #c0392b !important; } input:checked + .tslider:before { transform: translateX(20px); } .tslider:before { content:''; position:absolute; height:18px; width:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:0.3s; }</style>
                    </div>
                    <span style="font-weight:600; color:#1e293b;">Allow Public Registration</span>
                </label>
                <small class="text-gray" style="display:block; margin-top:0.25rem;">Disable this to prevent new users from creating accounts through the Register page.</small>
            </div>

            <button type="submit" class="btn btn-danger" style="width:100%; font-weight:700;">Save Global Settings</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
