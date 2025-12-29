<?php
// ====================
// MUNKAMENET KEZDÉS
// ====================
session_start();
require_once 'config.php';

// ====================
// PHPMailer BETÖLTÉSE
// ====================
require_once 'src/PHPMailer.php';
require_once 'src/Exception.php';
require_once 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ====================
// JOGOSULTSÁG ELLENŐRZÉS
// ====================
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// ====================
// RENDELÉSI ADATOK
// ====================
$order_list = $_SESSION['order'] ?? [];

if (empty($order_list)) {
    header("Location: myorder.php");
    exit();
}

// ====================
// KUPON BEÁLLÍTÁSOK
// ====================
$validCoupons = [
    'SUMMER10' => 0.10,
    'FREESHIP' => 'FREESHIP',
    'PSGKINGS' => 0.40
];

$shippingFlat = 4.99;
$freeShippingOver = 50.00;

// ====================
// RENDELÉS ÖSSZEGZÉS
// ====================
$subTotal = 0.0;
foreach ($order_list as $item) {
    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
    $price = isset($item['price']) ? (float)$item['price'] : 0.0;
    $subTotal += $price * $qty;
}

// ====================
// CSRF TOKEN
// ====================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// ====================
// VÁLTOZÓK INICIALIZÁLÁS
// ====================
$success = false;
$error = "";
$appliedCoupon = null;
$discountAmount = 0.0;
$shippingCost = ($subTotal >= $freeShippingOver) ? 0.0 : $shippingFlat;

