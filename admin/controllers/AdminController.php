<?php
class AdminController {
    private $conn;
    private $userModel;
    private $orderModel;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->userModel = new UserModel($connection);
        $this->orderModel = new OrderModel($connection);
    }
    
    public function index() {
        $users = $this->userModel->getAllUsers();
        $orders = $this->orderModel->getAllOrders();
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
}
?>