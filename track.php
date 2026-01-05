<?php
session_start();
require_once 'config.php';

// ====================
// JOGOSULTSÁG ELLENŐRZÉS
// ====================
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ====================
// VÁLTOZÓK
// ====================
$error = '';
$order = null;

// ====================
// RENDELÉS BETÖLTÉSE
// ====================
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = intval($_GET['id']);
    $user_email = $_SESSION['email'];
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND customer_email = ?");
    $stmt->bind_param("is", $order_id, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        $error = "Order not found or you don't have permission to view this order!";
    }
    $stmt->close();
} else {
    $error = "No order ID specified!";
}

// ====================
// ÁLLAPOT SZÍNEK
// ====================
$statusColors = [
    "Not Processed" => "#ff4d4d",
    "Processed" => "#ffa500", 
    "Handed to Courier" => "#1e90ff",
    "On the Way" => "#ffff66",
    "Delivered" => "#32cd32",
    "PC Configuration Ordered" => "#9370db"
];

// ====================
// ÁLLAPOT LEÍRÁSOK
// ====================
$statusDescriptions = [
    "Not Processed" => "Your order has been received and is waiting to be processed.",
    "Processed" => "Your order has been processed and is being prepared for shipment.",
    "Handed to Courier" => "Your order has been handed over to the courier service.",
    "On the Way" => "Your order is on the way to your address.",
    "Delivered" => "Your order has been delivered successfully.",
    "PC Configuration Ordered" => "Your PC configuration has been ordered and is being assembled." 

// ====================
// ÁLLAPOT ICONOK
// ====================
$statusIcons = [
    "Not Processed" => "fas fa-clock",
    "Processed" => "fas fa-cogs",
    "Handed to Courier" => "fas fa-handshake",
    "On the Way" => "fas fa-truck",
    "Delivered" => "fas fa-check-circle",
    "PC Configuration Ordered" => "fas fa-desktop" // Új ikon
];

// ====================
// HELPER FUNKCIÓK
// ====================
function parseOrderData($orderDataJson, $totalPrice) {
    if (empty($orderDataJson) || trim($orderDataJson) === '') {
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
        $cleanJson = trim($orderDataJson);
        $cleanJson = str_replace(["\r", "\n", "\t"], '', $cleanJson);
        $cleanJson = preg_replace('/\s+/', ' ', $cleanJson);
        
        if (strpos($cleanJson, "'") !== false) {
            $cleanJson = str_replace("'", '"', $cleanJson);
        }
        
        if (strpos($cleanJson, '[') === 0 && strpos($cleanJson, ']') !== false) {
            $cleanJson = '{"items": ' . $cleanJson . '}';
        }
        
        $data = json_decode($cleanJson, true);
    }
    
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        error_log("JSON decode error: " . json_last_error_msg() . " - Data: " . $orderDataJson);
        return [
            'items' => [],
            'coupon' => null,
            'discount' => 0,
            'shipping' => 0,
            'subtotal' => $totalPrice
        ];
    }
    
    if (isset($data['items']) && is_array($data['items'])) {
        return [
            'items' => $data['items'],
            'coupon' => $data['coupon'] ?? null,
            'discount' => $data['discount'] ?? 0,
            'shipping' => $data['shipping_cost'] ?? $data['shipping'] ?? 0,
            'subtotal' => $data['subtotal'] ?? $totalPrice
        ];
    } 
    elseif (is_array($data) && !empty($data)) {
        $firstItem = reset($data);
        if (isset($firstItem['name']) || isset($firstItem['product_name']) || isset($firstItem['title'])) {
            return [
                'items' => $data,
                'coupon' => null,
                'discount' => 0,
                'shipping' => 0,
                'subtotal' => $totalPrice
            ];
        }
    }
    
    return [
        'items' => [],
        'coupon' => null,
        'discount' => 0,
        'shipping' => 0,
        'subtotal' => $totalPrice
    ];
}

