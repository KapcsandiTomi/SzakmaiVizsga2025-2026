<?php
session_start();
require_once 'config.php';
require_once 'handler/profiledata.php';

class ProfileHandler {
    private $model;
    private $errors = [];
    private $success = [];
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
        }
    }
    
    private function handleProfileUpdate($post_data) {
        $newName = trim($post_data['name'] ?? '');
        $newEmail = trim($post_data['email'] ?? '');
        
        if (empty($newName) || empty($newEmail)) {
            $this->errors[] = "Both fields are required!";
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format!";
        } elseif (strlen($newName) < 3) {
            $this->errors[] = "Username must be at least 3 characters!";
        } else {
            if ($newEmail !== $this->currentUser['email'] && 
                $this->model->checkEmailExists($newEmail, $this->currentUser['id'])) {
                $this->errors[] = "Email already exists!";
            } else {
                if ($this->model->updateProfile($this->currentUser['id'], $newName, $newEmail)) {
                    $this->success[] = "Profile updated successfully!";
                    $_SESSION['email'] = $newEmail;
                    $this->currentUser['name'] = $newName;
                    $this->currentUser['email'] = $newEmail;
                } else {
                    $this->errors[] = "Update failed!";
                }
            }
        }
    }
    
    private function handlePasswordChange($post_data) {
        $currentPassword = $post_data['current_password'] ?? '';
        $newPassword = $post_data['new_password'] ?? '';
        $confirmPassword = $post_data['confirm_password'] ?? '';
        
        $hashedPassword = $this->model->getCurrentPassword($this->currentUser['id']);
        
        if (!password_verify($currentPassword, $hashedPassword)) {
            $this->errors[] = "The current password is incorrect!";
        } elseif ($newPassword !== $confirmPassword) {
            $this->errors[] = "The new passwords do not match!";
        } elseif (strlen($newPassword) < 6) {
            $this->errors[] = "Password must be at least 6 characters!";
        } else {
            if ($this->model->changePassword($this->currentUser['id'], $newPassword)) {
                $this->success[] = "Password successfully changed!";
            } else {
                $this->errors[] = "Password change failed!";
            }
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
            $this->errors[] = "Only JPG, JPEG, PNG and GIF files are allowed!";
        } elseif ($file_data["size"] > $maxFileSize) {
            $this->errors[] = "File is too large. Maximum size is 5MB!";
        } else {
            if (move_uploaded_file($file_data["tmp_name"], $targetFile)) {
                if ($this->model->updateProfilePicture($this->currentUser['id'], $targetFile)) {
                    $this->model->deleteOldProfilePicture($this->currentUser['profile_pic']);
                    $this->success[] = "Profile picture uploaded successfully!";
                    $this->currentUser['profile_pic'] = $targetFile;
                } else {
                    $this->errors[] = "Failed to update profile picture in database!";
                }
            } else {
                $this->errors[] = "There was an error uploading your file!";
            }
        }
    }
    
    public function getCurrentUser() {
        return $this->currentUser;
    }
    
    public function getUserFavorites() {
        return $this->model->getUserFavorites($this->currentUser['id']);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getSuccessMessages() {
        return $this->success;
    }
}

$handler = new ProfileHandler();
$handler->handleRequest();
?>