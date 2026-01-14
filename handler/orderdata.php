<?php
class OrderData {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function getOrdersByEmail($email) {
        $orders = [];
        
        $stmt = $this->conn->prepare("
            SELECT id, total_price, status, created_at, customer_name 
            FROM orders 
            WHERE customer_email = ? 
            ORDER BY created_at DESC
        ");
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($order = $result->fetch_assoc()) {
            $orders[] = $order;
        }
        
        $stmt->close();
        return $orders;
    }

    public function getOrderById($order_id, $email) {
        $stmt = $this->conn->prepare("
            SELECT * FROM orders 
            WHERE id = ? AND customer_email = ?
        ");
        
        $stmt->bind_param("is", $order_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $order = $result->fetch_assoc();
        $stmt->close();
        
        return $order;
    }

    public function createOrder($order_data) {
        $stmt = $this->conn->prepare("
            INSERT INTO orders 
            (customer_email, customer_name, total_price, status, created_at) 
            VALUES (?, ?, ?, 'Not Processed', NOW())
        ");
        
        $stmt->bind_param(
            "ssd",
            $order_data['email'],
            $order_data['name'],
            $order_data['total_price']
        );
        
        $success = $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();
        
        return $success ? $order_id : false;
    }

    public function updateOrderStatus($order_id, $status, $email) {
        $stmt = $this->conn->prepare("
            UPDATE orders 
            SET status = ? 
            WHERE id = ? AND customer_email = ?
        ");
        
        $stmt->bind_param("sis", $status, $order_id, $email);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function deleteOrder($order_id, $email) {
        $stmt = $this->conn->prepare("
            DELETE FROM orders 
            WHERE id = ? AND customer_email = ?
        ");
        
        $stmt->bind_param("is", $order_id, $email);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
}
?>