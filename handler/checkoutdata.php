<?php

class CheckoutData {
    private PDO $conn;

    public function __construct(PDO $database_connection) {
        $this->conn = $database_connection;
    }

    public function saveOrder(array $orderData): array {

        $sql = "
            INSERT INTO orders (
                customer_name,
                customer_email,
                customer_address,
                card_type,
                card_number,
                expiry,
                cvv,
                order_data,
                total_price,
                status,
                created_at
            ) VALUES (
                :customer_name,
                :customer_email,
                :customer_address,
                :card_type,
                :card_number,
                :expiry,
                :cvv,
                :order_data,
                :total_price,
                'Not Processed',
                NOW()
            )
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'error' => 'Database preparation error'];
        }

        try {
            $stmt->execute([
                'customer_name'    => $orderData['customer_name'],
                'customer_email'   => $orderData['customer_email'],
                'customer_address' => $orderData['customer_address'],
                'card_type'        => $orderData['card_type'],
                'card_number'      => $orderData['card_number'],
                'expiry'           => $orderData['expiry'],
                'cvv'              => $orderData['cvv'],
                'order_data'       => $orderData['order_data'],
                'total_price'      => $orderData['total_price']
            ]);

            return [
                'success'  => true,
                'order_id' => (int)$this->conn->lastInsertId()
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    public function validateCoupon(string $couponCode): array {
        $validCoupons = [
            'SUMMER10' => ['type' => 'percent', 'value' => 0.10],
            'FREESHIP' => ['type' => 'shipping', 'value' => 0],
            'PSGKINGS' => ['type' => 'percent', 'value' => 0.40]
        ];

        $couponCode = strtoupper(trim($couponCode));

        if ($couponCode === '') {
            return ['valid' => false, 'message' => 'Please enter a coupon code'];
        }

        if (!isset($validCoupons[$couponCode])) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }

        return [
            'valid'  => true,
            'coupon' => $validCoupons[$couponCode],
            'code'   => $couponCode
        ];
    }

    public function calculateTotals(
        float $subTotal,
        ?string $couponCode = null,
        float $shippingFlat = 4.99,
        float $freeShippingOver = 50.00
    ): array {

        $shippingCost = ($subTotal >= $freeShippingOver) ? 0.0 : $shippingFlat;
        $discountAmount = 0.0;
        $couponValid = false;

        if ($couponCode) {
            $couponResult = $this->validateCoupon($couponCode);

            if ($couponResult['valid']) {
                $couponValid = true;
                $coupon = $couponResult['coupon'];

                if ($coupon['type'] === 'percent') {
                    $discountAmount = $subTotal * $coupon['value'];
                }

                if ($coupon['type'] === 'shipping') {
                    $shippingCost = 0.0;
                }
            }
        }

        $totalPrice = max(0, $subTotal - $discountAmount + $shippingCost);

        return [
            'subtotal'        => $subTotal,
            'shipping_cost'   => $shippingCost,
            'discount_amount' => $discountAmount,
            'total_price'     => $totalPrice,
            'coupon_code'     => $couponCode,
            'coupon_valid'    => $couponValid
        ];
    }

    public function logCouponUsage(
        int $orderId,
        string $email,
        ?string $couponCode,
        float $discountAmount,
        float $shippingCost
    ): bool {

        $logEntry = sprintf(
            "[%s] Order #%d | %s | Coupon: %s | Discount: %.2f | Shipping: %.2f\n",
            date('Y-m-d H:i:s'),
            $orderId,
            $email,
            $couponCode ?? 'NONE',
            $discountAmount,
            $shippingCost
        );

        return (bool)file_put_contents(
            __DIR__ . '/coupon_log.txt',
            $logEntry,
            FILE_APPEND | LOCK_EX
        );
    }
}