function displayProduct($item) {
    $html = '<div class="track-product-item">';
    
    $image = '';
    
    if (isset($item['image']) && !empty($item['image'])) {
        $image = $item['image'];
    } elseif (isset($item['product_image']) && !empty($item['product_image'])) {
        $image = $item['product_image'];
    } elseif (isset($item['img']) && !empty($item['img'])) {
        $image = $item['img'];
    }
    
    if (!empty($image)) {
        if (strpos($image, '../') !== 0 && strpos($image, 'http') !== 0 && strpos($image, 'https') !== 0) {
            $image = '../' . $image;
        }
        
        $html .= '<img src="' . htmlspecialchars($image) . '" alt="Product" class="track-product-image" onerror="this.onerror=null; this.src=\'../letoles.jpg\';">';
    } else {
        $defaultImage = '../letoles.jpg';
        if (file_exists($defaultImage)) {
            $html .= '<img src="' . $defaultImage . '" alt="Default Product" class="track-product-image">';
        } else {
            $html .= '<div class="track-product-image-placeholder">
                        <i class="fas fa-image"></i>
                      </div>';
        }
    }
    
    $productName = '';
    if (isset($item['name']) && !empty($item['name'])) {
        $productName = $item['name'];
    } elseif (isset($item['product_name']) && !empty($item['product_name'])) {
        $productName = $item['product_name'];
    } elseif (isset($item['title']) && !empty($item['title'])) {
        $productName = $item['title'];
    } else {
        $productName = 'Product';
    }
    
    $quantity = isset($item['quantity']) ? intval($item['quantity']) : (isset($item['qty']) ? intval($item['qty']) : 1);
    
    $price = 0;
    if (isset($item['price']) && is_numeric($item['price'])) {
        $price = floatval($item['price']);
    } elseif (isset($item['product_price']) && is_numeric($item['product_price'])) {
        $price = floatval($item['product_price']);
    } elseif (isset($item['unit_price']) && is_numeric($item['unit_price'])) {
        $price = floatval($item['unit_price']);
    }
    
    $total = $price * $quantity;
    
    $html .= '<div class="track-product-details">';
    $html .= '<span class="track-product-name">' . htmlspecialchars($productName) . '</span>';
    
    if (isset($item['size']) || isset($item['color']) || isset($item['variant'])) {
        $html .= '<div class="track-product-info">';
        if (isset($item['size'])) {
            $html .= '<span class="me-2">Size: ' . htmlspecialchars($item['size']) . '</span>';
        }
        if (isset($item['color'])) {
            $html .= '<span class="me-2">Color: ' . htmlspecialchars($item['color']) . '</span>';
        }
        if (isset($item['variant'])) {
            $html .= '<span>Variant: ' . htmlspecialchars($item['variant']) . '</span>';
        }
        $html .= '</div>';
    }
    
    $html .= '<div class="track-product-meta">';
    $html .= '<span class="track-product-price">$' . number_format($price, 2) . ' each</span>';
    $html .= '<span class="track-product-quantity">× ' . $quantity . '</span>';
    $html .= '</div>';
    $html .= '<span class="track-product-total">$' . number_format($total, 2) . ' total</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Track Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="letoles.jpg" type="image/png">
    <style>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            color: #333;
            padding-bottom: 50px;
        }
        
        .track-navbar { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff; 
            padding: 15px 30px; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .track-navbar h1 { 
            margin: 0; 
            font-size: 24px; 
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .track-container { 
            max-width: 1200px; 
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .track-card { 
            border-radius: 16px; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); 
            background: #fff; 
            margin-bottom: 30px; 
            padding: 30px;
            border: none;
            overflow: hidden;
        }
        
        .track-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #f0f0f0;
        }
        
        .track-title {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .order-number {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }
        
        .tracking-progress {
            padding: 30px 0;
            position: relative;
        }
        
        .progress-bar {
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            margin: 20px 0 40px;
            position: relative;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 4px;
            transition: width 0.8s ease;
        }
        
        .status-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }
        
        .status-step {
            text-align: center;
            position: relative;
            flex: 1;
            padding: 0 10px;
        }
        
        .step-icon {
            width: 50px;
            height: 50px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 3px solid #e0e0e0;
            color: #e0e0e0;
            font-size: 20px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        
        .step-active .step-icon {
            border-color: var(--primary);
            color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 8px rgba(79, 172, 254, 0.2);
        }
        
        .step-completed .step-icon {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }
        
        .step-label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #666;
        }
        
        .step-active .step-label {
            color: var(--primary-dark);
        }
        
        .step-date {
            font-size: 0.85em;
            color: #999;
        }
        
        .current-status-card {
            background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border-left: 5px solid var(--primary);
        }
        
        .current-status {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .status-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .status-text {
            flex: 1;
        }
        
        .status-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .status-description {
            color: #666;
            font-size: 0.95em;
        }
        
        .products-section {
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 22px;
            color: var(--primary-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .track-products-list {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .track-product-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-radius: 10px;
            background: #f9f9f9;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .track-product-item:hover {
            background: #f0f7ff;
            transform: translateX(5px);
        }
        
        .track-product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid #e0f7fa;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }
        
        .track-product-details {
            flex: 1;
        }
        
        .track-product-name {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
            font-size: 1em;
        }
        
        .track-product-info {
            font-size: 0.9em;
            color: var(--secondary);
            display: block;
            margin-bottom: 5px;
        }
        
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .info-card {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid var(--primary);
        }
        
        .info-title {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-content {
            color: #555;
            line-height: 1.6;
        }
        
        .price-summary {
            background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
            border-radius: 12px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .price-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2em;
            color: var(--primary-dark);
            padding-top: 15px;
            margin-top: 10px;
            border-top: 2px solid #ddd;
        }
        
        .discount-text {
            color: var(--success);
        }
        
        .card-masked {
            font-family: monospace;
            letter-spacing: 2px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .btn-primary-lg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-primary-lg:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(79, 172, 254, 0.3);
            color: white;
        }
        
        .btn-secondary-lg {
            background: #fff;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-secondary-lg:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(79, 172, 254, 0.2);
        }
        
        .alert-container {
            margin: 20px 0;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 20px 25px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid var(--danger);
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 5px solid var(--info);
        }
        
        .track-footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
            border-top: 1px solid #eee;
        }
        
        .support-info {
            margin-top: 10px;
            font-size: 0.85em;
            color: #888;
        }
        
        .track-product-image-placeholder {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 24px;
            border: 2px solid #e0f7fa;
            flex-shrink: 0;
        }

        .track-product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 5px 0;
        }

        .track-product-price {
            font-size: 0.85em;
            color: #666;
        }

        .track-product-quantity {
            font-size: 0.85em;
            color: #667eea;
            font-weight: 600;
            background: rgba(102, 126, 234, 0.1);
            padding: 2px 8px;
            border-radius: 10px;
        }

        .track-product-total {
            font-size: 1em;
            color: #333;
            font-weight: 600;
            display: block;
            margin-top: 5px;
        }
        
        .dynamic-step {
            min-width: 80px;
        }
        
        @media (max-width: 768px) {
            .track-container {
                padding: 0 15px;
            }
            
            .track-card {
                padding: 20px;
            }
            
            .track-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .status-steps {
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .status-step {
                flex: 0 0 calc(33.333% - 20px);
            }
            
            .step-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn-primary-lg, .btn-secondary-lg {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .status-step {
                flex: 0 0 calc(50% - 20px);
            }
            
            .order-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="track-navbar">
    <div class="track-container">
        <h1><i class="fas fa-map-marker-alt"></i> Track Your Order</h1>
    </div>
</div>

<div class="track-container">
    <?php if (!empty($error)): ?>
        <div class="alert-container">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                <br>
                <a href="myorder.php" class="btn btn-sm btn-primary mt-3">
                    <i class="fas fa-arrow-left"></i> Back to My Orders
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($order && empty($error)): 
        $orderInfo = parseOrderData($order['order_data'], $order['total_price']);
        $items = $orderInfo['items'];
        $coupon = $orderInfo['coupon'];
        $discount = $orderInfo['discount'];
        $shipping = $orderInfo['shipping'];
        $currentStatus = $order['status'] ?? 'Not Processed';
        $statusColor = $statusColors[$currentStatus] ?? $statusColors['Not Processed'];
        $statusIcon = $statusIcons[$currentStatus] ?? $statusIcons['Not Processed'];
        $statusDescription = $statusDescriptions[$currentStatus] ?? 'Your order status is being updated.';
        
        $statusSteps = ["Not Processed", "Processed", "Handed to Courier", "On the Way", "Delivered"];
        
        if (!in_array($currentStatus, $statusSteps)) {
            $statusSteps[] = $currentStatus;
        }
        
        $currentStepIndex = array_search($currentStatus, $statusSteps);
        $progressPercentage = $currentStepIndex !== false ? ($currentStepIndex + 1) / count($statusSteps) * 100 : 20;
        
        $card_number = $order['card_number'] ?? '';
        $masked_card = !empty($card_number) ? 'XXXX-XXXX-XXXX-' . substr($card_number, -4) : 'N/A';
    ?>
    
    <div class="track-card">
        <div class="track-header">
            <h2 class="track-title">
                <i class="fas fa-box"></i> Order Tracking
            </h2>
            <div class="order-number">
                Order #<?php echo htmlspecialchars($order['id']); ?>
            </div>
        </div>
        
        <div class="tracking-progress">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progressPercentage; ?>%"></div>
            </div>
            
            <div class="status-steps">
                <?php foreach($statusSteps as $index => $step): 
                    $isActive = ($step === $currentStatus);
                    $isCompleted = ($currentStepIndex !== false && $index <= $currentStepIndex);
                    $stepClass = $isActive ? 'step-active' : ($isCompleted ? 'step-completed' : '');
                    
                    $stepIcon = $statusIcons[$step] ?? $statusIcons['Not Processed'];
                    $stepColor = $statusColors[$step] ?? $statusColors['Not Processed'];
                ?>
                <div class="status-step <?php echo $stepClass; ?> dynamic-step">
                    <div class="step-icon" style="<?php echo $isCompleted ? 'background:' . $stepColor : ''; ?>">
                        <i class="<?php echo $stepIcon; ?>"></i>
                    </div>
                    <div class="step-label"><?php echo $step; ?></div>
                    <?php if ($isActive || $isCompleted): ?>
                        <div class="step-date"><?php 
                            if ($isActive) {
                                echo 'Current';
                            } elseif ($isCompleted) {
                                echo 'Completed';
                            }
                        ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="current-status-card">
                <div class="current-status">
                    <div class="status-icon" style="background: <?php echo $statusColor; ?>;">
                        <i class="<?php echo $statusIcon; ?>"></i>
                    </div>
                    <div class="status-text">
                        <div class="status-name"><?php echo $currentStatus; ?></div>
                        <div class="status-description"><?php echo $statusDescription; ?></div>
                    </div>
                </div>
                <div class="order-date mt-3">
                    <i class="fas fa-calendar-alt"></i> 
                    Order Date: <?php echo date('F d, Y H:i:s', strtotime($order['created_at'])); ?>
                </div>
            </div>
        </div>
        
        <div class="order-info-grid">
            <div class="info-card">
                <h4 class="info-title"><i class="fas fa-user"></i> Customer Info</h4>
                <div class="info-content">
                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['customer_email']); ?><br>
                    <hr class="my-2">
                    <strong><i class="fas fa-map-marker-alt"></i> Shipping Address:</strong><br>
                    <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?>
                </div>
            </div>
            
            <div class="info-card">
                <h4 class="info-title"><i class="fas fa-credit-card"></i> Payment Info</h4>
                <div class="info-content">
                    <strong>Payment Method:</strong><br>
                    <?php echo htmlspecialchars($order['card_type'] ?? 'Credit Card'); ?><br>
                    <strong>Card Number:</strong> 
                    <span class="card-masked"><?php echo $masked_card; ?></span><br>
                    <strong>Expiry:</strong> <?php echo htmlspecialchars($order['expiry'] ?? 'N/A'); ?><br>
                    <strong>Status:</strong> 
                    <span style="color: var(--success); font-weight: 600;">Paid</span><br>
                    <strong>Payment Date:</strong><br>
                    <?php echo date('F d, Y', strtotime($order['created_at'])); ?>
                </div>
            </div>
        </div>
        
        <div class="products-section">
            <h3 class="section-title"><i class="fas fa-shopping-bag"></i> Order Items</h3>
            <?php if (is_array($items) && count($items) > 0): ?>
                <div class="track-products-list">
                    <?php foreach($items as $index => $item): ?>
                        <?php echo displayProduct($item); ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Order Total: $<?php echo number_format($order['total_price'], 2); ?></strong>
                    <br>
                    <small>Detailed product information is not available.</small>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="price-summary">
            <h3 class="section-title"><i class="fas fa-receipt"></i> Price Summary</h3>
            <?php if (is_array($items) && count($items) > 0): ?>
                <div class="price-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($orderInfo['subtotal'], 2); ?></span>
                </div>
                <?php if ($discount > 0): ?>
                    <div class="price-row">
                        <span class="discount-text">Discount <?php echo $coupon ? "($coupon)" : ""; ?>:</span>
                        <span class="discount-text">-$<?php echo number_format($discount, 2); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($shipping > 0): ?>
                    <div class="price-row">
                        <span>Shipping:</span>
                        <span>$<?php echo number_format($shipping, 2); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="price-row">
                <span><strong>Total Amount:</strong></span>
                <span><strong>$<?php echo number_format($order['total_price'], 2); ?></strong></span>
            </div>
        </div>
        
        <div class="button-group">
            <a href="myorder.php" class="btn-secondary-lg">
                <i class="fas fa-arrow-left"></i> Back to My Orders
            </a>
            <a href="products.php" class="btn-primary-lg">
                <i class="fas fa-shopping-cart"></i> Continue Shopping
            </a>
        </div>
    </div>
    
    <div class="track-footer">
        <p><i class="fas fa-headset"></i> Need help with your order?</p>
        <p class="support-info">
            Contact our customer support at aquaminishop@gmail.com<br>
            or call us at +36 70 123 4567
        </p>
    </div>
    
    <?php endif; ?>
</div>

<script>
setTimeout(() => {
    window.location.reload();
}, 30000);

document.querySelectorAll('.track-product-image').forEach(img => {
    img.addEventListener('error', function() {
        this.src = '../letoles.jpg';
        this.alt = 'Default Product Image';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const progressFill = document.querySelector('.progress-fill');
    if (progressFill) {
        const width = progressFill.style.width;
        progressFill.style.width = '0';
        
        setTimeout(() => {
            progressFill.style.transition = 'width 1.5s ease-in-out';
            progressFill.style.width = width;
        }, 500);
    }
    
    const productList = document.querySelector('.track-products-list');
    if (productList && productList.scrollHeight > 400) {
        productList.style.overflowY = 'scroll';
    }
});
</script>

</body>
</html>
