<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_role(['admin', 'editor', 'journalist']);
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/newsportal/admin/index.php');
}

$article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
$action     = $_POST['action'] ?? '';

// Approve/Reject from review.php or dashboard
if ($article_id > 0 && in_array($action, ['publish', 'reject']) && empty($_POST['title'])) {
    require_role(['admin', 'editor']);
    $new_status = ($action == 'publish') ? 'published' : 'rejected';
    $pdo->prepare("UPDATE articles SET status = ? WHERE id = ?")->execute([$new_status, $article_id]);
    $_SESSION['success_message'] = "Article " . ($new_status == 'published' ? 'approved' : 'rejected') . " successfully.";
    
    $redirect = $_POST['redirect_to'] ?? '/newsportal/admin/review.php';
    redirect($redirect);
}

// ---- Sanitize standard fields ----
$title           = sanitize_input($_POST['title'] ?? '');
$category_id     = (int)($_POST['category_id'] ?? 0);
$summary         = sanitize_input($_POST['summary'] ?? '');
$content         = $_POST['content'] ?? '';  // Allow HTML from Quill
$meta_description = sanitize_input($_POST['meta_description'] ?? '');
$image_position  = in_array($_POST['image_position'] ?? '', ['left','center','right','full']) ? $_POST['image_position'] : 'center';
$image_caption   = sanitize_input($_POST['image_caption'] ?? '');
$article_color   = preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['article_color'] ?? '') ? $_POST['article_color'] : '#c0392b';
$language        = in_array($_POST['language'] ?? '', ['en','ne','bilingual']) ? $_POST['language'] : 'en';
$article_type    = in_array($_POST['article_type'] ?? '', ['standard','opinion','interview','photo_essay','breaking']) ? $_POST['article_type'] : 'standard';
$is_featured     = isset($_POST['is_featured']) ? 1 : 0;
$scheduled_at    = !empty($_POST['scheduled_at']) ? date('Y-m-d H:i:s', strtotime($_POST['scheduled_at'])) : null;

// Slug: generate if empty
$slug_raw = trim($_POST['slug'] ?? '');
if (empty($slug_raw)) {
    $slug_raw = strtolower(preg_replace('/[^\w\s-]/', '', $title));
    $slug_raw = preg_replace('/[\s_-]+/', '-', $slug_raw);
    $slug_raw = trim($slug_raw, '-');
}
// Ensure slug uniqueness
$slug = $slug_raw;
$suffix = 1;
while (true) {
    $ck = $pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
    $ck->execute([$slug, $article_id]);
    if (!$ck->fetch()) break;
    $slug = $slug_raw . '-' . $suffix++;
}

// ---- Status ----
$status = 'draft';
if ($action == 'publish' && in_array($user['role'], ['admin','editor'])) $status = 'published';
if ($action == 'pending') $status = 'pending';
// If scheduled future date, override status
if ($scheduled_at && strtotime($scheduled_at) > time() && $status == 'published') $status = 'scheduled';

// ---- Image upload ----
$image_url   = null;
$upload_error = false;
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename    = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['image']['name']));
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_url = '/newsportal/uploads/' . $filename;
        } else {
            $_SESSION['error_message'] = "Failed to save uploaded image. Check directory permissions.";
            $upload_error = true;
        }
    } else {
        $_SESSION['error_message'] = "Image upload failed (error code: " . $_FILES['image']['error'] . ")";
        $upload_error = true;
    }
}
if ($upload_error) {
    redirect('/newsportal/admin/editor.php' . ($article_id > 0 ? "?id=$article_id" : ''));
}

// ---- Tags ----
$tags_raw = array_filter(array_map('trim', explode(',', $_POST['tags_hidden'] ?? '')));

try {
    if ($article_id > 0) {
        // UPDATE
        if ($image_url) {
            $stmt = $pdo->prepare("UPDATE articles SET title=?,category_id=?,summary=?,content=?,image_url=?,
                image_position=?,image_caption=?,article_color=?,language=?,article_type=?,
                meta_description=?,slug=?,is_featured=?,scheduled_at=?,status=? WHERE id=?");
            $stmt->execute([$title,$category_id,$summary,$content,$image_url,
                $image_position,$image_caption,$article_color,$language,$article_type,
                $meta_description,$slug,$is_featured,$scheduled_at,$status,$article_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE articles SET title=?,category_id=?,summary=?,content=?,
                image_position=?,image_caption=?,article_color=?,language=?,article_type=?,
                meta_description=?,slug=?,is_featured=?,scheduled_at=?,status=? WHERE id=?");
            $stmt->execute([$title,$category_id,$summary,$content,
                $image_position,$image_caption,$article_color,$language,$article_type,
                $meta_description,$slug,$is_featured,$scheduled_at,$status,$article_id]);
        }
        $_SESSION['success_message'] = "Article updated successfully.";
    } else {
        // INSERT
        $stmt = $pdo->prepare("INSERT INTO articles (title,content,summary,image_url,author_id,category_id,status,
            image_position,image_caption,article_color,language,article_type,meta_description,slug,is_featured,scheduled_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$title,$content,$summary,$image_url,$user['id'],$category_id,$status,
            $image_position,$image_caption,$article_color,$language,$article_type,
            $meta_description,$slug,$is_featured,$scheduled_at]);
        $article_id = $pdo->lastInsertId();
        $_SESSION['success_message'] = "Article created successfully.";
    }

    // Save tags
    $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?")->execute([$article_id]);
    foreach ($tags_raw as $tag_name) {
        if (empty($tag_name)) continue;
        // Insert tag if not exists
        $pdo->prepare("INSERT IGNORE INTO tags (name) VALUES (?)")->execute([$tag_name]);
        $tag_row = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
        $tag_row->execute([$tag_name]);
        $tag_id = $tag_row->fetchColumn();
        if ($tag_id) {
            $pdo->prepare("INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (?,?)")->execute([$article_id, $tag_id]);
        }
    }

    redirect('/newsportal/admin/index.php');

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    redirect('/newsportal/admin/editor.php' . ($article_id > 0 ? "?id=$article_id" : ''));
}
?>
