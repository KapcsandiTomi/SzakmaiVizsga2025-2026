<?php
session_start();
require_once '../config.php';

// ====================
// JOGOSULTSÁG ELLENŐRZÉS
// ====================
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}

// ====================
// BEÁLLÍTÁSOK
// ====================
$statusOptions = [
    "Not Processed",
    "Processed", 
    "Handed to Courier",
    "On the Way",
    "Delivered"
];

$statusColors = [
    "Not Processed" => "#ff4d4d",
    "Processed" => "#ffa500",
    "Handed to Courier" => "#1e90ff",
    "On the Way" => "#ffff66",
    "Delivered" => "#32cd32"
];

// ====================
// VÁLTOZÓK
// ====================
$message = '';
$error = '';
$usersResult = null;
$ordersResult = null;

// ====================
// FELHASZNÁLÓK BETÖLTÉSE
// ====================
$usersResult = $conn->query("SELECT id, name, email, is_admin FROM `4` ORDER BY id ASC");
if (!$usersResult) {
    $error = "Error loading users: " . $conn->error;
}

// ====================
// RENDELÉS ÁLLAPOT FRISSÍTÉS
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        $message = "Order status updated successfully!";
    } else {
        $error = "Error updating order: " . $stmt->error;
    }
    $stmt->close();
}

// ====================
// RENDELÉS TÖRLÉS
// ====================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    
    // Ellenőrizzük, hogy létezik-e a rendelés
    $checkResult = $conn->query("SELECT status FROM orders WHERE id = $del_id");
    if ($checkResult && $checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        
        if (($row['status'] ?? '') === 'Delivered') {
            if ($conn->query("DELETE FROM orders WHERE id = $del_id")) {
                $message = "Order deleted successfully!";
            } else {
                $error = "Error deleting order: " . $conn->error;
            }
        } else {
            $error = "Only delivered orders can be deleted!";
        }
        $checkResult->free();
    } else {
        $error = "Order not found!";
    }
}

// ====================
// RENDELÉSEK BETÖLTÉSE
// ====================
$ordersResult = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
if (!$ordersResult) {
    $error = "Error loading orders: " . $conn->error;
}

