<?php
session_start();
require_once __DIR__ . '/../config.php';

// Maintenance check
require_once __DIR__ . '/../handler/maintenance_check.php';

class OrderData {
    private PDO $conn;

    public function __construct(PDO $db_connection) {
        $this->conn = $db_connection;
    }

    // ====================
    // RENDELÉSEK EMAIL ALAPJÁN
    // ====================
    public function getOrdersByEmail(string $email): array {
        $stmt = $this->conn->prepare(
            "SELECT id, total_price, status, created_at, customer_name
             FROM orders
             WHERE customer_email = :email
             ORDER BY created_at DESC"
        );

        $stmt->execute(['email' => $email]);
        return $stmt->fetchAll();
    }

    // ====================
    // RENDELÉS LEKÉRÉS ID + EMAIL
    // ====================
    public function getOrderById(int $order_id, string $email): ?array {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM orders
             WHERE id = :id AND customer_email = :email"
        );

        $stmt->execute([
            'id'    => $order_id,
            'email' => $email
        ]);

        $order = $stmt->fetch();
        return $order ?: null;
    }

    // ====================
    // RENDELÉS LÉTREHOZÁS
    // ====================
    public function createOrder(array $order_data): int|false {
        $stmt = $this->conn->prepare(
            "INSERT INTO orders
            (customer_email, customer_name, total_price, status, created_at)
            VALUES (:email, :name, :total_price, 'Not Processed', NOW())"
        );

        $success = $stmt->execute([
            'email'       => $order_data['email'],
            'name'        => $order_data['name'],
            'total_price' => $order_data['total_price']
        ]);

        return $success ? (int)$this->conn->lastInsertId() : false;
    }

    // ====================
    // RENDELÉS STÁTUSZ FRISSÍTÉS
    // ====================
    public function updateOrderStatus(
        int $order_id,
        string $status,
        string $email
    ): bool {
        $stmt = $this->conn->prepare(
            "UPDATE orders
             SET status = :status
             WHERE id = :id AND customer_email = :email"
        );

        return $stmt->execute([
            'status' => $status,
            'id'     => $order_id,
            'email'  => $email
        ]);
    }

    // ====================
    // RENDELÉS TÖRLÉS
    // ====================
    public function deleteOrder(int $order_id, string $email): bool {
        $stmt = $this->conn->prepare(
            "DELETE FROM orders
             WHERE id = :id AND customer_email = :email"
        );

        return $stmt->execute([
            'id'    => $order_id,
            'email' => $email
        ]);
    }
}

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
            $price = isset($item['quantity']) ? $item['price'] * $item['quantity'] : $item['price'];
            $total += $price;
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
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - My Orders</title>
    <link rel="stylesheet" href="../assets/css/myorder.css">
    <link rel="stylesheet" href="../assets/css/user-navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
    <style>
        :root {
            --aqua-primary: #00b4d8;
            --aqua-secondary: #0077b6;
            --aqua-light: #90e0ef;
            --aqua-dark: #03045e;
            --aqua-gradient: linear-gradient(135deg, #00b4d8 0%, #0077b6 100%);
            --aqua-gradient-light: linear-gradient(135deg, #90e0ef 0%, #00b4d8 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(0, 180, 216, 0.1);
            --shadow-light: 0 8px 32px rgba(0, 116, 217, 0.07);
            --shadow-medium: 0 15px 35px rgba(0, 119, 182, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: cursive;
        }
        
        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            color: #1e3a8a;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .hero-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            padding: 30px 0;
        }
        
        .aqua-title {
            font-size: 2.8rem;
            font-weight: 900;
            background: var(--aqua-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }
        
        .wave-decoration {
            height: 15px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%2300b4d8' fill-opacity='0.2' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            margin-top: 15px;
        }
        
        .dashboard-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 119, 182, 0.15);
            border-color: var(--aqua-light);
        }
        
        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--aqua-gradient);
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            background: var(--aqua-gradient-light);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .card-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--aqua-dark);
            margin-bottom: 10px;
        }
        
        .card-value {
            font-size: 2.2rem;
            font-weight: 900;
            background: var(--aqua-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .card-subtext {
            color: #64748b;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        
        .cart-section {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: var(--shadow-medium);
            border: 2px solid #e0f2fe;
            grid-column: 2;
            grid-row: 1 / span 2;
            height: fit-content;
        }
        
        .section-title-aqua {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f9ff;
        }
        
        .section-title-aqua i {
            width: 45px;
            height: 45px;
            background: var(--aqua-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }
        
        .section-title-aqua h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--aqua-dark);
            margin: 0;
        }
        
        .cart-items-compact {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 25px;
        }
        
        .cart-item-compact {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .cart-item-compact:hover {
            border-color: var(--aqua-light);
            box-shadow: 0 5px 15px rgba(0, 180, 216, 0.1);
        }
        
        .cart-item-img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--aqua-dark);
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--aqua-primary);
        }
        
        .cart-total-compact {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 20px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .total-display-compact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .total-label-compact {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--aqua-dark);
        }
        
        .total-amount-compact {
            font-size: 2rem;
            font-weight: 800;
            background: var(--aqua-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .cart-actions-compact {
            display: flex;
            gap: 15px;
        }
        
        .btn-compact {
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            flex: 1;
            justify-content: center;
        }
        
        .btn-danger-compact {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 3px 10px rgba(239, 68, 68, 0.2);
        }
        
        .btn-danger-compact:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        }
        
        .btn-success-compact {
            background: var(--aqua-gradient);
            color: white;
            box-shadow: 0 3px 10px rgba(0, 180, 216, 0.2);
        }
        
        .btn-success-compact:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 180, 216, 0.3);
        }
        
        .orders-main-section {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: var(--shadow-medium);
            border: 2px solid #e0f2fe;
            grid-column: 1;
        }
        
        .orders-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f9ff;
        }
        
        .orders-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .orders-title i {
            width: 50px;
            height: 50px;
            background: var(--aqua-gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }
        
        .orders-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--aqua-dark);
            margin: 0;
        }
        
        .orders-count {
            background: var(--aqua-gradient);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 3px 10px rgba(0, 180, 216, 0.2);
        }
        
        .orders-horizontal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(550px, 1fr));
            gap: 25px;
        }
        
        .order-card-horizontal {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
            border-radius: 20px;
            padding: 30px;
            transition: all 0.4s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .order-card-horizontal:hover {
            transform: translateY(-5px);
            border-color: var(--aqua-light);
            box-shadow: 0 15px 30px rgba(0, 180, 216, 0.1);
        }
        
        .order-card-horizontal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--aqua-gradient);
        }
        
        .order-header-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .order-id-horizontal {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--aqua-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .order-id-horizontal i {
            font-size: 1.2rem;
            background: var(--aqua-light);
            padding: 10px;
            border-radius: 10px;
        }
        
        .order-status-horizontal {
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            color: white;
        }
        
        .order-details-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
            padding: 20px;
            background: white;
            border-radius: 15px;
            border: 2px solid #e0f2fe;
        }
        
        .detail-item-horizontal {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .detail-label-horizontal {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .detail-value-horizontal {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--aqua-dark);
        }
        
        .detail-price-horizontal {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--aqua-primary);
        }
        
        .order-progress-horizontal {
            margin: 20px 0;
        }
        
        .progress-label-horizontal {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .progress-bar-horizontal {
            height: 8px;
            background: #e0f2fe;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill-horizontal {
            height: 100%;
            background: var(--aqua-gradient);
            border-radius: 4px;
            transition: width 1s ease;
        }
        
        .order-footer-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e0f2fe;
        }
        
        .track-btn-horizontal {
            padding: 12px 30px;
            background: var(--aqua-gradient);
            color: white;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 180, 216, 0.2);
        }
        
        .track-btn-horizontal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 180, 216, 0.3);
            color: white;
        }
        
        .days-ago-horizontal {
            font-size: 0.85rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .days-ago-horizontal i {
            color: var(--aqua-primary);
        }
        
        .empty-state-compact {
            text-align: center;
            padding: 50px 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
            border-radius: 25px;
            border: 3px dashed var(--aqua-light);
            grid-column: 1 / -1;
        }
        
        .empty-icon-compact {
            font-size: 3.5rem;
            margin-bottom: 20px;
            color: var(--aqua-light);
        }
        
        .empty-title-compact {
            font-size: 1.5rem;
            color: var(--aqua-dark);
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .empty-text-compact {
            color: #64748b;
            max-width: 400px;
            margin: 0 auto 25px;
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .btn-shop-compact {
            padding: 15px 35px;
            background: var(--aqua-gradient);
            color: white;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 180, 216, 0.2);
            font-size: 1rem;
        }
        
        .btn-shop-compact:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 180, 216, 0.3);
            color: white;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @media (max-width: 1200px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }
            
            .cart-section {
                grid-column: 1;
                grid-row: auto;
            }
            
            .orders-horizontal-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 20px 15px;
            }
            
            .aqua-title {
                font-size: 2.2rem;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .order-details-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .orders-horizontal-grid {
                grid-template-columns: 1fr;
            }
            
            .order-header-horizontal {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-footer-horizontal {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .cart-actions-compact {
                flex-direction: column;
            }
            
            .cart-section, .orders-main-section {
                padding: 25px 20px;
            }
        }
        
        .cart-items-compact::-webkit-scrollbar {
            width: 6px;
        }
        
        .cart-items-compact::-webkit-scrollbar-track {
            background: #f0f9ff;
            border-radius: 3px;
        }
        
        .cart-items-compact::-webkit-scrollbar-thumb {
            background: var(--aqua-primary);
            border-radius: 3px;
        }
        
        .cart-items-compact::-webkit-scrollbar-thumb:hover {
            background: var(--aqua-secondary);
        }
    </style>
</head>
<body>

<div class="container-fluid bg-light p-0">
    <div class="row gx-0 d-none d-lg-flex">
      <div class="col-lg-7 px-5 text-start">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-map-marker-alt text-primary me-2"></small>
          <small>Gardonyi Road, Isaszeg, Hungary</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center py-3">
          <small class="far fa-clock text-primary me-2"></small>
          <small>Mon - Sat: 09:00 AM - 07:00 PM</small>
        </div>
      </div>
      <div class="col-lg-5 px-5 text-end">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-phone-alt text-primary me-2"></small>
          <small>+36 70 123 4567</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center">
          <a class="btn btn-sm-square bg-white text-primary me-1" href="https://www.facebook.com/groups/780808788334912"><i class="fab fa-facebook-f"></i></a>
          <a class="btn btn-sm-square bg-white text-primary me-1" href="https://x.com/tamas_kapc343"><i class="fa-brands fa-x-twitter"></i></a>
          <a class="btn btn-sm-square bg-white text-primary me-0" href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>

<?php
$currentPage = 'myorder.php';
include __DIR__ . '/includes/user-navbar.php';
?>

<div class="main-container">
    
    <div class="hero-header">
        <h1 class="aqua-title">My Orders</h1>
        <p class="text-muted fs-5">Manage your purchases and track orders in real-time</p>
        <div class="wave-decoration"></div>
    </div>
    
    <div class="dashboard-layout">
        
        <div class="orders-main-section">
            
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-title">Current Cart</div>
                    <div class="card-value"><?php echo $data['cart_count']; ?></div>
                    <div class="card-subtext">Items waiting for checkout</div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="card-title">Total Orders</div>
                    <div class="card-value"><?php echo $data['orders_count']; ?></div>
                    <div class="card-subtext">All-time completed orders</div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-title">Cart Total</div>
                    <div class="card-value">$<?php echo number_format($data['cart_total'], 2); ?></div>
                    <div class="card-subtext">Current cart value</div>
                </div>
            </div>
            
            <div class="orders-header">
                <div class="orders-title">
                    <i class="fas fa-history"></i>
                    <h2>Order History</h2>
                </div>
                <div class="orders-count">
                    <i class="fas fa-list-ol"></i>
                    <?php echo $data['orders_count']; ?> Orders
                </div>
            </div>
            
            <?php if (empty($data['orders'])): ?>
                <div class="empty-state-compact">
                    <div class="empty-icon-compact animate-float">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="empty-title-compact">No Orders Yet</h3>
                    <p class="empty-text-compact">Your completed orders will appear here with tracking information</p>
                    <a href="products.php" class="btn-shop-compact">
                        <i class="fas fa-bolt"></i> Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="orders-horizontal-grid">
                    <?php foreach ($data['orders'] as $index => $order): 
                        $statusInfo = $handler->getOrderStatusInfo($order['status']);
                        $progress = $handler->calculateProgress($order['status']);
                        $daysAgo = $handler->getDaysAgo($order['created_at']);
                    ?>
                        <div class="order-card-horizontal">
                            <div class="order-header-horizontal">
                                <div class="order-id-horizontal">
                                    <i class="fas fa-hashtag"></i>
                                    #<?php echo htmlspecialchars($order['id']); ?>
                                </div>
                                <div class="order-status-horizontal" style="background: <?php echo $statusInfo['color']; ?>;">
                                    <i class="<?php echo $statusInfo['icon']; ?>"></i>
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </div>
                            </div>
                            
                            <div class="order-details-grid">
                                <div class="detail-item-horizontal">
                                    <span class="detail-label-horizontal">Customer</span>
                                    <span class="detail-value-horizontal"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                                </div>
                                <div class="detail-item-horizontal">
                                    <span class="detail-label-horizontal">Order Date</span>
                                    <span class="detail-value-horizontal"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                </div>
                                <div class="detail-item-horizontal">
                                    <span class="detail-label-horizontal">Order Time</span>
                                    <span class="detail-value-horizontal"><?php echo date('H:i', strtotime($order['created_at'])); ?></span>
                                </div>
                                <div class="detail-item-horizontal">
                                    <span class="detail-label-horizontal">Total Amount</span>
                                    <span class="detail-value-horizontal detail-price-horizontal">$<?php echo number_format($order['total_price'], 2); ?></span>
                                </div>
                            </div>
                            
                            <div class="order-progress-horizontal">
                                <div class="progress-label-horizontal">
                                    <span>Order Progress</span>
                                    <span><?php echo $progress; ?>%</span>
                                </div>
                                <div class="progress-bar-horizontal">
                                    <div class="progress-fill-horizontal" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="order-footer-horizontal">
                                <div class="days-ago-horizontal">
                                    <i class="far fa-clock"></i>
                                    <?php echo $daysAgo; ?>
                                </div>
                                <a href="track.php?id=<?php echo $order['id']; ?>" class="track-btn-horizontal">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Track Order
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="cart-section">
            <div class="section-title-aqua">
                <i class="fas fa-shopping-basket"></i>
                <h2>Your Shopping Cart</h2>
            </div>
            
            <?php if (empty($data['cart'])): ?>
                <div class="empty-state-compact">
                    <div class="empty-icon-compact animate-float">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <h3 class="empty-title-compact">Your Cart is Empty</h3>
                    <p class="empty-text-compact">Add amazing products to your cart</p>
                    <a href="products.php" class="btn-shop-compact">
                        <i class="fas fa-store"></i> Browse Products
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-items-compact">
                    <?php foreach ($data['cart'] as $index => $item): ?>
                        <div class="cart-item-compact">
                            <img src="<?= htmlspecialchars($item['image']) ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>" 
                                 class="cart-item-img"
                                 onerror="this.src='letoles.jpg'">
                            <div class="cart-item-info">
                                <h3 class="cart-item-name"><?= htmlspecialchars($item['name']) ?></h3>
                                <div class="cart-item-price">
                                    $<?= number_format($item['price'], 2) ?>
                                    <?php if (isset($item['quantity']) && $item['quantity'] > 1): ?>
                                        <span style="font-size: 0.85rem; color: #64748b;">
                                            (<?= htmlspecialchars($item['quantity']) ?>×)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-total-compact">
                    <div class="total-display-compact">
                        <div class="total-label-compact">Total Amount</div>
                        <div class="total-amount-compact">$<?= number_format($data['cart_total'], 2) ?></div>
                    </div>
                    
                    <div class="cart-actions-compact">
                        <form method="post" style="flex: 1;" id="clearCartForm">
                            <button type="button" class="btn-compact btn-danger-compact" onclick="showClearCartConfirm()">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                            <input type="hidden" name="clear_order" value="1">
                        </form>
                        <a href="checkout.php" class="btn-compact btn-success-compact" style="flex: 1;">
                            <i class="fas fa-lock"></i> Checkout
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<br>
<footer>
  <div class="container">
    <div class="footer-col">
      <h2>OUR MOTTO<div class="underline"><span></span></div></h2>
      <p class="footer-para">Innovation starts here – with machines designed for creators and professionals who demand more. Whether you're editing, rendering, or building the future, our systems are ready to keep up with your vision.</p>
    </div>
    <div class="footer-col">
      <h3 class="text-office">
        Office<div class="underline"><span></span></div>
      </h3>
      <p>Street No 8</p><p>Gárdonyi Géza</p><p>Isaszeg, 2117, Hungary</p>
      <p class="email">aquaminishop@gmail.com</p>
      <p class="phone">+36 70 123 4567</p>
    </div>
    <div class="footer-col">
      <h3>Menu<div class="underline"><span></span></div></h3>
      <ul>
        <li><a href="fooldal.php">Home</a></li><li><a href="products.php">Products</a></li><li><a href="about.php">About us</a></li><li><a href="faq.php">FAQ</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h2>FOLLOW US<div class="underline"><span></span></div></h2>
      <div class="social-icons">
        <a href="https://www.facebook.com/groups/780808788334912"><i class="fab fa-facebook-f"></i></a>
        <a href="https://x.com/tamas_kapc343"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
</footer>

<script>
document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function() {
        this.src = 'letoles.jpg';
        this.alt = 'Default Product Image';
        this.style.border = '3px solid #ffd43b';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-fill-horizontal');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });
    
    const cards = document.querySelectorAll('.dashboard-card, .cart-item-compact, .order-card-horizontal');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    const trackButtons = document.querySelectorAll('.track-btn-horizontal');
    trackButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });
    
    const orderCards = document.querySelectorAll('.order-card-horizontal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    orderCards.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        item.style.transitionDelay = (index * 0.1) + 's';
        observer.observe(item);
    });
    
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    dashboardCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(15px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        card.style.transitionDelay = (index * 0.15) + 's';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });
    
    const wave = document.querySelector('.wave-decoration');
    if (wave) {
        let position = 0;
        function animateWave() {
            position = (position + 0.5) % 100;
            wave.style.backgroundPosition = position + '% 0';
            requestAnimationFrame(animateWave);
        }
        setTimeout(animateWave, 1000);
    }
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId !== '#') {
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        }
    });
});
</script>

