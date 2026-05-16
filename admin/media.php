<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin', 'editor']);
$user = get_logged_in_user();

$upload_dir = '../uploads/';
$files = [];

if (is_dir($upload_dir)) {
    $dir_files = scandir($upload_dir);
    foreach ($dir_files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.DS_Store') {
            $path = $upload_dir . $file;
            $files[] = [
                'name' => $file,
                'url' => '/newsportal/uploads/' . $file,
                'size' => filesize($path),
                'date' => filemtime($path)
            ];
        }
    }
}

// Sort by date descending
usort($files, function($a, $b) {
    return $b['date'] <=> $a['date'];
});

if (isset($_GET['delete'])) {
    $file_to_delete = basename($_GET['delete']);
    $file_path = $upload_dir . $file_to_delete;
    if (file_exists($file_path)) {
        unlink($file_path);
        log_action("Deleted media file", $file_to_delete);
        $_SESSION['success_message'] = "File deleted successfully.";
    }
    redirect('/newsportal/admin/media.php');
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php require 'includes/sidebar.php'; ?>

    <div class="dashboard-content">
        <h2 class="mb-2">Media Library</h2>
        <p class="text-gray mb-3">Manage all images and assets uploaded to the portal.</p>

        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:1.5rem;">
            <?php foreach ($files as $f): ?>
            <div class="ed-card" style="padding:0; overflow:hidden; position:relative;">
                <img src="<?= $f['url'] ?>" style="width:100%; height:150px; object-fit:cover;">
                <div style="padding:0.75rem;">
                    <div style="font-size:0.75rem; font-weight:600; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($f['name']) ?></div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:0.5rem;">
                        <span style="font-size:0.7rem; color:#94a3b8;"><?= round($f['size'] / 1024, 1) ?> KB</span>
                        <div style="display:flex; gap:0.5rem;">
                            <button onclick="copyToClipboard('<?= $f['url'] ?>')" class="btn btn-light" style="padding:0.25rem 0.5rem; font-size:0.7rem;" title="Copy URL">
                                <i class="fas fa-link"></i>
                            </button>
                            <a href="?delete=<?= urlencode($f['name']) ?>" onclick="return confirm('Permanently delete this file?')" class="btn btn-light" style="padding:0.25rem 0.5rem; font-size:0.7rem; color:#e74c3c;" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($files)): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:5rem; color:#94a3b8;">
                <i class="fas fa-photo-video" style="font-size:3rem; opacity:0.3; margin-bottom:1rem;"></i>
                <p>No media files found in the library.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    const fullUrl = window.location.origin + text;
    navigator.clipboard.writeText(fullUrl).then(() => {
        alert('URL copied to clipboard!');
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
