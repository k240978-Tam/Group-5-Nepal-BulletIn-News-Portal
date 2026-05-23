<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-layout">
    <?php require 'includes/sidebar.php'; ?>

    <div class="dashboard-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h2 class="mb-0">User Management</h2>
            <button onclick="toggleCreateForm()" class="btn btn-primary"><i class="fas fa-plus"></i> Create New User</button>
        </div>

        <!-- Create User Form (Hidden by default) -->
        <div id="createUserForm" class="ed-card" style="display:none; margin-bottom:1.5rem; border: 1.5px solid var(--accent); background:#fff5f5; padding: 1.25rem; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,.08);">
            <h5 style="margin-bottom:1rem; color:var(--accent);"><i class="fas fa-user-plus"></i> Add New Team Member</h5>
            <form action="/newsportal/admin/users/create" method="POST" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; align-items: flex-end;">
                <div class="form-group" style="margin:0;">
                    <label style="font-weight:600; font-size:0.85rem;">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-weight:600; font-size:0.85rem;">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-weight:600; font-size:0.85rem;">Temporary Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-weight:600; font-size:0.85rem;">Initial Role</label>
                    <select name="role" class="form-control" required>
                        <option value="user">User</option>
                        <option value="journalist">Journalist</option>
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div style="display:flex; gap:0.5rem;">
                    <button type="submit" class="btn btn-danger" style="flex:1;">Create User</button>
                    <button type="button" onclick="toggleCreateForm()" class="btn btn-light" style="padding:0.6rem 1rem;">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Filters -->
        <div class="ed-card" style="margin-bottom:1.5rem; padding:1rem; background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.08);">
            <form method="GET" action="/newsportal/admin/users" style="display:flex; gap:1rem; flex-wrap:wrap;">
                <div style="flex:1; min-width:200px;">
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name or email...">
                </div>
                <div style="width:150px;">
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        <option value="admin" <?= $role_filter == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="editor" <?= $role_filter == 'editor' ? 'selected' : '' ?>>Editor</option>
                        <option value="journalist" <?= $role_filter == 'journalist' ? 'selected' : '' ?>>Journalist</option>
                        <option value="user" <?= $role_filter == 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger">Filter</button>
                <?php if($search || $role_filter): ?>
                    <a href="/newsportal/admin/users" class="btn btn-light">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Users Table -->
        <div class="ed-card" style="padding:0; overflow-x:auto; background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,.08);">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                        <th style="padding:1rem;">User</th>
                        <th style="padding:1rem;">Role</th>
                        <th style="padding:1rem;">Activity</th>
                        <th style="padding:1rem;">Joined</th>
                        <th style="padding:1rem; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding:1rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <div style="width:36px; height:36px; background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#64748b;">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($u['name']) ?></div>
                                    <div style="font-size:0.75rem; color:#64748b;"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:1rem;">
                            <?php
                            $role_colors = [
                                'admin' => ['bg'=>'#fee2e2', 'text'=>'#991b1b'],
                                'editor' => ['bg'=>'#fef3c7', 'text'=>'#92400e'],
                                'journalist' => ['bg'=>'#dbeafe', 'text'=>'#1e40af'],
                                'user' => ['bg'=>'#f1f5f9', 'text'=>'#475569']
                            ];
                            $c = $role_colors[$u['role']] ?? $role_colors['user'];
                            ?>
                            <span style="background:<?= $c['bg'] ?>; color:<?= $c['text'] ?>; padding:0.2rem 0.6rem; border-radius:20px; font-size:0.7rem; font-weight:700; text-transform:uppercase;">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td style="padding:1rem;">
                            <div style="font-size:0.75rem; color:#64748b;">
                                <i class="fas fa-file-alt"></i> <?= $u['article_count'] ?> Articles<br>
                                <i class="fas fa-comment"></i> <?= $u['comment_count'] ?> Comments
                            </div>
                        </td>
                        <td style="padding:1rem; font-size:0.85rem; color:#64748b;">
                            <?= date('M j, Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td style="padding:1rem; text-align:right;">
                            <?php if ($u['id'] != $user['id']): ?>
                                <button onclick="openRoleModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>', '<?= $u['role'] ?>')" class="text-primary" style="background:none; border:none; cursor:pointer; font-size:0.85rem; margin-right:0.5rem;">
                                    <i class="fas fa-user-tag"></i> Role
                                </button>
                                <button onclick="confirmDeleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>')" class="text-danger" style="background:none; border:none; cursor:pointer; font-size:0.85rem;">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">(You)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div id="roleModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div style="background:#fff; border-radius:16px; padding:2rem; max-width:400px; width:90%; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
        <h3 id="roleModalTitle" style="margin:0 0 1.5rem; color:#1e293b;">Change User Role</h3>
        <form action="/newsportal/admin/users/role" method="POST">
            <input type="hidden" name="user_id" id="roleUserId">
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:.4rem;">Select New Role</label>
                <select name="role" id="roleSelect" class="form-control" required>
                    <option value="user">User</option>
                    <option value="journalist">Journalist</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                </select>
                <p style="font-size:0.75rem; color:#94a3b8; margin-top:0.5rem;">Be careful when promoting users to Admin or Editor roles.</p>
            </div>
            <div style="display:flex; gap:1rem;">
                <button type="button" onclick="closeRoleModal()" style="flex:1; padding:.75rem; border:1.5px solid #e2e8f0; border-radius:8px; background:#fff; color:#475569; font-weight:600; cursor:pointer;">Cancel</button>
                <button type="submit" style="flex:1; padding:.75rem; border:none; border-radius:8px; background:linear-gradient(135deg, #c0392b, #e74c3c); color:#fff; font-weight:700; cursor:pointer;">Update Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div id="deleteUserModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div style="background:#fff; border-radius:16px; padding:2rem; max-width:400px; width:90%; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); text-align:center;">
        <div style="width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fas fa-exclamation-triangle" style="font-size:1.4rem;color:#e74c3c;"></i>
        </div>
        <h3 id="deleteUserTitle" style="margin:0 0 0.5rem; color:#1e293b;">Delete User?</h3>
        <p style="color:#64748b; font-size:0.9rem; margin-bottom:1.5rem;">All articles and comments by this user will also be permanently deleted. This cannot be undone.</p>
        <form action="/newsportal/admin/users/delete" method="POST">
            <input type="hidden" name="user_id" id="deleteUserId">
            <div style="display:flex; gap:1rem;">
                <button type="button" onclick="closeDeleteUserModal()" style="flex:1; padding:.75rem; border:1.5px solid #e2e8f0; border-radius:8px; background:#fff; color:#475569; font-weight:600; cursor:pointer;">Cancel</button>
                <button type="submit" style="flex:1; padding:.75rem; border:none; border-radius:8px; background:#e74c3c; color:#fff; font-weight:700; cursor:pointer;">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCreateForm() {
    const form = document.getElementById('createUserForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function openRoleModal(id, name, currentRole) {
    document.getElementById('roleUserId').value = id;
    document.getElementById('roleModalTitle').textContent = 'Change Role for ' + name;
    document.getElementById('roleSelect').value = currentRole;
    document.getElementById('roleModal').style.display = 'flex';
}
function closeRoleModal() {
    document.getElementById('roleModal').style.display = 'none';
}

function confirmDeleteUser(id, name) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUserTitle').textContent = 'Delete User: ' + name + '?';
    document.getElementById('deleteUserModal').style.display = 'flex';
}
function closeDeleteUserModal() {
    document.getElementById('deleteUserModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('roleModal')) closeRoleModal();
    if (event.target == document.getElementById('deleteUserModal')) closeDeleteUserModal();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