// ====================
// HELPER FUNCTIONS
// ====================
function displayProduct($item) {
    $html = '<div class="product-item">';
    
    // Kép megjelenítése (ha van)
    $image = $item['image'] ?? '';
    if (!empty($image) && file_exists('../' . $image)) {
        $html .= '<img src="../' . htmlspecialchars($image) . '" alt="Product" class="product-image" onerror="this.style.display=\'none\'">';
    } else {
        // Alapértelmezett kép, ha nincs megadva
        $html .= '<img src="../letoles.jpg" alt="Default Product" class="product-image">';
    }
    
    // Termék neve
    $productName = htmlspecialchars($item['name'] ?? 'Unknown Product');
    
    // Mennyiség és ár
    $quantity = $item['quantity'] ?? 1;
    $price = number_format($item['price'] ?? 0, 2);
    
    $html .= '<div class="product-details">';
    $html .= '<span class="product-name">' . $productName . '</span>';
    $html .= '<span class="product-info">' . $quantity . '×$' . $price . '</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

function parseOrderData($orderDataJson, $totalPrice) {
    if (empty($orderDataJson)) {
        return [
            'items' => [],
            'coupon' => null,
            'discount' => 0,
            'shipping' => 0,
            'subtotal' => $totalPrice
        ];
    }
    
    $data = json_decode($orderDataJson, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'items' => [['name' => 'Error parsing order data', 'price' => 0, 'quantity' => 1]],
            'coupon' => null,
            'discount' => 0,
            'shipping' => 0,
            'subtotal' => $totalPrice
        ];
    }
    
    if (isset($data['items'])) {
        // Új formátum (checkout.php-ből)
        return [
            'items' => $data['items'],
            'coupon' => $data['coupon'] ?? null,
            'discount' => $data['discount'] ?? 0,
            'shipping' => $data['shipping_cost'] ?? 0,
            'subtotal' => $data['subtotal'] ?? $totalPrice
        ];
    } else {
        // Régi formátum
        return [
            'items' => is_array($data) ? $data : [],
            'coupon' => null,
            'discount' => 0,
            'shipping' => 0,
            'subtotal' => $totalPrice
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../letoles.jpg" type="image/png">
    <style>
        /* ==================== */
        /* ALAP STÍLUSOK */
        /* ==================== */
        :root {
            --primary: #4facfe;
            --primary-dark: #2a8bf2;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        /* ==================== */
        /* NAVBAR */
        /* ==================== */
        .admin-navbar { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff; 
            padding: 15px 30px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .admin-navbar h1 { 
            margin: 0; 
            font-size: 24px; 
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .nav-links a { 
            color: #fff; 
            text-decoration: none; 
            font-weight: 600; 
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-links a:hover { 
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        /* ==================== */
        /* TARTALOM */
        /* ==================== */
        .admin-container { 
            max-width: 1600px; 
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .admin-card { 
            border-radius: 12px; 
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); 
            background: #fff; 
            margin-bottom: 30px; 
            padding: 25px;
            border: none;
            overflow: hidden;
        }
        
        .admin-card h2 { 
            margin-bottom: 25px;
            color: var(--primary-dark);
            padding-bottom: 15px;
            border-bottom: 3px solid #f0f0f0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* ==================== */
        /* ALERTS */
        /* ==================== */
        .alert-container {
            margin-bottom: 25px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid var(--success);
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid var(--danger);
        }
        
        /* ==================== */
        /* TÁBLA STÍLUSOK */
        /* ==================== */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95em;
        }
        
        .admin-table thead th { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            font-weight: 600;
            padding: 15px 12px;
            text-align: left;
            border: none;
            position: sticky;
            top: 0;
        }
        
        .admin-table tbody tr { 
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }
        
        .admin-table tbody tr:hover { 
            background: #f8faff;
            transform: scale(1.002);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        .admin-table tbody td { 
            padding: 12px;
            vertical-align: middle;
            border: none;
        }
        
        /* ==================== */
        /* SPECIFIKUS ELEMEK */
        /* ==================== */
        .status-cell {
            padding: 0 !important;
        }
        
        .status-form {
            margin: 0;
            height: 100%;
        }
        
        .status-select { 
            border: none; 
            color: #000; 
            font-weight: 600; 
            padding: 12px;
            width: 100%;
            height: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }
        
        .status-select:focus {
            outline: none;
            box-shadow: inset 0 0 0 2px var(--primary);
        }
        
        .delete-btn { 
            background: var(--danger);
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85em;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .delete-btn:hover { 
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        /* ==================== */
        /* TERMÉK ELEMEK KÉPEKKEL */
        /* ==================== */
        .product-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px dashed #eaeaea;
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e0f7fa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }
        
        .product-details {
            flex: 1;
            min-width: 0; /* Prevents overflow */
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 3px;
            font-size: 0.9em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .product-info {
            font-size: 0.8em;
            color: var(--secondary);
            display: block;
        }
        
        /* ==================== */
        /* BADGES */
        /* ==================== */
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8em;
        }
        
        .admin-badge {
            background: var(--success);
            color: white;
        }
        
        .user-badge {
            background: var(--secondary);
            color: white;
        }
        
        .coupon-badge { 
            background: var(--info); 
            color: white; 
            padding: 3px 8px; 
            border-radius: 10px; 
            font-size: 0.75em; 
            display: inline-block;
            margin-top: 5px;
        }
        
        /* ==================== */
        /* ACTION BUTTONS */
        /* ==================== */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85em;
            border-radius: 6px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-success-sm {
            background: var(--success);
            color: white;
            border: none;
        }
        
        .btn-warning-sm {
            background: var(--warning);
            color: #212529;
            border: none;
        }
        
        .btn-danger-sm {
            background: var(--danger);
            color: white;
            border: none;
        }
        
        .btn-success-sm:hover {
            background: #218838;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-warning-sm:hover {
            background: #e0a800;
            color: #212529;
            transform: translateY(-2px);
        }
        
        .btn-danger-sm:hover {
            background: #c82333;
            color: white;
            transform: translateY(-2px);
        }
        
        /* ==================== */
        /* CARD INFO */
        /* ==================== */
        .card-info {
            font-size: 0.9em;
            line-height: 1.5;
        }
        
        .card-info b {
            color: var(--primary-dark);
        }
        
        /* ==================== */
        /* PRICE INFO */
        /* ==================== */
        .price-info {
            font-size: 1.1em;
            font-weight: 700;
            color: var(--primary-dark);
        }
        
        .discount-info { 
            color: var(--danger); 
            font-size: 0.85em; 
            margin-top: 3px;
            display: block;
        }
        
        .shipping-info {
            color: var(--secondary);
            font-size: 0.85em;
            display: block;
        }
        
        /* ==================== */
        /* DATE */
        /* ==================== */
        .order-date {
            font-size: 0.85em;
            color: var(--secondary);
            white-space: nowrap;
        }
        
        /* ==================== */
        /* PRODUCTS COLUMN SPECIFIC */
        /* ==================== */
        .products-column {
            max-width: 300px;
            min-width: 250px;
        }
        
        .products-list {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .products-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .products-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .products-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .products-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* ==================== */
        /* RESPONSIVE */
        /* ==================== */
        @media (max-width: 1200px) {
            .admin-container {
                padding: 0 15px;
            }
            
            .admin-table {
                font-size: 0.9em;
            }
            
            .admin-table thead th,
            .admin-table tbody td {
                padding: 10px 8px;
            }
            
            .product-image {
                width: 40px;
                height: 40px;
            }
        }
        
        @media (max-width: 992px) {
            .admin-navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .nav-links {
                width: 100%;
                justify-content: center;
            }
            
            .admin-card {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .products-column {
                max-width: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .admin-table-container {
                overflow-x: auto;
            }
            
            .admin-table {
                min-width: 1000px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
        
        /* ==================== */
        /* LOADING & EMPTY STATES */
        /* ==================== */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--secondary);
        }
        
        .empty-state i {
            font-size: 3em;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- ==================== -->
<!-- NAVBAR -->
<!-- ==================== -->
<div class="admin-navbar">
    <h1><i class="fas fa-cogs"></i> Admin Panel</h1>
    <div class="nav-links">
        <a href="../fooldal.php"><i class="fas fa-home"></i> Home</a>
        <a href="../products.php"><i class="fas fa-store"></i> Products</a>
        <a href="pc_admin.php"><i class="fas fa-microchip"></i> PC Config Admin</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- ==================== -->
<!-- FŐ TARTALOM -->
<!-- ==================== -->
<div class="admin-container">
    
    <!-- ==================== -->
    <!-- ALERTS -->
    <!-- ==================== -->
    <?php if (!empty($message)): ?>
        <div class="alert-container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert-container">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ==================== -->
    <!-- FELHASZNÁLÓK LISTA -->
    <!-- ==================== -->
    <div class="admin-card">
        <h2><i class="fas fa-users"></i> Users Management</h2>
        
        <?php if ($usersResult && $usersResult->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $usersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['is_admin']): ?>
                                <span class="badge admin-badge">Admin</span>
                            <?php else: ?>
                                <span class="badge user-badge">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <?php if (!$user['is_admin']): ?>
                                        <a href="make_admin.php?id=<?php echo $user['id']; ?>" 
                                           class="btn-sm btn-success-sm">
                                            <i class="fas fa-user-shield"></i> Grant Admin
                                        </a>
                                    <?php else: ?>
                                        <a href="remove_admin.php?id=<?php echo $user['id']; ?>" 
                                           class="btn-sm btn-warning-sm">
                                            <i class="fas fa-user-minus"></i> Remove Admin
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                       class="btn-sm btn-danger-sm" 
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-user"></i> Current User</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <h4>No Users Found</h4>
                <p>There are no users in the database.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ==================== -->
    <!-- RENDELÉSEK LISTA -->
    <!-- ==================== -->
    <div class="admin-card">
        <h2><i class="fas fa-shopping-cart"></i> Orders Management</h2>
        
        <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Info</th>
                        <th>Payment Info</th>
                        <th>Price Details</th>
                        <th class="products-column">Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $ordersResult->fetch_assoc()): 
                        // RENDELÉS ADATOK FELDOLGOZÁSA
                        $orderInfo = parseOrderData($order['order_data'], $order['total_price']);
                        $items = $orderInfo['items'];
                        $coupon = $orderInfo['coupon'];
                        $discount = $orderInfo['discount'];
                        $shipping = $orderInfo['shipping'];
                        $currentStatus = $order['status'] ?? 'Not Processed';
                        $statusColor = $statusColors[$currentStatus] ?? '#ffffff';
                    ?>
                    <tr>
                        <!-- ORDER ID -->
                        <td><strong>#<?php echo htmlspecialchars($order['id']); ?></strong></td>
                        
                        <!-- CUSTOMER INFO -->
                        <td>
                            <div><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></div>
                            <div class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars(substr($order['customer_address'], 0, 50)); ?>...</div>
                        </td>
                        
                        <!-- PAYMENT INFO -->
                        <td>
                            <div class="card-info">
                                <div><b>Type:</b> <?php echo htmlspecialchars($order['card_type']); ?></div>
                                <div><b>Card:</b> <?php echo htmlspecialchars($order['card_number']); ?></div>
                                <div><b>Expiry:</b> <?php echo htmlspecialchars($order['expiry']); ?></div>
                                <div><b>CVV:</b> <?php echo htmlspecialchars($order['cvv']); ?></div>
                            </div>
                        </td>
                        
                        <!-- PRICE DETAILS -->
                        <td>
                            <div class="price-info">$<?php echo number_format($order['total_price'], 2); ?></div>
                            
                            <?php if ($discount > 0): ?>
                                <span class="discount-info">
                                    <i class="fas fa-tag"></i> Discount: -$<?php echo number_format($discount, 2); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($shipping > 0): ?>
                                <span class="shipping-info">
                                    <i class="fas fa-shipping-fast"></i> Shipping: $<?php echo number_format($shipping, 2); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($coupon): ?>
                                <span class="coupon-badge">
                                    <i class="fas fa-ticket-alt"></i> Coupon: <?php echo htmlspecialchars($coupon); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- PRODUCTS - MOST KÉPEKKEL -->
                        <td class="products-column">
                            <?php if (is_array($items) && count($items) > 0): ?>
                                <div class="products-list">
                                    <?php foreach($items as $index => $item): ?>
                                        <?php echo displayProduct($item); ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">No product data</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- STATUS -->
                        <td class="status-cell" style="background: <?php echo $statusColor; ?>;">
                            <form method="POST" class="status-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" 
                                        class="status-select" 
                                        onchange="this.form.submit()"
                                        style="background: <?php echo $statusColor; ?>;">
                                    <?php foreach($statusOptions as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>" 
                                            <?php echo ($currentStatus == $option) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        
                        <!-- ACTIONS -->
                        <td>
                            <?php if ($currentStatus === 'Delivered'): ?>
                                <button onclick="deleteOrder(<?php echo $order['id']; ?>)" 
                                        class="delete-btn">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- DATE -->
                        <td>
                            <div class="order-date">
                                <?php 
                                $date = new DateTime($order['created_at']);
                                echo $date->format('M d, Y');
                                ?>
                                <br>
                                <small><?php echo $date->format('H:i:s'); ?></small>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h4>No Orders Found</h4>
                <p>There are no orders in the database.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==================== -->
<!-- JAVASCRIPT -->
<!-- ==================== -->
<script>
// Status selector colors
const statusColors = {
    "Not Processed": "#ff4d4d",
    "Processed": "#ffa500",
    "Handed to Courier": "#1e90ff",
    "On the Way": "#ffff66",
    "Delivered": "#32cd32"
};

// Update status selector colors on change
document.querySelectorAll('.status-select').forEach(select => {
    // Set initial color
    select.style.backgroundColor = statusColors[select.value] || '#ffffff';
    select.style.color = '#000000';
    
    // Update on change
    select.addEventListener('change', function() {
        this.style.backgroundColor = statusColors[this.value] || '#ffffff';
        this.closest('td').style.backgroundColor = statusColors[this.value] || '#ffffff';
        
        // Show loading indicator
        const originalText = this.value;
        this.innerHTML = '<option disabled><div class="loading-spinner"></div> Updating...</option>';
        
        // Submit form after a short delay for UX
        setTimeout(() => {
            this.closest('form').submit();
        }, 300);
    });
});

// Delete order function
function deleteOrder(orderId) {
    if (confirm('Are you sure you want to delete order #' + orderId + '? This action cannot be undone.')) {
        window.location.href = '?delete=' + orderId;
    }
}

// Auto-hide alerts after 5 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 500);
    });
}, 5000);

// Handle image errors
document.querySelectorAll('.product-image').forEach(img => {
    img.addEventListener('error', function() {
        this.src = '../letoles.jpg';
        this.alt = 'Default Product Image';
    });
});
</script>

</body>
</html>