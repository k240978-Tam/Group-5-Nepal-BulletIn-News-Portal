<?php
namespace App\Controllers;
use App\Core\Controller;

class AdminController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->requireRole(['admin', 'editor', 'journalist']);
    }

    public function dashboard() {
        $stats = [];
        $recent_articles = [];
        
        if (in_array($_SESSION['role'], ['admin', 'editor'])) {
            $stats['total_articles'] = $this->pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
            $stats['pending_articles'] = $this->pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'pending'")->fetchColumn();
            $stats['total_users'] = $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            
            $stmt = $this->pdo->query("SELECT a.id, a.title, a.status, u.name as author, a.created_at, a.internal_note 
                                 FROM articles a 
                                 JOIN users u ON a.author_id = u.id 
                                 ORDER BY a.created_at DESC LIMIT 10");
            $recent_articles = $stmt->fetchAll();
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $stats['my_articles'] = $stmt->fetchColumn();
            
            $stmt = $this->pdo->prepare("SELECT id, title, status, created_at, internal_note FROM articles WHERE author_id = ? ORDER BY created_at DESC LIMIT 10");
            $stmt->execute([$_SESSION['user_id']]);
            $recent_articles = $stmt->fetchAll();
        }

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recent_articles' => $recent_articles,
            'user' => get_logged_in_user()
        ]);
    }

    public function deleteArticle() {
        $this->requireRole(['admin', 'editor']);
        $article_id = (int)($_POST['article_id'] ?? 0);
        
        if ($article_id > 0) {
            $this->pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$article_id]);
            log_action("Deleted article", "ID: $article_id");
            $_SESSION['success_message'] = "Article deleted.";
        }
        $this->redirect('/newsportal/admin');
    }

    public function processArticle() {
        $this->requireRole(['admin', 'editor']);
        $article_id = (int)($_POST['article_id'] ?? 0);
        $action = $_POST['action'] ?? 'publish';
        $internal_note = sanitize_input($_POST['internal_note'] ?? '');
        
        if ($article_id > 0) {
            $new_status = ($action == 'publish') ? 'published' : 'rejected';
            $stmt = $this->pdo->prepare("UPDATE articles SET status = ?, internal_note = ? WHERE id = ?");
            $stmt->execute([$new_status, $internal_note, $article_id]);
            
            log_action(($action == 'publish' ? "Published article" : "Rejected article"), "ID: $article_id, Note: $internal_note");
            $_SESSION['success_message'] = "Article " . ($new_status == 'published' ? 'approved' : 'rejected') . " successfully.";
        }
        
        $redirect = $_POST['redirect_to'] ?? '/newsportal/admin/review';
        $this->redirect($redirect);
    }

    public function users() {
        $this->requireRole(['admin']);
        $search = sanitize_input($_GET['q'] ?? '');
        $role_filter = sanitize_input($_GET['role'] ?? '');
        
        $query = "SELECT u.*, 
                  (SELECT COUNT(*) FROM articles WHERE author_id = u.id) as article_count,
                  (SELECT COUNT(*) FROM comments WHERE user_id = u.id) as comment_count
                  FROM users u WHERE 1=1";
        $params = [];
        
        if ($search) {
            $query .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($role_filter) {
            $query .= " AND u.role = ?";
            $params[] = $role_filter;
        }
        
        $query .= " ORDER BY u.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll();
        
        $this->view('admin/users', [
            'users' => $users,
            'search' => $search,
            'role_filter' => $role_filter,
            'user' => get_logged_in_user()
        ]);
    }

    public function createUser() {
        $this->requireRole(['admin']);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if (!empty($name) && !empty($email) && !empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed, $role]);
            log_action("Created user", "Name: $name, Email: $email, Role: $role");
            $_SESSION['success_message'] = "User created successfully.";
        } else {
            $_SESSION['error_message'] = "All fields are required.";
        }
        $this->redirect('/newsportal/admin/users');
    }

    public function deleteUser() {
        $this->requireRole(['admin']);
        $user_id = (int)($_POST['user_id'] ?? 0);
        
        if ($user_id > 0 && $user_id !== $_SESSION['user_id']) {
            $this->pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
            log_action("Deleted user", "ID: $user_id");
            $_SESSION['success_message'] = "User deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Cannot delete yourself.";
        }
        $this->redirect('/newsportal/admin/users');
    }

    public function updateUserRole() {
        $this->requireRole(['admin']);
        $user_id = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? 'user';
        
        if ($user_id > 0 && $user_id !== $_SESSION['user_id']) {
            $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $user_id]);
            log_action("Updated user role", "ID: $user_id, Role: $role");
            $_SESSION['success_message'] = "Role updated successfully.";
        } else {
            $_SESSION['error_message'] = "Cannot modify your own role.";
        }
        $this->redirect('/newsportal/admin/users');
    }

    public function editor() {
        $this->requireRole(['admin', 'editor', 'journalist']);
        $article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $article = null;
        
        $currentUser = get_logged_in_user();

        if ($article_id > 0) {
            $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE id = ?");
            $stmt->execute([$article_id]);
            $article = $stmt->fetch();
            if ($currentUser['role'] == 'journalist' && $article['author_id'] != $currentUser['id']) {
                $_SESSION['error_message'] = "You do not have permission to edit this article.";
                $this->redirect('/newsportal/admin');
            }
        }
        
        $categories = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
        
        $article_tags = [];
        if ($article_id > 0) {
            try {
                $stmt = $this->pdo->prepare("SELECT t.name FROM tags t JOIN article_tags at2 ON t.id = at2.tag_id WHERE at2.article_id = ?");
                $stmt->execute([$article_id]);
                $article_tags = array_column($stmt->fetchAll(), 'name');
            } catch(\Exception $e) { $article_tags = []; }
        }
        
        $this->view('admin/editor', [
            'article' => $article,
            'categories' => $categories,
            'article_tags' => $article_tags,
            'user' => $currentUser
        ]);
    }

    public function saveArticle() {
        $this->requireRole(['admin', 'editor', 'journalist']);
        $currentUser = get_logged_in_user();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/newsportal/admin');
        }

        $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
        $action     = $_POST['action'] ?? '';

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
            $ck = $this->pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
            $ck->execute([$slug, $article_id]);
            if (!$ck->fetch()) break;
            $slug = $slug_raw . '-' . $suffix++;
        }

        // ---- Status ----
        $status = 'draft';
        if ($action == 'publish' && in_array($currentUser['role'], ['admin','editor'])) $status = 'published';
        if ($action == 'pending') $status = 'pending';
        if ($scheduled_at && strtotime($scheduled_at) > time() && $status == 'published') $status = 'scheduled';

        // ---- Image upload ----
        $image_url   = null;
        $upload_error = false;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $filename    = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['image']['name']));
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    $image_url = '/newsportal/uploads/' . $filename;
                } else {
                    $_SESSION['error_message'] = "Failed to save uploaded image.";
                    $upload_error = true;
                }
            } else {
                $_SESSION['error_message'] = "Image upload failed.";
                $upload_error = true;
            }
        }
        if ($upload_error) {
            $this->redirect('/newsportal/admin/editor' . ($article_id > 0 ? "?id=$article_id" : ''));
        }

        // ---- Tags ----
        $tags_raw = array_filter(array_map('trim', explode(',', $_POST['tags_hidden'] ?? '')));

        try {
            if ($article_id > 0) {
                // UPDATE
                if ($image_url) {
                    $stmt = $this->pdo->prepare("UPDATE articles SET title=?,category_id=?,summary=?,content=?,image_url=?,
                        image_position=?,image_caption=?,article_color=?,language=?,article_type=?,
                        meta_description=?,slug=?,is_featured=?,scheduled_at=?,status=? WHERE id=?");
                    $stmt->execute([$title,$category_id,$summary,$content,$image_url,
                        $image_position,$image_caption,$article_color,$language,$article_type,
                        $meta_description,$slug,$is_featured,$scheduled_at,$status,$article_id]);
                } else {
                    $stmt = $this->pdo->prepare("UPDATE articles SET title=?,category_id=?,summary=?,content=?,
                        image_position=?,image_caption=?,article_color=?,language=?,article_type=?,
                        meta_description=?,slug=?,is_featured=?,scheduled_at=?,status=? WHERE id=?");
                    $stmt->execute([$title,$category_id,$summary,$content,
                        $image_position,$image_caption,$article_color,$language,$article_type,
                        $meta_description,$slug,$is_featured,$scheduled_at,$status,$article_id]);
                }
                $_SESSION['success_message'] = "Article updated successfully.";
            } else {
                // INSERT
                $stmt = $this->pdo->prepare("INSERT INTO articles (title,content,summary,image_url,author_id,category_id,status,
                    image_position,image_caption,article_color,language,article_type,meta_description,slug,is_featured,scheduled_at)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$title,$content,$summary,$image_url,$currentUser['id'],$category_id,$status,
                    $image_position,$image_caption,$article_color,$language,$article_type,
                    $meta_description,$slug,$is_featured,$scheduled_at]);
                $article_id = $this->pdo->lastInsertId();
                $_SESSION['success_message'] = "Article created successfully.";
            }

            // Save tags
            $this->pdo->prepare("DELETE FROM article_tags WHERE article_id = ?")->execute([$article_id]);
            foreach ($tags_raw as $tag_name) {
                if (empty($tag_name)) continue;
                $this->pdo->prepare("INSERT IGNORE INTO tags (name) VALUES (?)")->execute([$tag_name]);
                $tag_row = $this->pdo->prepare("SELECT id FROM tags WHERE name = ?");
                $tag_row->execute([$tag_name]);
                $tag_id = $tag_row->fetchColumn();
                if ($tag_id) {
                    $this->pdo->prepare("INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (?,?)")->execute([$article_id, $tag_id]);
                }
            }

            $this->redirect('/newsportal/admin');

        } catch (\PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
            $this->redirect('/newsportal/admin/editor' . ($article_id > 0 ? "?id=$article_id" : ''));
        }
    }

    public function media() {
        $this->requireRole(['admin', 'editor']);
        $upload_dir = __DIR__ . '/../../public/uploads/';
        
        if (isset($_GET['delete'])) {
            $file_to_delete = basename($_GET['delete']);
            $file_path = $upload_dir . $file_to_delete;
            if (file_exists($file_path)) {
                unlink($file_path);
                log_action("Deleted media file", $file_to_delete);
                $_SESSION['success_message'] = "File deleted successfully.";
            }
            $this->redirect('/newsportal/admin/media');
        }
        
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
        
        $this->view('admin/media', [
            'files' => $files,
            'user' => get_logged_in_user()
        ]);
    }

    public function review() {
        $this->requireRole(['admin', 'editor']);
        $stmt = $this->pdo->query("SELECT a.id, a.title, u.name as author, a.created_at, c.name as category 
                             FROM articles a 
                             JOIN users u ON a.author_id = u.id 
                             JOIN categories c ON a.category_id = c.id
                             WHERE a.status = 'pending' 
                             ORDER BY a.created_at ASC");
        $pending_articles = $stmt->fetchAll();
        $this->view('admin/review', [
            'pending_articles' => $pending_articles,
            'user' => get_logged_in_user()
        ]);
    }

    public function logs() {
        $this->requireRole(['admin']);
        $stmt = $this->pdo->query("SELECT l.*, u.name as user_name, u.email as user_email 
                             FROM audit_logs l 
                             LEFT JOIN users u ON l.user_id = u.id 
                             ORDER BY l.created_at DESC LIMIT 100");
        $logs = $stmt->fetchAll();
        $this->view('admin/logs', [
            'logs' => $logs,
            'user' => get_logged_in_user()
        ]);
    }

    public function settings() {
        $this->requireRole(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $to_update = [
                'site_name' => sanitize_input($_POST['site_name'] ?? ''),
                'site_tagline' => sanitize_input($_POST['site_tagline'] ?? ''),
                'accent_color' => sanitize_input($_POST['accent_color'] ?? '#c0392b'),
                'allow_registration' => isset($_POST['allow_registration']) ? '1' : '0'
            ];
            
            try {
                $this->pdo->beginTransaction();
                foreach ($to_update as $key => $val) {
                    $stmt = $this->pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$key, $val, $val]);
                }
                log_action("Updated site settings", json_encode($to_update));
                $this->pdo->commit();
                $_SESSION['success_message'] = "Site settings updated successfully.";
            } catch (\PDOException $e) {
                $this->pdo->rollBack();
                $_SESSION['error_message'] = "Error updating settings: " . $e->getMessage();
            }
            $this->redirect('/newsportal/admin/settings');
        }
        
        // Fetch current settings
        $settings_stmt = $this->pdo->query("SELECT * FROM site_settings");
        $settings = [];
        while ($row = $settings_stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        $this->view('admin/settings', [
            'settings' => $settings,
            'user' => get_logged_in_user()
        ]);
    }
}
