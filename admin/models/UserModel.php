<?php
class UserModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT id, name, email, is_admin FROM `4` ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, is_admin FROM `4` WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM `4` WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function makeAdmin($id) {
        $stmt = $this->conn->prepare("UPDATE `4` SET is_admin = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function removeAdmin($id) {
        $stmt = $this->conn->prepare("UPDATE `4` SET is_admin = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