// ====================
// FORM FELDOLGOZÁS
// ====================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF ellenőrzés
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        //ADATOK a DATABSEBOL
        $name = trim($_POST["fullname"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $address = trim($_POST["address"] ?? "");
        $cardType = trim($_POST["card_type"] ?? "");
        $cardNumber = preg_replace('/\D/', '', $_POST["card_number"] ?? "");
        $expiry = trim($_POST["expiry"] ?? "");
        $cvv = preg_replace('/\D/', '', $_POST["cvv"] ?? "");
        $terms = isset($_POST['terms']) ? true : false;
        $coupon = trim($_POST['coupon'] ?? "");

        // ====================
        // VALIDÁCIÓ
        // ====================
        $validationErrors = [];
        
        if (empty($name)) $validationErrors[] = "Full name is required";
        if (empty($email)) $validationErrors[] = "Email address is required";
        if (empty($address)) $validationErrors[] = "Shipping address is required";
        if (empty($cardType)) $validationErrors[] = "Card type is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $validationErrors[] = "Invalid email address format";
        if (!preg_match('/^\d{12}$/', $cardNumber)) $validationErrors[] = "Card number must be exactly 12 digits";
        if (!preg_match('/^\d{3}$/', $cvv)) $validationErrors[] = "CVV must be exactly 3 digits";
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) $validationErrors[] = "Invalid expiry date format (MM/YY)";
        if (!$terms) $validationErrors[] = "You must accept the Terms & Conditions and Privacy Policy";

        if (!empty($validationErrors)) {
            $error = implode("<br>", $validationErrors);
        } else {
            // ====================
            // KUPON FELDOLGOZÁS
            // ====================
            if (!empty($coupon) && isset($validCoupons[$coupon])) {
                $appliedCoupon = $coupon;
                if ($validCoupons[$coupon] === 'FREESHIP') {
                    $shippingCost = 0.0;
                } else {
                    $discountAmount = $subTotal * floatval($validCoupons[$coupon]);
                }
            }

            // ====================
            // VÉGÖSSZEG SZÁMÍTÁS
            // ====================
            $totalPrice = round($subTotal - $discountAmount + $shippingCost, 2);
            $cardNumberMasked = 'XXXX-XXXX-' . substr($cardNumber, -4);
            $cvvMasked = '***';
            
            // ====================
            // RENDELÉS ADATOK KÉSZÍTÉS
            // ====================
            $orderDataArray = [
                'items' => $order_list,
                'coupon' => $appliedCoupon,
                'discount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'subtotal' => $subTotal
            ];
            
            $orderData = json_encode($orderDataArray, JSON_UNESCAPED_UNICODE);

            // ====================
            // ADATBÁZIS MŰVELET
            // ====================
            $stmt = $conn->prepare("INSERT INTO orders 
                (customer_name, customer_email, customer_address, card_type, card_number, expiry, cvv, order_data, total_price, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Processed', NOW())");
            
            if ($stmt === false) {
                $error = "Database error. Please try again later.";
            } else {
                $stmt->bind_param(
                    "ssssssssd",
                    $name,
                    $email,
                    $address,
                    $cardType,
                    $cardNumberMasked,
                    $expiry,
                    $cvvMasked,
                    $orderData,
                    $totalPrice
                );

                if ($stmt->execute()) {
                    $orderId = $conn->insert_id;  // Rendelés ID
                    
                    // ====================
                    // EMAIL KÜLDÉS A VÁSÁRLÓNÁK
                    // ====================
                    try {
                        $mail = new PHPMailer(true);
                        
                        // SMTP beállítások
                        $mail->isSMTP();
                        $mail->Host = SMTP_HOST;
                        $mail->SMTPAuth = true;
                        $mail->Username = SMTP_USERNAME;
                        $mail->Password = SMTP_PASSWORD;
                        $mail->SMTPSecure = SMTP_SECURE;
                        $mail->Port = SMTP_PORT;
                        
                        
                        // Feladó
                        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                        
                        // Címzett (vásárló)
                        $mail->addAddress($email, $name);
                        
                        // Válasz cím
                        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                        
                        // Email formátum
                        $mail->isHTML(true);
                        
                        // Email tárgy
                        $mail->Subject = '✅ Order Confirmation - Aqua Mini Shop #' . $orderId;
                        
                        //AMIT A FELHASZNÁLÓ ÉS AZ ADMIN KAP
                        $mail->Body = '
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                                .header { background: linear-gradient(135deg, #00796b 0%, #004d40 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                                .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; }
                                .order-id { font-size: 24px; font-weight: bold; color: #00796b; margin: 20px 0; }
                                .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                                .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                                .table th { background-color: #f0f0f0; }
                                .total-row { font-weight: bold; font-size: 18px; }
                                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="header">
                                    <h1>🎉 Thank You for Your Order!</h1>
                                    <p>Your order has been successfully placed</p>
                                </div>
                                
                                <div class="content">
                                    <h2>Order Details</h2>
                                    <p><strong>Order Number:</strong> <span class="order-id">#' . $orderId . '</span></p>
                                    <p><strong>Order Date:</strong> ' . date('Y-m-d H:i:s') . '</p>
                                    <p><strong>Customer:</strong> ' . htmlspecialchars($name) . '</p>
                                    <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                                    <p><strong>Shipping Address:</strong> ' . nl2br(htmlspecialchars($address)) . '</p>
                                    
                                    <h3>Order Summary</h3>
                                    <table class="table">
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>';
                        
                        //TERMÉKEK HOZZÁADÁSA
                        foreach ($order_list as $item) {
                            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                            $productName = htmlspecialchars($item['name'] ?? 'Product');
                            $total = $price * $qty;
                            
                            $mail->Body .= '
                                        <tr>
                                            <td>' . $productName . '</td>
                                            <td>' . $qty . '</td>
                                            <td>$' . number_format($price, 2) . '</td>
                                            <td>$' . number_format($total, 2) . '</td>
                                        </tr>';
                        }
                        
                        //TELJES ÖSSZEG -- TOTAL
                        $mail->Body .= '
                                        <tr>
                                            <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                                            <td>$' . number_format($subTotal, 2) . '</td>
                                        </tr>';
                        
                        if ($discountAmount > 0) {
                            $mail->Body .= '
                                        <tr>
                                            <td colspan="3" style="text-align: right;"><strong>Discount (' . htmlspecialchars($appliedCoupon) . '):</strong></td>
                                            <td style="color: green;">-$' . number_format($discountAmount, 2) . '</td>
                                        </tr>';
                        }
                        
                        $mail->Body .= '
                                        <tr>
                                            <td colspan="3" style="text-align: right;"><strong>Shipping:</strong></td>
                                            <td>' . ($shippingCost == 0 ? 'FREE' : '$' . number_format($shippingCost, 2)) . '</td>
                                        </tr>
                                        <tr class="total-row">
                                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                                            <td>$' . number_format($totalPrice, 2) . '</td>
                                        </tr>
                                    </table>
                                    
                                    <h3>Payment Information</h3>
                                    <p><strong>Payment Method:</strong> ' . htmlspecialchars($cardType) . ' ending in ' . substr($cardNumber, -4) . '</p>
                                    <p><strong>Status:</strong> <span style="color: #00796b; font-weight: bold;">Pending Processing</span></p>
                                    
                                    <h3>What Happens Next?</h3>
                                    <ol>
                                        <li>Your order is being prepared for shipment</li>
                                        <li>You will receive another email with tracking information</li>
                                        <li>Estimated delivery: 3-5 business days</li>
                                    </ol>
                                    
                                    <div class="footer">
                                        <p>If you have any questions, please contact us:</p>
                                        <p>📧 Email: <a href="mailto:kapcsitomo2022@gmail.com">aquaminishop@gmail.com</a></p>
                                        <p>📞 Phone: +36 70 123 4567</p>
                                        <p>🏪 Aqua Mini Shop</p>
                                        <p>© ' . date('Y') . ' Aqua Mini Shop. All rights reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </body>
                        </html>';
                        
                        //FELHASZNÁLÓNAK ÉS ADMINAK ÜZI
                        $mail->AltBody = "THANK YOU FOR YOUR ORDER!\n\n" .
                                        "Order Number: #" . $orderId . "\n" .
                                        "Order Date: " . date('Y-m-d H:i:s') . "\n" .
                                        "Customer: " . $name . "\n" .
                                        "Email: " . $email . "\n" .
                                        "Total Amount: $" . number_format($totalPrice, 2) . "\n\n" .
                                        "Your order is being processed and you will receive another email with tracking information once it ships.\n\n" .
                                        "If you have any questions, please contact us at kapcsitomo2022@gmail.com\n\n" .
                                        "Thank you for shopping with Aqua Mini Shop!";
                        
                        // Email a vásárlónak
                        $mail->send();
                        
                        // ====================
                        // EMAIL KÜLDÉS NEKEM MINT ADMIN
                        // ====================
                        $adminMail = new PHPMailer(true);
                        $adminMail->isSMTP();
                        $adminMail->Host = SMTP_HOST;
                        $adminMail->SMTPAuth = true;
                        $adminMail->Username = SMTP_USERNAME;
                        $adminMail->Password = SMTP_PASSWORD;
                        $adminMail->SMTPSecure = SMTP_SECURE;
                        $adminMail->Port = SMTP_PORT;
                        
                        $adminMail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                        $adminMail->addAddress('kapcsitomo2022@gmail.com', 'Admin');  // Neked küld
                        $adminMail->addAddress('kapcsandi.tomi@gmail.com', 'Admin');  // Másik email címedre is
                        
                        $adminMail->isHTML(true);
                        $adminMail->Subject = '📦 NEW ORDER #' . $orderId . ' - Aqua Mini Shop';
                        
                        $itemsCount = count($order_list);
                        
                        $adminMail->Body = '
                        <h2>📦 New Order Received!</h2>
                        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;">
                            <strong>URGENT:</strong> New order requires processing
                        </div>
                        
                        <h3>Order Details</h3>
                        <p><strong>Order ID:</strong> #' . $orderId . '</p>
                        <p><strong>Customer:</strong> ' . htmlspecialchars($name) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                        <p><strong>Items:</strong> ' . $itemsCount . ' products</p>
                        <p><strong>Total Amount:</strong> $' . number_format($totalPrice, 2) . '</p>
                        <p><strong>Order Date:</strong> ' . date('Y-m-d H:i:s') . '</p>
                        
                        <h3>Products Ordered:</h3>
                        <ul>';
                        
                        foreach ($order_list as $item) {
                            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                            $productName = htmlspecialchars($item['name'] ?? 'Product');
                            $adminMail->Body .= '<li>' . $productName . ' (x' . $qty . ') - $' . number_format($price * $qty, 2) . '</li>';
                        }
                        
                        $adminMail->Body .= '
                        </ul>
                        
                        <p><strong>Shipping Address:</strong><br>' . nl2br(htmlspecialchars($address)) . '</p>
                        
                        <p><strong>Payment Method:</strong> ' . htmlspecialchars($cardType) . '</p>
                        <p><strong>Card Number:</strong> ' . $cardNumberMasked . '</p>';
                        
                        if ($appliedCoupon) {
                            $adminMail->Body .= '<p><strong>Coupon Used:</strong> ' . htmlspecialchars($appliedCoupon) . '</p>';
                        }
                        
                        $adminMail->Body .= '
                        <br>
                        <a href="http://localhost/aqua-mini-shop/admin/orders.php?id=' . $orderId . '" style="background: #00796b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Order in Admin Panel</a>
                        ';
                        
                        $adminMail->AltBody = "NEW ORDER!\nOrder #" . $orderId . "\nCustomer: " . $name . "\nEmail: " . $email . "\nTotal: $" . number_format($totalPrice, 2) . "\nItems: " . $itemsCount;
                        
                        $adminMail->send();
                        
                        // Sikeres EMAIL
                        error_log("✅ Emails sent successfully for order #" . $orderId . " to " . $email);
                        
                    } catch (Exception $e) {
                        // Email HIBA, SIKERTLEN
                        error_log("❌ Email sending failed: " . $e->getMessage());
                    }
                    
                    //KUPONOK
                    if ($appliedCoupon) {
                        $couponLog = date('Y-m-d H:i:s') . " - Order #" . $orderId . " - User: $email, Coupon: $appliedCoupon, Discount: $discountAmount, Shipping: $shippingCost\n";
                        file_put_contents('coupon_log.txt', $couponLog, FILE_APPEND);
                    }
                    
                    //DELETE ORDER VAGYIS RENDELÉS TÖRLÉSE
                    unset($_SESSION['order']);
                    $success = true;
                    
                } else {
                    $error = "Error processing your order. Please try again.";
                }

                $stmt->close();
            }
        }
    }
}
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
                
                <p><strong>✅ Confirmation email has been sent to:</strong> <?= htmlspecialchars($email) ?></p>
                <p><strong>Total Paid:</strong> $<?= isset($totalPrice) ? number_format($totalPrice, 2) : '0.00' ?></p>
                
                <div class="action-buttons">
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="index.php" class="btn btn-outline">Back to Home</a>
                </div>
                
                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e0f7fa;">
                    <p><small>Need help? Contact us: <strong>kapcsitomo2022@gmail.com</strong></small></p>
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
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
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
                                   value="<?= htmlspecialchars($appliedCoupon ?? '') ?>">
                            <button type="button" class="coupon-btn" id="applyCouponBtn">Apply</button>
                        </div>
                        <div id="couponMessage" class="coupon-message"></div>
                        <p style="margin-top: 10px; font-size: 0.85em; color: var(--gray);">
                            Available: SUMMER10 (10% off), FREESHIP (free shipping)
                        </p>
                    </div>

                    <!-- BIZTONSÁGI JELZŐK, DISZNEK -->
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
                        <input type="hidden" name="coupon" id="couponField" value="<?= htmlspecialchars($appliedCoupon ?? '') ?>">
                        
                        <!-- NÉV -->
                        <div class="form-group">
                            <label class="form-label" for="fullname">Full Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="fullname" 
                                   name="fullname" 
                                   required
                                   value="<?= htmlspecialchars($_POST['fullname'] ?? $_SESSION['name'] ?? '') ?>">
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
                                   value="<?= htmlspecialchars($_POST['email'] ?? $_SESSION['email'] ?? '') ?>">
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
                                      placeholder="Street, City, Postal Code, Country"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
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
                                <option value="Visa" <?= (isset($_POST['card_type']) && $_POST['card_type'] == 'Visa') ? 'selected' : '' ?>>Visa</option>
                                <option value="MasterCard" <?= (isset($_POST['card_type']) && $_POST['card_type'] == 'MasterCard') ? 'selected' : '' ?>>MasterCard</option>
                                <option value="Revolut" <?= (isset($_POST['card_type']) && $_POST['card_type'] == 'Revolut') ? 'selected' : '' ?>>Revolut</option>
                                <option value="Maestro" <?= (isset($_POST['card_type']) && $_POST['card_type'] == 'Maestro') ? 'selected' : '' ?>>Maestro</option>
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
                                   value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>">
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
                                       value="<?= htmlspecialchars($_POST['expiry'] ?? '') ?>">
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
                                       value="<?= htmlspecialchars($_POST['cvv'] ?? '') ?>">
                                <div id="cvvError" class="error-text"></div>
                            </div>
                        </div>
                        
                        <!-- FELTÉTELEK -->
                        <div class="terms-check">
                            <input type="checkbox" id="terms" name="terms" required <?= (isset($_POST['terms']) && $_POST['terms'] == 'on') ? 'checked' : '' ?>>
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
        
        // KUPONOK, ITT IS HOZZÁ LEHET ADNI
        const validCoupons = {
            'SUMMER10': { type: 'percent', value: 0.10 },
            'FREESHIP': { type: 'shipping', value: 0 },
            'PSGKINGS': { type: 'percent', value: 0.40 }
        };
        
        // ELEMEK, A VALIDÁSLÁSHOZ
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
        
        //HIBAMEZŐŐŐK
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
        //CUPON ÜZENET, SIKERES VAGY SEM
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
            
            // Shipping megjelenítés
            if (shipping === 0) {
                shippingDisplay.innerHTML = '<span style="color: #4caf50;">FREE</span>';
            } else {
                shippingDisplay.textContent = '$' + shipping.toFixed(2);
            }
            
            //LEÁRAZÁS, AKCIO MEGJELENÉSE HA SIKER
            discountDisplay.textContent = '-$' + discount.toFixed(2);
            discountDisplay.style.color = discount > 0 ? '#4caf50' : '#6c7a89';
            
            //TELJES VAGYIS TOTAL AR
            totalDisplay.textContent = '$' + total.toFixed(2);
            
            //PAY GOMB SZOVEGE
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
                    showCouponMessage('Invalid coupon code. Available codes: SUMMER10, FREESHIP', false);
                    return;
                }
                
                //KUPON ELMENTESE EGY VALTOZOBAN
                couponField.value = code;
                
                // Kupon alkalmazása
                const coupon = validCoupons[code];
                
                if (coupon.type === 'percent') {
                    discount = parseFloat((subtotal * coupon.value).toFixed(2));
                    shipping = subtotal >= freeShippingOver ? 0 : shippingFlat;
                } else if (coupon.type === 'shipping') {
                    discount = 0;
                    shipping = 0;
                }
                
                // Összegek frissítése
                updateTotals();
                
                // Sikeres üzenet
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
        
        //EXPIRY VAGY LEJARAS VALIDALSA
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
        
        //CVC VALIDÁLÁSA
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
                
                // Minden hiba törlése
                ['fullname', 'email', 'address', 'card_type', 'card_number', 'expiry', 'cvv', 'terms'].forEach(clearError);
                
                // Validáció
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
                    //HIBAHOZ visszagoregetes
                    const firstError = document.querySelector('.input-error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
                
                //HA MINDEN KIRALY AKKOR EL KELL INDITANI
                processingOverlay.style.display = 'flex';
                payBtn.disabled = true;
                
                //KÉSLELETETÉS HOGY NE LEGYEN TURTELVE
                setTimeout(() => {
                    form.submit();
                }, 1500);
                
                return false;
            });
        }
        
        // INICIALIZÁLÁS!!!!!
        document.addEventListener('DOMContentLoaded', function() {
            updateTotals();
            
            //KUPON MUTATASA HA VAN!!!
            const appliedCoupon = couponField.value;
            if (appliedCoupon) {
                couponInput.value = appliedCoupon;
            }
        });
    </script>
</body>
</html>