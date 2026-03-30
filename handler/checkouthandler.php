<?php
require_once __DIR__ . '/checkoutdata.php';
require_once __DIR__ . '/../src/PHPMailer.php';
require_once __DIR__ . '/../src/Exception.php';
require_once __DIR__ . '/../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CheckoutHandler {
    private $model;
    private $orderList;
    private $subTotal;
    private $sessionData;
    
    public function __construct($database_connection, $orderList, $sessionData = []) {
        $this->model = new CheckoutData($database_connection);
        $this->orderList = $orderList;
        $this->sessionData = $sessionData;
        $this->calculateSubTotal();
    }
    
    private function calculateSubTotal() {
        $this->subTotal = 0.0;
        foreach ($this->orderList as $item) {
            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $this->subTotal += $price * $qty;
        }
    }
    
    public function getSubTotal() {
        return $this->subTotal;
    }
    
    public function calculateTotals($couponCode = null) {
        return $this->model->calculateTotals($this->subTotal, $couponCode);
    }
    
    public function validateForm($postData) {
        $errors = [];
        
        $name = trim($postData["fullname"] ?? "");
        $email = trim($postData["email"] ?? "");
        $address = trim($postData["address"] ?? "");
        $cardType = trim($postData["card_type"] ?? "");
        $cardNumber = preg_replace('/\D/', '', $postData["card_number"] ?? "");
        $expiry = trim($postData["expiry"] ?? "");
        $cvv = preg_replace('/\D/', '', $postData["cvv"] ?? "");
        $terms = isset($postData['terms']) ? true : false;
        
        if (empty($name)) $errors[] = "Full name is required";
        if (empty($email)) $errors[] = "Email address is required";
        if (empty($address)) $errors[] = "Shipping address is required";
        if (empty($cardType)) $errors[] = "Card type is required";
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address format";
        }
        
        if (!preg_match('/^\d{12}$/', $cardNumber)) {
            $errors[] = "Card number must be exactly 12 digits";
        }
        
        if (!preg_match('/^\d{3}$/', $cvv)) {
            $errors[] = "CVV must be exactly 3 digits";
        }
        
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
            $errors[] = "Invalid expiry date format (MM/YY)";
        }
        
        if (!$terms) {
            $errors[] = "You must accept the Terms & Conditions and Privacy Policy";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => [
                'name' => $name,
                'email' => $email,
                'address' => $address,
                'card_type' => $cardType,
                'card_number' => $cardNumber,
                'expiry' => $expiry,
                'cvv' => $cvv,
                'coupon' => trim($postData['coupon'] ?? "")
            ]
        ];
    }
    
    public function processOrder($formData) {
        $validation = $this->validateForm($formData);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'type' => 'validation',
                'errors' => $validation['errors']
            ];
        }
        
        $data = $validation['data'];
        $totals = $this->calculateTotals($data['coupon']);
        
        $orderDataArray = [
            'items' => $this->orderList,
            'coupon' => $data['coupon'] ?: null,
            'discount' => $totals['discount_amount'],
            'shipping_cost' => $totals['shipping_cost'],
            'subtotal' => $this->subTotal
        ];
        
        $orderDataJson = json_encode($orderDataArray, JSON_UNESCAPED_UNICODE);
        
        $orderData = [
            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'customer_address' => $data['address'],
            'card_type' => $data['card_type'],
            'card_number' => 'XXXX-XXXX-' . substr($data['card_number'], -4),
            'expiry' => $data['expiry'],
            'cvv' => '***',
            'order_data' => $orderDataJson,
            'total_price' => $totals['total_price']
        ];
        
        $result = $this->model->saveOrder($orderData);
        
        if (!$result['success']) {
            return [
                'success' => false,
                'type' => 'database',
                'error' => $result['error']
            ];
        }
        
        $orderId = $result['order_id'];
        
        if ($data['coupon'] && $totals['coupon_valid']) {
            $this->model->logCouponUsage($orderId, $data['email'], $data['coupon'], 
                $totals['discount_amount'], $totals['shipping_cost']);
        }
        
        $emailResult = $this->sendEmails($orderId, $data, $totals);
        
        if (!$emailResult['success']) {
            error_log("Email sending failed for order #" . $orderId . ": " . $emailResult['error']);
        }
        
        return [
            'success' => true,
            'order_id' => $orderId,
            'customer_email' => $data['email'],
            'customer_name' => $data['name'],
            'total_price' => $totals['total_price'],
            'email_sent' => $emailResult['success']
        ];
    }
    
    private function sendEmails($orderId, $customerData, $totals) {
        try {
            $customerEmailResult = $this->sendCustomerEmail($orderId, $customerData, $totals);
            $adminEmailResult = $this->sendAdminEmail($orderId, $customerData, $totals);
            
            return [
                'success' => $customerEmailResult && $adminEmailResult,
                'customer_sent' => $customerEmailResult,
                'admin_sent' => $adminEmailResult
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function sendCustomerEmail($orderId, $customerData, $totals) {
        try {
            $mail = new PHPMailer(true);
            
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($customerData['email'], $customerData['name']);
            $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            
            $mail->isHTML(true);
            $mail->Subject = '✅ Order Confirmation - Aqua Mini Shop #' . $orderId;
            
            $mail->Body = $this->generateCustomerEmailBody($orderId, $customerData, $totals);
            $mail->AltBody = $this->generateCustomerEmailText($orderId, $customerData, $totals);
            
            return $mail->send();
        } catch (Exception $e) {
            error_log("Customer email failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendAdminEmail($orderId, $customerData, $totals) {
        try {
            $mail = new PHPMailer(true);
            
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress('kapcsitomo2022@gmail.com', 'Admin');
            $mail->addAddress('kapcsandi.tomi@gmail.com', 'Admin');
            
            $mail->isHTML(true);
            $mail->Subject = '📦 NEW ORDER #' . $orderId . ' - Aqua Mini Shop';
            
            $mail->Body = $this->generateAdminEmailBody($orderId, $customerData, $totals);
            $mail->AltBody = $this->generateAdminEmailText($orderId, $customerData, $totals);
            
            return $mail->send();
        } catch (Exception $e) {
            error_log("Admin email failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function generateCustomerEmailBody($orderId, $customerData, $totals) {
        $itemsHtml = '';
        foreach ($this->orderList as $item) {
            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $name = htmlspecialchars($item['name'] ?? 'Product');
            $total = $price * $qty;
            
            $itemsHtml .= '
            <tr>
                <td>' . $name . '</td>
                <td>' . $qty . '</td>
                <td>$' . number_format($price, 2) . '</td>
                <td>$' . number_format($total, 2) . '</td>
            </tr>';
        }
        
        $couponHtml = '';
        if ($totals['discount_amount'] > 0 && $customerData['coupon']) {
            $couponHtml = '
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Discount (' . htmlspecialchars($customerData['coupon']) . '):</strong></td>
                <td style="color: green;">-$' . number_format($totals['discount_amount'], 2) . '</td>
            </tr>';
        }
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: cursive; line-height: 1.6; color: #333; }
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
                    <p><strong>Customer:</strong> ' . htmlspecialchars($customerData['name']) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($customerData['email']) . '</p>
                    <p><strong>Shipping Address:</strong> ' . nl2br(htmlspecialchars($customerData['address'])) . '</p>
                    
                    <h3>Order Summary</h3>
                    <table class="table">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                        ' . $itemsHtml . '
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                            <td>$' . number_format($this->subTotal, 2) . '</td>
                        </tr>
                        ' . $couponHtml . '
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Shipping:</strong></td>
                            <td>' . ($totals['shipping_cost'] == 0 ? 'FREE' : '$' . number_format($totals['shipping_cost'], 2)) . '</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td>$' . number_format($totals['total_price'], 2) . '</td>
                        </tr>
                    </table>
                    
                    <h3>Payment Information</h3>
                    <p><strong>Payment Method:</strong> ' . htmlspecialchars($customerData['card_type']) . ' ending in ' . substr(preg_replace('/\D/', '', $customerData['card_number'] ?? ''), -4) . '</p>
                    <p><strong>Status:</strong> <span style="color: #00796b; font-weight: bold;">Pending Processing</span></p>
                    
                    <h3>What Happens Next?</h3>
                    <ol>
                        <li>Your order is being prepared for shipment</li>
                        <li>You will receive another email with tracking information</li>
                        <li>Estimated delivery: 3-5 business days</li>
                    </ol>
                    
                    <div class="footer">
                        <p>If you have any questions, please contact us:</p>
                        <p>📧 Email: <a href="mailto:aquaminishop@gmail.com">aquaminishop@gmail.com</a></p>
                        <p>📞 Phone: +36 70 123 4567</p>
                        <p>🏪 Aqua Mini Shop</p>
                        <p>© ' . date('Y') . ' Aqua Mini Shop. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
    }
    
    private function generateCustomerEmailText($orderId, $customerData, $totals) {
        return "THANK YOU FOR YOUR ORDER!\n\n" .
               "Order Number: #" . $orderId . "\n" .
               "Order Date: " . date('Y-m-d H:i:s') . "\n" .
               "Customer: " . $customerData['name'] . "\n" .
               "Email: " . $customerData['email'] . "\n" .
               "Total Amount: $" . number_format($totals['total_price'], 2) . "\n\n" .
               "Your order is being processed and you will receive another email with tracking information once it ships.\n\n" .
               "If you have any questions, please contact us at aquaminishop@gmail.com\n\n" .
               "Thank you for shopping with Aqua Mini Shop!";
    }
    
    private function generateAdminEmailBody($orderId, $customerData, $totals) {
        $itemsList = '';
        foreach ($this->orderList as $item) {
            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $name = htmlspecialchars($item['name'] ?? 'Product');
            $itemsList .= '<li>' . $name . ' (x' . $qty . ') - $' . number_format($price * $qty, 2) . '</li>';
        }
        
        $couponInfo = '';
        if ($customerData['coupon']) {
            $couponInfo = '<p><strong>Coupon Used:</strong> ' . htmlspecialchars($customerData['coupon']) . '</p>';
        }
        
        return '
        <h2>📦 New Order Received!</h2>
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;">
            <strong>URGENT:</strong> New order requires processing
        </div>
        
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> #' . $orderId . '</p>
        <p><strong>Customer:</strong> ' . htmlspecialchars($customerData['name']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($customerData['email']) . '</p>
        <p><strong>Items:</strong> ' . count($this->orderList) . ' products</p>
        <p><strong>Total Amount:</strong> $' . number_format($totals['total_price'], 2) . '</p>
        <p><strong>Order Date:</strong> ' . date('Y-m-d H:i:s') . '</p>
        
        <h3>Products Ordered:</h3>
        <ul>' . $itemsList . '</ul>
        
        <p><strong>Shipping Address:</strong><br>' . nl2br(htmlspecialchars($customerData['address'])) . '</p>
        
        <p><strong>Payment Method:</strong> ' . htmlspecialchars($customerData['card_type']) . '</p>
        <p><strong>Card Number:</strong> XXXX-XXXX-' . substr(preg_replace('/\D/', '', $customerData['card_number'] ?? ''), -4) . '</p>
        ' . $couponInfo . '
        <br>
        <a href="http://localhost/aqua-mini-shop/admin/orders.php?id=' . $orderId . '" style="background: #00796b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Order in Admin Panel</a>';
    }
    
    private function generateAdminEmailText($orderId, $customerData, $totals) {
        return "NEW ORDER!\nOrder #" . $orderId . "\nCustomer: " . $customerData['name'] . "\nEmail: " . $customerData['email'] . "\nTotal: $" . number_format($totals['total_price'], 2) . "\nItems: " . count($this->orderList);
    }
    
    public function clearCart() {
        unset($_SESSION['order']);
        return true;
    }
}
?>