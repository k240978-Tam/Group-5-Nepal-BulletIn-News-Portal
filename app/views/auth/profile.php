<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$isDashboardUser = in_array($user['role'], ['admin', 'editor', 'journalist']);
$avatar = !empty($user['profile_picture']) ? '/newsportal/uploads/' . htmlspecialchars($user['profile_picture']) : null;
?>

<?php if ($isDashboardUser): ?>
<div class="dashboard-layout">
    <?php require __DIR__ . '/../admin/includes/sidebar.php'; ?>
    <div class="dashboard-content">
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4" style="border-radius:10px; border:none; box-shadow:0 2px 12px rgba(0,0,0,.08) !important;">
            <div class="card-body text-center py-4">
                <div class="mb-3 position-relative d-inline-block">
                    <?php if ($avatar): ?>
                        <img src="<?= $avatar ?>" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <i class="fas fa-user-circle fa-7x text-secondary" style="color: #cbd5e1 !important;"></i>
                    <?php endif; ?>
                </div>
                <h4 class="font-weight-bold mb-1" style="color:#1e293b;"><?= htmlspecialchars($user['name']) ?></h4>
                <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
                <span class="badge" style="background:#fee2e2; color:#991b1b; padding:0.4rem 0.8rem; border-radius:20px; font-size:0.75rem; font-weight:700; text-transform:uppercase;"><?= ucfirst($user['role']) ?></span>
                
                <hr class="my-4" style="border-color:#f1f5f9;">
                
                <div class="text-left px-2">
                    <div class="mb-2">
                        <small class="text-muted d-block" style="font-size:0.7rem; font-weight:700; text-transform:uppercase;">Phone Number</small>
                        <span style="font-weight:600; color:#475569;"><?= !empty($user['phone_number']) ? htmlspecialchars($user['phone_number']) : 'Not provided' ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size:0.7rem; font-weight:700; text-transform:uppercase;">Date of Birth</small>
                        <span style="font-weight:600; color:#475569;"><?= !empty($user['dob']) ? date('F j, Y', strtotime($user['dob'])) : 'Not provided' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm mb-4" style="border-radius:10px; border:none; box-shadow:0 2px 12px rgba(0,0,0,.08) !important;">
            <div class="card-header bg-white py-3" style="border-bottom:1px solid #f1f5f9;">
                <h5 class="mb-0 font-weight-bold" style="color:#1e293b;"><i class="fas fa-user-cog text-danger mr-2"></i> Update Profile Details</h5>
            </div>
            <div class="card-body p-4">
                <form action="/newsportal/profile/update" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label style="font-weight:600; font-size:0.85rem; color:#374151;">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:6px; font-size:0.9rem;" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label style="font-weight:600; font-size:0.85rem; color:#374151;">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" placeholder="+977-98XXXXXXXX" style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:6px; font-size:0.9rem;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label style="font-weight:600; font-size:0.85rem; color:#374151;">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob'] ?? '') ?>" style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:6px; font-size:0.9rem;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label style="font-weight:600; font-size:0.85rem; color:#374151;">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control-file" accept="image/*" style="font-size:0.85rem;">
                                <small class="text-muted">JPEG or PNG format. Recommended 300x300px.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label style="font-weight:600; font-size:0.85rem; color:#374151;">New Password <span class="text-muted font-weight-normal">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:6px; font-size:0.9rem;">
                    </div>

                    <button type="submit" class="btn btn-danger px-4 py-2 font-weight-bold" style="border-radius:6px; background:linear-gradient(135deg, #c0392b, #e74c3c); border:none;"><i class="fas fa-save mr-2"></i> Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($isDashboardUser): ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
