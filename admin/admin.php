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
// FELHASZNÁLÓK BETÖLTÉSE
// ====================
$usersResult = $conn->query("SELECT id, name, email, is_admin FROM `4` ORDER BY id ASC");

// ====================
// RENDELÉS ÁLLAPOT FRISSÍTÉS
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
}

// ====================
// RENDELÉS TÖRLÉS
// ====================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    
    $result = $conn->query("SELECT status FROM orders WHERE id = $del_id");
    $row = $result->fetch_assoc();
    
    if (($row['status'] ?? '') === 'Delivered') {
        $conn->query("DELETE FROM orders WHERE id = $del_id");
    }
}

// ====================
// RENDELÉSEK BETÖLTÉSE
// ====================
$ordersResult = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");

// ====================
// HELPER FUNCTIONS
// ====================
function displayProduct($item) {
    $html = '<div class="product">';
    
    // Kép megjelenítése (ha van)
    if (isset($item['image']) && $item['image']) {
        $html .= '<img src="' . htmlspecialchars($item['image']) . '" alt="Product" onerror="this.style.display=\'none\'">';
    }
    
    // Termék neve
    $productName = htmlspecialchars($item['name'] ?? 'Unknown Product');
    
    // Mennyiség és ár
    $quantity = $item['quantity'] ?? 1;
    $price = number_format($item['price'] ?? 0, 2);
    
    $html .= $productName . ' (' . $quantity . '×$' . $price . ')';
    $html .= '</div>';
    
    return $html;
}

