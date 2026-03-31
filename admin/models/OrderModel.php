<?php
class OrderModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getAllOrders() {
        $stmt = $this->conn->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($orderId, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }
    
    public function deleteOrder($orderId) {
        $stmt = $this->conn->prepare("SELECT status FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['status'] === 'Delivered') {
            $stmt = $this->conn->prepare("DELETE FROM orders WHERE id = ?");
            return $stmt->execute([$orderId]);
        }
        
        return false;
    }
    
    public function getOrderById($orderId) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
