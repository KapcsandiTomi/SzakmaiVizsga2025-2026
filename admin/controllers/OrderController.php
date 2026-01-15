<?php
class OrderController {
    private $conn;
    private $orderModel;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->orderModel = new OrderModel($connection);
    }
    
    public function index() {
        $orders = $this->orderModel->getAllOrders();
        require_once __DIR__ . '/../views/admin/orders.php';
    }
    
    public function updateStatus($postData) {
        session_start();
        
        if (isset($postData['order_id'], $postData['status'])) {
            $orderId = intval($postData['order_id']);
            $status = $postData['status'];
            
            if ($this->orderModel->updateStatus($orderId, $status)) {
                $_SESSION['success'] = "Order status updated successfully!";
            } else {
                $_SESSION['error'] = "Error updating order status!";
            }
        }
        
        header("Location: index.php?page=orders");
        exit();
    }
    
    public function delete($id) {
        session_start();
        
        if ($this->orderModel->deleteOrder($id)) {
            $_SESSION['success'] = "Order deleted successfully!";
        } else {
            $_SESSION['error'] = "Only delivered orders can be deleted!";
        }
        
        header("Location: index.php?page=orders");
        exit();
    }
}
?>
