<?php
require_once __DIR__ . '/../handler/trackhandler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Track Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
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
            font-family: cursive; 
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
            font-family: cursive;
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
        
        @media (max-width: 768px) {
            .track-navbar {
                padding: 14px 16px;
            }

            .track-navbar h1,
            .track-title {
                font-size: 20px;
            }

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

            .current-status,
            .track-product-meta {
                flex-direction: column;
                align-items: flex-start;
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
    <?php if (!empty($data['error'])): ?>
        <div class="alert-container">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($data['error']); ?>
                <br>
                <a href="myorder.php" class="btn btn-sm btn-primary mt-3">
                    <i class="fas fa-arrow-left"></i> Back to My Orders
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($data['trackingData'] && empty($data['error'])): 
        $order = $data['trackingData']['order'] ?? [];
        $statusInfo = $data['trackingData']['status_info'];
        $progress = $data['trackingData']['progress'];
        $currentStatus = $order['status'] ?? 'Not Processed';
        $statusColor = $statusInfo['colors'][$currentStatus] ?? '#ffffff';
        $statusSteps = $statusInfo['steps'];
        $currentStepIndex = array_search($currentStatus, $statusSteps);
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
                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
            </div>
            
            <div class="status-steps">
                <?php foreach($statusSteps as $index => $step): 
                    $isActive = ($step === $currentStatus);
                    $isCompleted = ($currentStepIndex !== false && $index <= $currentStepIndex);
                    $stepClass = $isActive ? 'step-active' : ($isCompleted ? 'step-completed' : '');
                ?>
                <div class="status-step <?php echo $stepClass; ?>">
                    <div class="step-icon" style="<?php echo $isCompleted ? 'background:' . $statusInfo['colors'][$step] : ''; ?>">
                        <i class="<?php echo $statusInfo['icons'][$step]; ?>"></i>
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
                        <i class="<?php echo $statusInfo['icons'][$currentStatus]; ?>"></i>
                    </div>
                    <div class="status-text">
                        <div class="status-name"><?php echo $currentStatus; ?></div>
                        <div class="status-description"><?php echo $statusInfo['descriptions'][$currentStatus]; ?></div>
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
                    <span class="card-masked"><?php echo htmlspecialchars($data['maskedCard'] ?? 'N/A'); ?></span><br>
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
            <?php if (is_array($data['orderInfo']['items']) && count($data['orderInfo']['items']) > 0): ?>
                <div class="track-products-list">
                    <?php foreach($data['orderInfo']['items'] as $index => $item): ?>
                        <?php echo $handler->displayProduct($item); ?>
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
            <?php if (is_array($data['orderInfo']['items']) && count($data['orderInfo']['items']) > 0): ?>
                <div class="price-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($data['orderInfo']['subtotal'], 2); ?></span>
                </div>
                <?php if ($data['orderInfo']['discount'] > 0): ?>
                    <div class="price-row">
                        <span class="discount-text">Discount <?php echo $data['orderInfo']['coupon'] ? "(" . htmlspecialchars($data['orderInfo']['coupon']) . ")" : ""; ?>:</span>
                        <span class="discount-text">-$<?php echo number_format($data['orderInfo']['discount'], 2); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($data['orderInfo']['shipping'] > 0): ?>
                    <div class="price-row">
                        <span>Shipping:</span>
                        <span>$<?php echo number_format($data['orderInfo']['shipping'], 2); ?></span>
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

