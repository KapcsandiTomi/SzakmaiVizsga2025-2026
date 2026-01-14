<?php
class OrderModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getAllOrders() {
        $result = $this->conn->query("SELECT * FROM orders ORDER BY created_at DESC");
        $orders = [];
        
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    public function updateStatus($orderId, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }
    
    public function deleteOrder($orderId) {
        $check = $this->conn->query("SELECT status FROM orders WHERE id = $orderId");
        $row = $check->fetch_assoc();
        
        if ($row['status'] === 'Delivered') {
            $stmt = $this->conn->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param("i", $orderId);
            return $stmt->execute();
        }
        
        return false;
    }
    
    public function getOrderById($orderId) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}
?>