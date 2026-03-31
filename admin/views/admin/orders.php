<?php
if (!isset($orders)) {
    $orders = [];
}

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
        return [
            'items' => $data['items'],
            'coupon' => $data['coupon'] ?? null,
            'discount' => $data['discount'] ?? 0,
            'shipping' => $data['shipping_cost'] ?? 0,
            'subtotal' => $data['subtotal'] ?? $totalPrice
        ];
    } else {
        return [
            'items' => is_array($data) ? $data : [],
            'coupon' => null,
            'discount' => 0,
            'shipping' => 0,
            'subtotal' => $totalPrice
        ];
    }
}

function displayProduct($item) {
    $html = '<div class="product-item">';
    
    $image = $item['image'] ?? '';
    $productName = htmlspecialchars($item['name'] ?? 'Unknown Product');
    $quantity = $item['quantity'] ?? 1;
    $price = number_format($item['price'] ?? 0, 2);
    
    if (!empty($image)) {
        $rootPath = dirname(dirname(dirname(__DIR__)));
        
        $possiblePaths = [
            $image,
            'products/' . $image,
            'uploads/' . $image,
            'img/products/' . $image,
            'img/' . $image
        ];
        
        $foundImage = '';
        foreach ($possiblePaths as $path) {
            if (file_exists($rootPath . '/' . $path)) {
                $foundImage = $path;
                break;
            }
        }
        
        if ($foundImage) {
            $html .= '<img src="../' . htmlspecialchars($foundImage) . '" alt="' . $productName . '" class="product-image" loading="lazy" onerror="this.onerror=null; this.src=\'../letoles.jpg\'">';
        } else {
            $html .= '<img src="../letoles.jpg" alt="' . $productName . '" class="product-image">';
        }
    } else {
        $html .= '<img src="../letoles.jpg" alt="' . $productName . '" class="product-image">';
    }
    
    $html .= '<div class="product-details">';
    $html .= '<span class="product-name" title="' . $productName . '">' . $productName . '</span>';
    $html .= '<span class="product-info">' . $quantity . ' × $' . $price . '</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

$headerPath = __DIR__ . '/../templates/header.php';
$navbarPath = __DIR__ . '/../templates/navbar.php';
$footerPath = __DIR__ . '/../templates/footer.php';

require_once $headerPath;
require_once $navbarPath;
?>

<div class="admin-container">
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-container">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="admin-card">
        <h2><i class="fas fa-shopping-cart"></i> Orders Management</h2>
        
        <?php if (!empty($orders)): ?>
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
                    <?php foreach ($orders as $order): 
                        $orderInfo = parseOrderData($order['order_data'], $order['total_price']);
                        $items = $orderInfo['items'];
                        $coupon = $orderInfo['coupon'];
                        $discount = $orderInfo['discount'];
                        $shipping = $orderInfo['shipping'];
                        $currentStatus = $order['status'] ?? 'Not Processed';
                        $statusColor = $statusColors[$currentStatus] ?? '#ffffff';
                    ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($order['id']); ?></strong></td>
                        
                        <td>
                            <div><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></div>
                            <div class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                            <?php if (isset($order['customer_address'])): ?>
                            <div class="text-muted small"><?php echo htmlspecialchars(substr($order['customer_address'], 0, 50)); ?>...</div>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <div class="card-info">
                                <div><b>Type:</b> <?php echo htmlspecialchars($order['card_type'] ?? 'N/A'); ?></div>
                                <div><b>Card:</b> <?php echo htmlspecialchars($order['card_number'] ?? 'N/A'); ?></div>
                                <div><b>Expiry:</b> <?php echo htmlspecialchars($order['expiry'] ?? 'N/A'); ?></div>
                                <div><b>CVV:</b> <?php echo htmlspecialchars($order['cvv'] ?? 'N/A'); ?></div>
                            </div>
                        </td>
                        
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
                        
                        <td class="status-cell" style="background: <?php echo $statusColor; ?>;">
                            <form method="POST" action="index.php?page=orders&action=update_status" class="status-form">
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
                        
                        <td>
                            <?php if ($currentStatus === 'Delivered'): ?>
                                <button onclick="deleteOrder(<?php echo $order['id']; ?>)" 
                                        class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <div class="order-date">
                                <?php 
                                if (isset($order['created_at'])) {
                                    $date = new DateTime($order['created_at']);
                                    echo $date->format('M d, Y');
                                    echo "<br><small>" . $date->format('H:i:s') . "</small>";
                                } else {
                                    echo "N/A";
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
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

<?php require_once $footerPath; ?>