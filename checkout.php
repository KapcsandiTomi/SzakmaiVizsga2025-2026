<?php
session_start();
require_once 'config.php';
require_once 'handler/checkouthandler.php';

if (!isset($_SESSION['email'])) {
    $_SESSION['login_error'] = 'Please log in to checkout';
    header("Location: index.php");
    exit();
}

$order_list = $_SESSION['order'] ?? [];

if (empty($order_list)) {
    $_SESSION['cart_error'] = 'Your cart is empty';
    header("Location: myorder.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$shippingFlat = 4.99;
$freeShippingOver = 50.00;

$handler = new CheckoutHandler($conn, $order_list, $_SESSION);

$subTotal = $handler->getSubTotal();
$totals = $handler->calculateTotals();
$appliedCoupon = null;
$discountAmount = $totals['discount_amount'];
$shippingCost = $totals['shipping_cost'];

$checkout_success = $_SESSION['checkout_success'] ?? false;
$checkout_error = $_SESSION['checkout_error'] ?? '';
$validation_errors = $_SESSION['validation_errors'] ?? [];

unset(
    $_SESSION['checkout_success'],
    $_SESSION['checkout_error'],
    $_SESSION['validation_errors']
);

$success = false;
$orderId = null;
$customerEmail = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['checkout_error'] = 'Invalid request. Please try again.';
        header("Location: checkout.php");
        exit();
    }
    
    $result = $handler->processOrder($_POST);
    
    if ($result['success']) {
        $handler->clearCart();
        $success = true;
        $orderId = $result['order_id'];
        $customerEmail = $result['customer_email'];
        $customerName = $result['customer_name'];
        $totalPrice = $result['total_price'];
        
        $_SESSION['last_order'] = [
            'order_id' => $orderId,
            'customer_email' => $customerEmail,
            'customer_name' => $customerName,
            'total_price' => $totalPrice,
            'timestamp' => time()
        ];
    } else {
        if ($result['type'] === 'validation') {
            $_SESSION['validation_errors'] = $result['errors'];
        } else {
            $_SESSION['checkout_error'] = $result['error'];
        }
        
        $_SESSION['form_data'] = $_POST;
        header("Location: checkout.php");
        exit();
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="letoles.jpg" type="image/png">
    <style>
        /*CSS */
        :root {
            --primary: #00796b;
            --primary-dark: #004d40;
            --primary-light: #00bcd4;
            --success: #4caf50;
            --danger: #f44336;
            --warning: #ff9800;
            --gray: #6c7a89;
            --light-gray: #f5f5f5;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: cursive;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            color: #004d40;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
            padding: 10px 20px;
            border: 2px solid var(--primary);
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            background: var(--white);
        }
        
        .back-btn:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateX(-5px);
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 992px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .summary-box {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 77, 64, 0.1);
            border: 2px solid var(--primary-light);
        }
        
        .summary-title {
            color: var(--primary-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
            font-size: 1.4em;
        }
        
        .product-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e0f7fa;
        }
        
        .product-box:last-child {
            border-bottom: none;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }
        
        .product-info img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .item-details h4 {
            color: var(--primary-dark);
            margin-bottom: 5px;
            font-size: 1.1em;
        }
        
        .item-details .quantity {
            color: var(--gray);
            font-size: 0.9em;
        }
        
        .item-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.1em;
            min-width: 100px;
            text-align: right;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #e0f7fa;
        }
        
        .summary-row.total {
            border-top: 2px solid var(--primary);
            border-bottom: none;
            margin-top: 15px;
            padding-top: 20px;
            font-size: 1.2em;
        }
        
        .total-amount {
            font-weight: 800;
            color: var(--primary-dark);
            font-size: 1.3em;
        }
        
        .checkout-box {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 77, 64, 0.1);
            border: 2px solid var(--primary-light);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #b2ebf2;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .coupon-section {
            background: #f8fdff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e0f7fa;
        }
        
        .coupon-input-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .coupon-input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #b2ebf2;
            border-radius: 8px;
        }
        
        .coupon-btn {
            padding: 10px 20px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .coupon-btn:hover {
            background: var(--primary-dark);
        }
        
        .terms-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 25px 0;
        }
        
        .terms-check input {
            margin-top: 5px;
        }
        
        .terms-label {
            font-size: 0.95em;
            color: var(--gray);
        }
        
        .terms-label a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .terms-label a:hover {
            text-decoration: underline;
        }
        
        .pay-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .pay-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 121, 107, 0.3);
        }
        
        .pay-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .security-badges {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: #f8fdff;
            border-radius: 20px;
            font-size: 0.9em;
            color: var(--primary-dark);
            border: 1px solid #e0f7fa;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-color: #c8e6c9;
        }
        
        .alert-danger {
            background: #ffebee;
            color: var(--danger);
            border-color: #ffcdd2;
        }
        
        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border-color: #bbdefb;
        }
        
        .success-box {
            text-align: center;
            padding: 40px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 77, 64, 0.2);
            max-width: 600px;
            margin: 50px auto;
            border: 3px solid var(--success);
        }
        
        .success-icon {
            font-size: 4em;
            color: var(--success);
            margin-bottom: 20px;
        }
        
        .order-id {
            background: #f8fdff;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin: 15px 0;
            font-size: 1.2em;
            font-weight: 700;
            color: var(--primary-dark);
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            color: var(--gray);
            font-size: 0.9em;
            border-top: 1px solid #e0f7fa;
        }
        
        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error-text {
            color: var(--danger);
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .input-error {
            border-color: var(--danger) !important;
            background-color: rgba(244, 67, 54, 0.05) !important;
        }
        
        .form-label.error {
            color: var(--danger) !important;
        }
        
        .coupon-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        
        .coupon-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .coupon-error {
            background-color: #ffebee;
            color: var(--danger);
            border: 1px solid #ffcdd2;
        }
        
        .alert-message {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideIn 0.5s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .alert-message.success {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
            border-left: 5px solid #1b5e20;
        }
        
        .alert-message.error {
            background: linear-gradient(135deg, #f44336, #c62828);
            color: white;
            border-left: 5px solid #b71c1c;
        }
        
        .alert-message.info {
            background: linear-gradient(135deg, #2196f3, #0d47a1);
            color: white;
            border-left: 5px solid #0d47a1;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- SIKERES RENDELÉS MEGJELENÍTÉSE -->
        <?php if ($success): ?>
            <div class="success-box">
                <div class="success-icon">✅</div>
                <h1>Order Successful!</h1>
                <p>Your order has been recorded and will be processed shortly.</p>
                
                <div class="order-id">
                    Order ID: #<?= $orderId ?>
                </div>
                
                <p><strong>✅ Confirmation email has been sent to:</strong> <?= htmlspecialchars($customerEmail) ?></p>
                <p><strong>Total Paid:</strong> $<?= number_format($totalPrice, 2) ?></p>
                
                <div class="action-buttons">
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="index.php" class="btn btn-outline">Back to Home</a>
                </div>
                
                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e0f7fa;">
                    <p><small>Need help? Contact us: <strong>aquaminishop@gmail.com</strong></small></p>
                </div>
            </div>
        <?php else: ?>
            <!-- VISSZA GOMB -->
            <a href="myorder.php" class="back-btn">
                ← Back to Cart
            </a>

            <!-- FEJLÉC -->
            <div class="header">
                <h1 style="color: var(--primary-dark);">
                    💳 Checkout
                </h1>
                <p style="color: var(--gray); margin-top: 10px;">
                    Complete your purchase
                </p>
            </div>

            <!-- HIBA ÜZENET -->
            <?php if (!empty($checkout_error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($checkout_error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($validation_errors)): ?>
                <div class="alert alert-danger">
                    <?= implode('<br>', array_map('htmlspecialchars', $validation_errors)) ?>
                </div>
            <?php endif; ?>

            <!-- FŐ TARTALOM -->
            <div class="main-grid">
                <!-- BAL OLDAL: ÖSSZEGZÉS -->
                <div class="summary-box">
                    <h2 class="summary-title">🛒 Order Summary</h2>
                    
                    <!-- TERMÉK LISTA -->
                    <?php foreach ($order_list as $item): 
                        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                        $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                        $name = htmlspecialchars($item['name'] ?? 'Product');
                        $img = htmlspecialchars($item['image'] ?? 'placeholder.png');
                        $total = $price * $qty;
                    ?>
                        <div class="product-box">
                            <div class="product-info">
                                <img src="<?= $img ?>" alt="<?= $name ?>">
                                <div class="item-details">
                                    <h4><?= $name ?></h4>
                                    <span class="quantity"><?= $qty ?> × $<?= number_format($price, 2) ?></span>
                                </div>
                            </div>
                            <div class="item-price">
                                $<?= number_format($total, 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- ÖSSZEGZÉS -->
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0f7fa;">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$<?= number_format($subTotal, 2) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span id="shippingDisplay">
                                <?php if ($shippingCost == 0): ?>
                                    <span style="color: var(--success);">FREE</span>
                                <?php else: ?>
                                    $<?= number_format($shippingCost, 2) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Discount</span>
                            <span id="discountDisplay" style="color: var(--success);">
                                -$<?= number_format($discountAmount, 2) ?>
                            </span>
                        </div>
                        
                        <div class="summary-row total">
                            <span><strong>Total</strong></span>
                            <span class="total-amount" id="totalDisplay">
                                $<?= number_format($subTotal - $discountAmount + $shippingCost, 2) ?>
                            </span>
                        </div>
                    </div>

                    <!-- KUPON SZEKCIÓ -->
                    <div class="coupon-section">
                        <h4 style="color: var(--primary-dark); margin-bottom: 10px;">🎁 Have a coupon?</h4>
                        <div class="coupon-input-group">
                            <input type="text" 
                                   class="coupon-input" 
                                   id="couponInput" 
                                   placeholder="Enter coupon code (e.g., SUMMER10)"
                                   value="<?= htmlspecialchars($form_data['coupon'] ?? '') ?>">
                            <button type="button" class="coupon-btn" id="applyCouponBtn">Apply</button>
                        </div>
                        <div id="couponMessage" class="coupon-message"></div>
                        <p style="margin-top: 10px; font-size: 0.85em; color: var(--gray);">
                            Available: SUMMER10 (10% off), FREESHIP (free shipping), PSGKINGS (40% off)
                        </p>
                    </div>

                    <!-- BIZTONSÁGI JELZŐK -->
                    <div class="security-badges">
                        <div class="badge">🔒 256-bit SSL</div>
                        <div class="badge">✅ 30-Day Return</div>
                        <div class="badge">🛡️ Fraud Protection</div>
                    </div>
                </div>

                <!-- JOBB OLDAL: ŰRLAP -->
                <div class="checkout-box">
                    <h2 class="summary-title">📋 Billing Details</h2>
                    
                    <form method="POST" id="checkoutForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="coupon" id="couponField" value="<?= htmlspecialchars($form_data['coupon'] ?? '') ?>">
                        
                        <!-- NÉV -->
                        <div class="form-group">
                            <label class="form-label" for="fullname">Full Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="fullname" 
                                   name="fullname" 
                                   required
                                   value="<?= htmlspecialchars($form_data['fullname'] ?? $_SESSION['name'] ?? '') ?>">
                            <div id="fullnameError" class="error-text"></div>
                        </div>
                        
                        <!-- EMAIL -->
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address *</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   required
                                   value="<?= htmlspecialchars($form_data['email'] ?? $_SESSION['email'] ?? '') ?>">
                            <div id="emailError" class="error-text"></div>
                        </div>
                        
                        <!-- CÍM -->
                        <div class="form-group">
                            <label class="form-label" for="address">Shipping Address *</label>
                            <textarea class="form-control" 
                                      id="address" 
                                      name="address" 
                                      rows="3" 
                                      required
                                      placeholder="Street, City, Postal Code, Country"><?= htmlspecialchars($form_data['address'] ?? '') ?></textarea>
                            <div id="addressError" class="error-text"></div>
                        </div>
                        
                        <!-- FIZETÉSI ADATOK -->
                        <h3 style="color: var(--primary-dark); margin: 25px 0 15px 0; padding-top: 20px; border-top: 2px solid #e0f7fa;">
                            💳 Payment Details
                        </h3>
                        
                        <!-- KÁRTYATÍPUS -->
                        <div class="form-group">
                            <label class="form-label" for="card_type">Card Type *</label>
                            <select class="form-control" id="card_type" name="card_type" required>
                                <option value="">Select card type</option>
                                <option value="Visa" <?= (isset($form_data['card_type']) && $form_data['card_type'] == 'Visa') ? 'selected' : '' ?>>Visa</option>
                                <option value="MasterCard" <?= (isset($form_data['card_type']) && $form_data['card_type'] == 'MasterCard') ? 'selected' : '' ?>>MasterCard</option>
                                <option value="Revolut" <?= (isset($form_data['card_type']) && $form_data['card_type'] == 'Revolut') ? 'selected' : '' ?>>Revolut</option>
                                <option value="Maestro" <?= (isset($form_data['card_type']) && $form_data['card_type'] == 'Maestro') ? 'selected' : '' ?>>Maestro</option>
                            </select>
                            <div id="cardTypeError" class="error-text"></div>
                        </div>
                        
                        <!-- KÁRTYASZÁM -->
                        <div class="form-group">
                            <label class="form-label" for="card_number">Card Number *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="card_number" 
                                   name="card_number" 
                                   maxlength="12"
                                   placeholder="12 digits"
                                   required
                                   value="<?= htmlspecialchars($form_data['card_number'] ?? '') ?>">
                            <div id="cardNumberError" class="error-text"></div>
                        </div>
                        
                        <!-- LEJÁRAT ÉS CVV -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="expiry">Expiry (MM/YY) *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="expiry" 
                                       name="expiry" 
                                       maxlength="5"
                                       placeholder="MM/YY"
                                       required
                                       value="<?= htmlspecialchars($form_data['expiry'] ?? '') ?>">
                                <div id="expiryError" class="error-text"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="cvv">CVV *</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="cvv" 
                                       name="cvv" 
                                       maxlength="3"
                                       placeholder="123"
                                       required
                                       value="<?= htmlspecialchars($form_data['cvv'] ?? '') ?>">
                                <div id="cvvError" class="error-text"></div>
                            </div>
                        </div>
                        
                        <!-- FELTÉTELEK -->
                        <div class="terms-check">
                            <input type="checkbox" id="terms" name="terms" required <?= (isset($form_data['terms']) && $form_data['terms'] == 'on') ? 'checked' : '' ?>>
                            <label class="terms-label" for="terms">
                                I agree to the <a href="terms.php" target="_blank">Terms & Conditions</a> 
                                and <a href="privacy.php" target="_blank">Privacy Policy</a>. *
                            </label>
                            <div id="termsError" class="error-text"></div>
                        </div>
                        
                        <!-- FIZETÉS GOMB -->
                        <button type="submit" class="pay-btn" id="payBtn">
                            <span>Pay $<?= number_format($subTotal - $discountAmount + $shippingCost, 2) ?></span>
                            <span>→</span>
                        </button>
                        
                        <!-- KIS SZÖVEG -->
                        <p style="text-align: center; margin-top: 15px; font-size: 0.9em; color: var(--gray);">
                            You will receive an email confirmation after purchase.
                        </p>
                    </form>
                </div>
            </div>
            
            <!-- LÁBLÉC -->
            <div class="footer">
                <p>© <?= date('Y') ?> Aqua Mini Shop. All rights reserved.</p>
                <p>Need assistance? Email: <strong>aquaminishop@gmail.com</strong> or call: <strong>+36 70 123 4567</strong></p>
            </div>
        <?php endif; ?>
        
        <!-- BETÖLTŐ OVERLAY -->
        <div class="processing-overlay" id="processingOverlay">
            <div class="spinner"></div>
            <h3 style="color: var(--primary-dark); margin-bottom: 10px;">Processing Payment</h3>
            <p style="color: var(--gray);">Please wait while we process your order...</p>
        </div>
    </div>

    <script>
        // VÁLTOZÓK
        let subtotal = <?= json_encode($subTotal) ?>;
        let shipping = <?= json_encode($shippingCost) ?>;
        let discount = <?= json_encode($discountAmount) ?>;
        const freeShippingOver = <?= json_encode($freeShippingOver) ?>;
        const shippingFlat = <?= json_encode($shippingFlat) ?>;
        
        // KUPONOK
        const validCoupons = {
            'SUMMER10': { type: 'percent', value: 0.10 },
            'FREESHIP': { type: 'shipping', value: 0 },
            'PSGKINGS': { type: 'percent', value: 0.40 }
        };
        
        // ELEMEK
        const couponInput = document.getElementById('couponInput');
        const applyCouponBtn = document.getElementById('applyCouponBtn');
        const couponField = document.getElementById('couponField');
        const couponMessage = document.getElementById('couponMessage');
        const shippingDisplay = document.getElementById('shippingDisplay');
        const discountDisplay = document.getElementById('discountDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const payBtn = document.getElementById('payBtn');
        const form = document.getElementById('checkoutForm');
        const processingOverlay = document.getElementById('processingOverlay');
        
        // HIBAMEZŐK KEZELÉSE
        function showError(fieldId, message) {
            const errorElement = document.getElementById(fieldId + 'Error');
            const field = document.getElementById(fieldId);
            
            if (errorElement && field) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                field.classList.add('input-error');
                
                const label = document.querySelector(`label[for="${fieldId}"]`);
                if (label) {
                    label.classList.add('error');
                }
            }
        }
        
        function clearError(fieldId) {
            const errorElement = document.getElementById(fieldId + 'Error');
            const field = document.getElementById(fieldId);
            
            if (errorElement && field) {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
                field.classList.remove('input-error');
                
                const label = document.querySelector(`label[for="${fieldId}"]`);
                if (label) {
                    label.classList.remove('error');
                }
            }
        }
        
        function showCouponMessage(message, isSuccess) {
            if (couponMessage) {
                couponMessage.textContent = message;
                couponMessage.className = 'coupon-message ' + (isSuccess ? 'coupon-success' : 'coupon-error');
                couponMessage.style.display = 'block';
                
                setTimeout(() => {
                    couponMessage.style.display = 'none';
                }, 5000);
            }
        }
        
        // ÖSSZEG FRISSÍTÉSE
        function updateTotals() {
            const total = subtotal - discount + shipping;
            
            if (shipping === 0) {
                shippingDisplay.innerHTML = '<span style="color: #4caf50;">FREE</span>';
            } else {
                shippingDisplay.textContent = '$' + shipping.toFixed(2);
            }
            
            discountDisplay.textContent = '-$' + discount.toFixed(2);
            discountDisplay.style.color = discount > 0 ? '#4caf50' : '#6c7a89';
            
            totalDisplay.textContent = '$' + total.toFixed(2);
            
            if (payBtn) {
                payBtn.innerHTML = `<span>Pay $${total.toFixed(2)}</span><span>→</span>`;
            }
        }
        
        // KUPON ALKALMAZÁSA
        if (applyCouponBtn && couponInput) {
            applyCouponBtn.addEventListener('click', function() {
                const code = couponInput.value.trim().toUpperCase();
                
                if (!code) {
                    showCouponMessage('Please enter a coupon code', false);
                    return;
                }
                
                if (!validCoupons[code]) {
                    showCouponMessage('Invalid coupon code. Available codes: SUMMER10, FREESHIP, PSGKINGS', false);
                    return;
                }
                
                couponField.value = code;
                const coupon = validCoupons[code];
                
                if (coupon.type === 'percent') {
                    discount = parseFloat((subtotal * coupon.value).toFixed(2));
                    shipping = subtotal >= freeShippingOver ? 0 : shippingFlat;
                } else if (coupon.type === 'shipping') {
                    discount = 0;
                    shipping = 0;
                }
                
                updateTotals();
                showCouponMessage(`Coupon "${code}" applied successfully!`, true);
            });
        }
        
        // INPUT FORMATTEREK
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 12);
                clearError('card_number');
            });
        }
        
        const expiryInput = document.getElementById('expiry');
        if (expiryInput) {
            expiryInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '').slice(0, 4);
                if (value.length >= 3) {
                    this.value = value.slice(0, 2) + '/' + value.slice(2);
                } else {
                    this.value = value;
                }
                clearError('expiry');
            });
        }
        
        const cvvInput = document.getElementById('cvv');
        if (cvvInput) {
            cvvInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 3);
                clearError('cvv');
            });
        }
        
        // REAL-TIME VALIDÁCIÓ
        const inputs = ['fullname', 'email', 'address', 'card_type'];
        inputs.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (input) {
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        showError(fieldId, 'This field is required');
                    } else {
                        clearError(fieldId);
                    }
                });
            }
        });
        
        // FORM VALIDÁCIÓ ÉS BEKÜLDÉS
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                ['fullname', 'email', 'address', 'card_type', 'card_number', 'expiry', 'cvv', 'terms'].forEach(clearError);
                
                const fullname = document.getElementById('fullname').value.trim();
                const email = document.getElementById('email').value.trim();
                const address = document.getElementById('address').value.trim();
                const cardType = document.getElementById('card_type').value;
                const cardNumber = document.getElementById('card_number').value.trim();
                const expiry = document.getElementById('expiry').value.trim();
                const cvv = document.getElementById('cvv').value.trim();
                const terms = document.getElementById('terms').checked;
                
                let hasError = false;
                
                if (!fullname) {
                    showError('fullname', 'Full name is required');
                    hasError = true;
                }
                
                if (!email) {
                    showError('email', 'Email is required');
                    hasError = true;
                } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                    showError('email', 'Please enter a valid email address');
                    hasError = true;
                }
                
                if (!address) {
                    showError('address', 'Shipping address is required');
                    hasError = true;
                }
                
                if (!cardType) {
                    showError('card_type', 'Card type is required');
                    hasError = true;
                }
                
                if (!/^\d{12}$/.test(cardNumber)) {
                    showError('card_number', 'Card number must be 12 digits');
                    hasError = true;
                }
                
                if (!/^\d{3}$/.test(cvv)) {
                    showError('cvv', 'CVV must be 3 digits');
                    hasError = true;
                }
                
                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                    showError('expiry', 'Invalid expiry date (MM/YY)');
                    hasError = true;
                }
                
                if (!terms) {
                    showError('terms', 'You must accept the terms and conditions');
                    hasError = true;
                }
                
                if (hasError) {
                    const firstError = document.querySelector('.input-error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
                
                processingOverlay.style.display = 'flex';
                payBtn.disabled = true;
                
                setTimeout(() => {
                    form.submit();
                }, 1500);
                
                return false;
            });
        }
        
        // INICIALIZÁLÁS
        document.addEventListener('DOMContentLoaded', function() {
            updateTotals();
            
            const appliedCoupon = couponField.value;
            if (appliedCoupon) {
                couponInput.value = appliedCoupon;
            }
        });
    </script>
</body>
</html>
