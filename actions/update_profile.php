<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

require_login();
$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        $_SESSION['error_message'] = "Name and Email are required.";
        redirect('/newsportal/profile.php');
    }

    try {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            $_SESSION['error_message'] = "This email is already registered to another account.";
            redirect('/newsportal/profile.php');
        }

        $profile_picture = $user['profile_picture'] ?? null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = 'profile_' . $user['id'] . '_' . time() . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
                    $profile_picture = $new_filename;
                    // Delete old picture if it exists
                    if (!empty($user['profile_picture']) && file_exists($upload_dir . $user['profile_picture'])) {
                        unlink($upload_dir . $user['profile_picture']);
                    }
                }
            } else {
                $_SESSION['error_message'] = "Invalid image format. Allowed: JPG, PNG, GIF, WEBP.";
                redirect('/newsportal/profile.php');
                exit;
            }
        }

        if (!empty($password)) {
            // Update with password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, profile_picture = ? WHERE id = ?");
            $stmt->execute([$name, $email, $hashed_password, $profile_picture, $user['id']]);
        } else {
            // Update without password
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?");
            $stmt->execute([$name, $email, $profile_picture, $user['id']]);
        }

        // Update session data
        $_SESSION['user_name'] = $name;
        $_SESSION['email'] = $email;

        $_SESSION['success_message'] = "Profile updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
    }

    redirect('/newsportal/profile.php');
} else {
    redirect('/newsportal/profile.php');
}
?>
