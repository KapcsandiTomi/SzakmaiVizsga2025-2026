<?php
class CheckoutData {
    private $conn;
    
    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }
    
    public function saveOrder($orderData) {
        $stmt = $this->conn->prepare("INSERT INTO orders 
            (customer_name, customer_email, customer_address, card_type, card_number, expiry, cvv, order_data, total_price, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Processed', NOW())");
        
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database preparation error'];
        }
        
        $stmt->bind_param(
            "ssssssssd",
            $orderData['customer_name'],
            $orderData['customer_email'],
            $orderData['customer_address'],
            $orderData['card_type'],
            $orderData['card_number'],
            $orderData['expiry'],
            $orderData['cvv'],
            $orderData['order_data'],
            $orderData['total_price']
        );
        
        if ($stmt->execute()) {
            $orderId = $stmt->insert_id;
            $stmt->close();
            return ['success' => true, 'order_id' => $orderId];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $error];
        }
    }
    
    public function validateCoupon($couponCode) {
        $validCoupons = [
            'SUMMER10' => ['type' => 'percent', 'value' => 0.10],
            'FREESHIP' => ['type' => 'shipping', 'value' => 0],
            'PSGKINGS' => ['type' => 'percent', 'value' => 0.40]
        ];
        
        $couponCode = strtoupper(trim($couponCode));
        
        if (empty($couponCode)) {
            return ['valid' => false, 'message' => 'Please enter a coupon code'];
        }
        
        if (!isset($validCoupons[$couponCode])) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }
        
        return ['valid' => true, 'coupon' => $validCoupons[$couponCode], 'code' => $couponCode];
    }
    
    public function calculateTotals($subTotal, $couponCode = null, $shippingFlat = 4.99, $freeShippingOver = 50.00) {
        $shippingCost = ($subTotal >= $freeShippingOver) ? 0.0 : $shippingFlat;
        $discountAmount = 0.0;
        
        if ($couponCode) {
            $couponResult = $this->validateCoupon($couponCode);
            
            if ($couponResult['valid']) {
                $coupon = $couponResult['coupon'];
                
                if ($coupon['type'] === 'percent') {
                    $discountAmount = $subTotal * $coupon['value'];
                } elseif ($coupon['type'] === 'shipping') {
                    $shippingCost = 0.0;
                }
            }
        }
        
        $totalPrice = $subTotal - $discountAmount + $shippingCost;
        
        return [
            'subtotal' => $subTotal,
            'shipping_cost' => $shippingCost,
            'discount_amount' => $discountAmount,
            'total_price' => $totalPrice,
            'coupon_code' => $couponCode,
            'coupon_valid' => $couponCode ? $couponResult['valid'] : false
        ];
    }
    
    public function logCouponUsage($orderId, $email, $couponCode, $discountAmount, $shippingCost) {
        $logEntry = date('Y-m-d H:i:s') . " - Order #" . $orderId . " - User: $email, Coupon: $couponCode, Discount: $discountAmount, Shipping: $shippingCost\n";
        file_put_contents('coupon_log.txt', $logEntry, FILE_APPEND);
        return true;
    }
}
?>