function parseOrderData($orderDataJson, $totalPrice) {
    $data = json_decode($orderDataJson, true);
    
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
            'items' => $data,
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
    <title>Aqua Mini Shop - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ==================== */
        /* ALAP STÍLUSOK */
        /* ==================== */
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #f0f2f5; 
            margin: 0; 
            padding: 0; 
        }
        
        /* ==================== */
        /* NAVBAR */
        /* ==================== */
        .navbar { 
            background: #4facfe; 
            color: #fff; 
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        
        .navbar h1 { 
            margin: 0; 
            font-size: 24px; 
            font-weight: bold; 
        }
        
        .navbar a { 
            color: #fff; 
            text-decoration: none; 
            font-weight: bold; 
            margin-left: 15px; 
        }
        
        /* ==================== */
        /* TARTALOM */
        /* ==================== */
        .container { 
            max-width: 1400px; 
            margin: 20px auto; 
        }
        
        .card { 
            border-radius: 15px; 
            box-shadow: 0 6px 20px rgba(0,0,0,0.1); 
            background: #fff; 
            margin-bottom: 30px; 
            padding: 20px; 
        }
        
        .card h2 { 
            margin-bottom: 20px; 
        }
        
        /* ==================== */
        /* TÁBLA STÍLUSOK */
        /* ==================== */
        .table th { 
            background: #4facfe; 
            color: #fff; 
        }
        
        .table tbody tr:hover { 
            background: #e6f7ff; 
            transition: 0.3s; 
        }
        
        /* ==================== */
        /* SPECIFIKUS ELEMEK */
        /* ==================== */
        .status-select { 
            border: none; 
            color: #000; 
            font-weight: bold; 
            padding: 5px 10px; 
            border-radius: 5px; 
            width: 100%; 
        }
        
        .delete-btn { 
            border: none; 
            padding: 5px 10px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-weight: bold; 
            background: #ff4d4d; 
            color: #fff; 
        }
        
        .product img { 
            width: 50px; 
            border-radius: 5px; 
            margin-right: 5px; 
            vertical-align: middle; 
        }
        
        .coupon-badge { 
            background: #28a745; 
            color: white; 
            padding: 2px 8px; 
            border-radius: 10px; 
            font-size: 12px; 
            margin-left: 5px; 
        }
        
        .discount-info { 
            color: #dc3545; 
            font-size: 12px; 
            margin-top: 2px; 
        }
    </style>
</head>
<body>

<!-- ==================== -->
<!-- NAVBAR -->
<!-- ==================== -->
<div class="navbar">
    <h1>⚙️ Admin Panel</h1>
    <div>
        <a href="../fooldal.php" class="btn btn-light">🏠 Home</a>
        <a href="../logout.php" class="btn btn-light">🚪 Logout</a>
    </div>
</div>

<!-- ==================== -->
<!-- FŐ TARTALOM -->
<!-- ==================== -->
<div class="container">

    <!-- ==================== -->
    <!-- FELHASZNÁLÓK LISTA -->
    <!-- ==================== -->
    <div class="card">
        <h2>👥 Users List</h2>
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usersResult->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <?php if ($user['is_admin']): ?>
                            <span class="badge bg-success">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-danger">User</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <?php if (!$user['is_admin']): ?>
                                <a href="make_admin.php?id=<?= $user['id'] ?>" class="btn btn-success btn-sm">
                                    Grant Admin
                                </a>
                            <?php else: ?>
                                <a href="remove_admin.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">
                                    Remove Admin
                                </a>
                            <?php endif; ?>
                            
                            <a href="delete_user.php?id=<?= $user['id'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Delete user?');">
                                Delete
                            </a>
                        <?php else: ?>
                            🔒 You
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- ==================== -->
    <!-- RENDELÉSEK LISTA -->
    <!-- ==================== -->
    <div class="card">
        <h2>📦 Orders List</h2>
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Card Info</th>
                    <th>Total</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Action</th>
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
                ?>
                <tr>
                    <!-- ID -->
                    <td><?= $order['id'] ?></td>
                    
                    <!-- VEVŐ ADATOK -->
                    <td><b><?= htmlspecialchars($order['customer_name']) ?></b></td>
                    <td><b><?= htmlspecialchars($order['customer_email']) ?></b></td>
                    <td><b><?= htmlspecialchars($order['customer_address']) ?></b></td>
                    
                    <!-- KÁRTYA ADATOK -->
                    <td>
                        <b>Type:</b> <?= htmlspecialchars($order['card_type']) ?><br>
                        <b>Number:</b> <?= htmlspecialchars($order['card_number']) ?><br>
                        <b>Expiry:</b> <?= htmlspecialchars($order['expiry']) ?><br>
                        <b>CVV:</b> <?= htmlspecialchars($order['cvv']) ?>
                    </td>
                    
                    <!-- ÁR -->
                    <td>
                        $<?= number_format($order['total_price'], 2) ?>
                        
                        <?php if ($discount > 0): ?>
                            <br><small class="discount-info">
                                Discount: -$<?= number_format($discount, 2) ?>
                            </small>
                        <?php endif; ?>
                        
                        <?php if ($shipping > 0): ?>
                            <br><small>Shipping: $<?= number_format($shipping, 2) ?></small>
                        <?php endif; ?>
                        
                        <?php if ($coupon): ?>
                            <br><small class="coupon-badge">Coupon: <?= htmlspecialchars($coupon) ?></small>
                        <?php endif; ?>
                    </td>
                    
                    <!-- TERMÉKEK -->
                    <td>
                        <?php if (is_array($items)): ?>
                            <?php foreach($items as $item): ?>
                                <?= displayProduct($item) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted">No product data</div>
                        <?php endif; ?>
                    </td>
                    
                    <!-- ÁLLAPOT -->
                    <td style="background: <?= $statusColors[$order['status'] ?? 'Not Processed'] ?? '#fff' ?>;">
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" 
                                    class="status-select" 
                                    onchange="this.form.submit()"
                                    style="background: <?= $statusColors[$order['status'] ?? 'Not Processed'] ?? '#fff' ?>;">
                                <?php foreach($statusOptions as $option): ?>
                                <option value="<?= $option ?>" 
                                        <?= ($order['status'] ?? 'Not Processed') == $option ? 'selected' : '' ?>>
                                    <?= $option ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    
                    <!-- MŰVELETEK -->
                    <td>
                        <?php if (($order['status'] ?? '') === 'Delivered'): ?>
                            <a href="?delete=<?= $order['id'] ?>" 
                               onclick="return confirm('Delete order?');" 
                               class="delete-btn">
                                Delete
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    
                    <!-- DÁTUM -->
                    <td><?= $order['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== -->
<!-- JAVASCRIPT -->
<!-- ==================== -->
<script>
const colors = {
    "Not Processed": "#ff4d4d",
    "Processed": "#ffa500",
    "Handed to Courier": "#1e90ff",
    "On the Way": "#ffff66",
    "Delivered": "#32cd32"
};

// Állapotválasztó színének frissítése
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        this.style.backgroundColor = colors[this.value] || '#fff';
        this.closest('td').style.backgroundColor = colors[this.value] || '#fff';
    });
});
</script>

</body>
</html>