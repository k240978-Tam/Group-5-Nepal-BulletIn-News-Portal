<?php
namespace App\Controllers;
use App\Core\Controller;

class UserController extends Controller {
    public function profile() {
        $this->requireRole();
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        $this->view('auth/profile', ['user' => $user]);
    }

    public function update() {
        $this->requireRole();
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone_number = sanitize_input($_POST['phone_number'] ?? '');
        $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
        $id = $_SESSION['user_id'];

        if (empty($name)) {
            $_SESSION['error_message'] = "Name cannot be empty.";
            $this->redirect('/newsportal/profile');
        }

        // Handle profile picture upload
        $profile_picture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = 'avatar_' . $id . '_' . time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['profile_picture']['name']));
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $filename)) {
                    $profile_picture = $filename;
                } else {
                    $_SESSION['error_message'] = "Failed to upload profile picture.";
                }
            } else {
                $_SESSION['error_message'] = "Profile picture upload error code: " . $_FILES['profile_picture']['error'];
            }
        }

        try {
            if ($profile_picture) {
                if (!empty($password)) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->pdo->prepare("UPDATE users SET name = ?, password = ?, phone_number = ?, dob = ?, profile_picture = ? WHERE id = ?");
                    $stmt->execute([$name, $hashed, $phone_number, $dob, $profile_picture, $id]);
                } else {
                    $stmt = $this->pdo->prepare("UPDATE users SET name = ?, phone_number = ?, dob = ?, profile_picture = ? WHERE id = ?");
                    $stmt->execute([$name, $phone_number, $dob, $profile_picture, $id]);
                }
            } else {
                if (!empty($password)) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->pdo->prepare("UPDATE users SET name = ?, password = ?, phone_number = ?, dob = ? WHERE id = ?");
                    $stmt->execute([$name, $hashed, $phone_number, $dob, $id]);
                } else {
                    $stmt = $this->pdo->prepare("UPDATE users SET name = ?, phone_number = ?, dob = ? WHERE id = ?");
                    $stmt->execute([$name, $phone_number, $dob, $id]);
                }
            }
            $_SESSION['name'] = $name;
            $_SESSION['success_message'] = "Profile updated successfully.";
        } catch (\PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        }

        $this->redirect('/newsportal/profile');
    }
}
