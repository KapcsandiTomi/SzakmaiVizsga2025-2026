<?php
class ProfileData {
    private $conn;
    private $user;
    
    public function __construct($database_connection, $user_id = null) {
        $this->conn = $database_connection;
        if ($user_id) {
            $this->loadUser($user_id);
        }
    }
    
    public function loadUser($user_id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, profile_pic FROM `4` WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->user = $result->fetch_assoc();
        $stmt->close();
        
        return $this->user;
    }
    
    public function loadUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, name, email, profile_pic FROM `4` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->user = $result->fetch_assoc();
        $stmt->close();
        
        return $this->user;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function getUserFavorites($user_id) {
        $favorites = [];
        $favoritesStmt = $this->conn->prepare("SELECT * FROM favorites WHERE user_id = ? ORDER BY created_at DESC");
        $favoritesStmt->bind_param("i", $user_id);
        $favoritesStmt->execute();
        $favoritesResult = $favoritesStmt->get_result();
        while ($row = $favoritesResult->fetch_assoc()) {
            $favorites[] = $row;
        }
        $favoritesStmt->close();
        
        return $favorites;
    }
    
    public function updateProfile($user_id, $name, $email) {
        $stmt = $this->conn->prepare("UPDATE `4` SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    public function checkEmailExists($email, $user_id) {
        $checkStmt = $this->conn->prepare("SELECT id FROM `4` WHERE email = ? AND id != ?");
        $checkStmt->bind_param("si", $email, $user_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $exists = $checkResult->num_rows > 0;
        $checkStmt->close();
        
        return $exists;
    }
    
    public function changePassword($user_id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE `4` SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    public function getCurrentPassword($user_id) {
        $stmt = $this->conn->prepare("SELECT password FROM `4` WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();
        
        return $hashedPassword;
    }
    
    public function updateProfilePicture($user_id, $profile_pic_path) {
        $stmt = $this->conn->prepare("UPDATE `4` SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $profile_pic_path, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    public function deleteOldProfilePicture($profile_pic_path) {
        if ($profile_pic_path && file_exists($profile_pic_path) && 
            strpos($profile_pic_path, 'uploads/') !== false) {
            return unlink($profile_pic_path);
        }
        return false;
    }
}
?>