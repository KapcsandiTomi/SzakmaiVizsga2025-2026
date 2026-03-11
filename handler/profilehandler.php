<?php
require_once 'config.php';
require_once 'handler/profiledata.php';

class ProfileHandler {
    private $model;
    private $currentUser;
    
    public function __construct() {
        global $conn;
        $this->model = new ProfileData($conn);
        
        if (isset($_SESSION['email'])) {
            $this->currentUser = $this->model->loadUserByEmail($_SESSION['email']);
        }
    }
    
    public function handleRequest() {
        if (!isset($_SESSION['email'])) {
            header('Location: index.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_profile'])) {
                $this->handleProfileUpdate($_POST);
            } elseif (isset($_POST['change_password'])) {
                $this->handlePasswordChange($_POST);
            } elseif (isset($_POST['change_picture']) && isset($_FILES['profile_pic'])) {
                $this->handlePictureUpload($_FILES['profile_pic']);
            }
            
            header('Location: profile.php');
            exit();
        }
    }
    
    private function handleProfileUpdate($post_data) {
        $newName = trim($post_data['name'] ?? '');
        $newEmail = trim($post_data['email'] ?? '');
        
        if (empty($newName) || empty($newEmail)) {
            $_SESSION['profile_error'] = "Both fields are required!";
            return;
        }
        
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['profile_error'] = "Invalid email format!";
            return;
        }
        
        if (strlen($newName) < 3) {
            $_SESSION['profile_error'] = "Username must be at least 3 characters!";
            return;
        }
        
        if ($newEmail !== $this->currentUser['email'] && 
            $this->model->checkEmailExists($newEmail, $this->currentUser['id'])) {
            $_SESSION['profile_error'] = "Email already exists!";
            return;
        }
        
        if ($this->model->updateProfile($this->currentUser['id'], $newName, $newEmail)) {
            $_SESSION['profile_success'] = "Profile updated successfully!";
            $_SESSION['email'] = $newEmail;
            $_SESSION['name'] = $newName;
            $this->currentUser['name'] = $newName;
            $this->currentUser['email'] = $newEmail;
        } else {
            $_SESSION['profile_error'] = "Update failed! Please try again.";
        }
    }
    
    private function handlePasswordChange($post_data) {
        $currentPassword = $post_data['current_password'] ?? '';
        $newPassword = $post_data['new_password'] ?? '';
        $confirmPassword = $post_data['confirm_password'] ?? '';
        
        $hashedPassword = $this->model->getCurrentPassword($this->currentUser['id']);
        
        if (!password_verify($currentPassword, $hashedPassword)) {
            $_SESSION['password_error'] = "The current password is incorrect!";
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['password_error'] = "The new passwords do not match!";
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['password_error'] = "Password must be at least 6 characters!";
            return;
        }
        
        if ($this->model->changePassword($this->currentUser['id'], $newPassword)) {
            $_SESSION['password_success'] = "Password successfully changed!";
        } else {
            $_SESSION['password_error'] = "Password change failed!";
        }
    }
    
    private function handlePictureUpload($file_data) {
        $targetDir = "uploads/";
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($file_data["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024;
        
        if (!in_array($imageFileType, $allowedTypes)) {
            $_SESSION['picture_error'] = "Only JPG, JPEG, PNG and GIF files are allowed!";
            return;
        }
        
        if ($file_data["size"] > $maxFileSize) {
            $_SESSION['picture_error'] = "File is too large. Maximum size is 5MB!";
            return;
        }
        
        if (move_uploaded_file($file_data["tmp_name"], $targetFile)) {
            if ($this->model->updateProfilePicture($this->currentUser['id'], $targetFile)) {
                $this->model->deleteOldProfilePicture($this->currentUser['profile_pic']);
                $_SESSION['picture_success'] = "Profile picture uploaded successfully!";
                $this->currentUser['profile_pic'] = $targetFile;
            } else {
                $_SESSION['picture_error'] = "Failed to update profile picture in database!";
                unlink($targetFile); 
            }
        } else {
            $_SESSION['picture_error'] = "There was an error uploading your file!";
        }
    }
    
    public function getCurrentUser() {
        return $this->currentUser;
    }
    
    public function getUserFavorites() {
        return $this->model->getUserFavorites($this->currentUser['id']);
    }
}

$handler = new ProfileHandler();
$handler->handleRequest();
?>
