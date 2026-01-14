<?php
class UserModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getAllUsers() {
        $result = $this->conn->query("SELECT id, name, email, is_admin FROM `4` ORDER BY id ASC");
        $users = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, is_admin FROM `4` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM `4` WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function makeAdmin($id) {
        $stmt = $this->conn->prepare("UPDATE `4` SET is_admin = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function removeAdmin($id) {
        $stmt = $this->conn->prepare("UPDATE `4` SET is_admin = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>