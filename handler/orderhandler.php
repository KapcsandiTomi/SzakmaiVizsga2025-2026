<?php
session_start();
require_once 'config.php';
require_once 'orderdata.php';

class OrderHandler {
    private $orderData;
    
    public function __construct() {
        global $conn;
        $this->orderData = new OrderData($conn);
    }

    public function checkAuthentication() {
        if (!isset($_SESSION['email'])) {
            header("Location: index.php");
            exit();
        }
        return $_SESSION['email'];
    }

    public function getCurrentCart() {
        return $_SESSION['order'] ?? [];
    }

    public function calculateCartTotal($cart) {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'];
        }
        return $total;
    }

    public function clearCart() {
        unset($_SESSION['order']);
        return true;
    }

    public function getUserOrders($email) {
        return $this->orderData->getOrdersByEmail($email);
    }

    public function getOrderStatusInfo($status) {
        $statusColors = [
            "Not Processed" => "#ff6b6b",
            "Processed" => "#4ecdc4", 
            "Handed to Courier" => "#45b7d1",
            "On the Way" => "#96ceb4",
            "Delivered" => "#95e1d3"
        ];

        $statusIcons = [
            "Not Processed" => "fas fa-hourglass-half",
            "Processed" => "fas fa-cogs",
            "Handed to Courier" => "fas fa-handshake",
            "On the Way" => "fas fa-shipping-fast",
            "Delivered" => "fas fa-check-circle"
        ];

        return [
            'color' => $statusColors[$status] ?? '#667eea',
            'icon' => $statusIcons[$status] ?? 'fas fa-question'
        ];
    }

    public function calculateProgress($status) {
        switch($status) {
            case 'Not Processed': return 20;
            case 'Processed': return 40;
            case 'Handed to Courier': return 60;
            case 'On the Way': return 80;
            case 'Delivered': return 100;
            default: return 0;
        }
    }

    public function getDaysAgo($date) {
        $orderDate = strtotime($date);
        $currentDate = time();
        $daysDiff = floor(($currentDate - $orderDate) / (60 * 60 * 24));
        
        if ($daysDiff == 0) return 'Today';
        if ($daysDiff == 1) return 'Yesterday';
        return $daysDiff . ' days ago';
    }

    public function handleRequest() {
        $user_email = $this->checkAuthentication();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['clear_order'])) {
                $this->clearCart();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }

        $cart = $this->getCurrentCart();
        $cart_total = $this->calculateCartTotal($cart);
        $orders = $this->getUserOrders($user_email);

        return [
            'email' => $user_email,
            'cart' => $cart,
            'cart_total' => $cart_total,
            'cart_count' => count($cart),
            'orders' => $orders,
            'orders_count' => count($orders)
        ];
    }
}

$handler = new OrderHandler();
$data = $handler->handleRequest();
?>