<!-- Clear Cart confirmation modal -->
<div class="modal fade" id="clearCartModal" tabindex="-1" aria-labelledby="clearCartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:16px 16px 0 0;border:none;">
        <h5 class="modal-title" id="clearCartModalLabel" style="color:white;font-weight:700;">
          <i class="fas fa-trash me-2"></i> Clear Cart 🗑️
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding:25px;text-align:center;font-size:1.05rem;color:#1e3a8a;">
        <i class="fas fa-exclamation-triangle" style="font-size:2.5rem;color:#ef4444;margin-bottom:15px;display:block;"></i>
        Are you sure you want to remove all items from your cart? This action cannot be undone.
      </div>
      <div class="modal-footer" style="border:none;justify-content:center;gap:15px;padding-bottom:20px;">
        <button type="button" class="btn-compact btn-success-compact" style="flex:0;padding:10px 30px;" data-bs-dismiss="modal">
          <i class="fas fa-arrow-left"></i> Keep Items
        </button>
        <button type="button" class="btn-compact btn-danger-compact" style="flex:0;padding:10px 30px;" onclick="document.getElementById('clearCartForm').submit();">
          <i class="fas fa-trash"></i> Yes, Clear It
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function showClearCartConfirm() {
    var modal = new bootstrap.Modal(document.getElementById('clearCartModal'));
    modal.show();
}
</script>

<script src="../assets/js/javas.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/chat.js"></script>
<script src="../assets/js/myorder.js"></script>
</body>
</html>

