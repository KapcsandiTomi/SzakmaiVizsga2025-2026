<?php

class TrackData {
    private PDO $conn;

    public function __construct(PDO $db_connection) {
        $this->conn = $db_connection;
    }

    // ====================
    // RENDELÉS LEKÉRÉSE ID + EMAIL
    // ====================
    public function getOrderByIdAndEmail(int $order_id, string $email): ?array {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM orders
             WHERE id = :id AND customer_email = :email"
        );

        $stmt->execute([
            'id'    => $order_id,
            'email' => $email
        ]);

        $order = $stmt->fetch();
        return $order ?: null;
    }

    // ====================
    // RENDELÉS STÁTUSZ FRISSÍTÉS
    // ====================
    public function updateOrderStatus(
        int $order_id,
        string $status,
        string $email
    ): bool {
        $stmt = $this->conn->prepare(
            "UPDATE orders
             SET status = :status
             WHERE id = :id AND customer_email = :email"
        );

        return $stmt->execute([
            'status' => $status,
            'id'     => $order_id,
            'email'  => $email
        ]);
    }

    // ====================
    // RENDELÉS TRACKING ADATOK
    // ====================
    public function getOrderTrackingData(int $order_id, string $email): ?array {
        $order = $this->getOrderByIdAndEmail($order_id, $email);

        if (!$order) {
            return null;
        }

        return [
            'order'       => $order,
            'status_info' => $this->getStatusInfo(),
            'progress'    => $this->calculateProgress(
                $order['status'] ?? 'Not Processed'
            )
        ];
    }

    // ====================
    // STÁTUSZ METAADATOK
    // ====================
    private function getStatusInfo(): array {
        return [
            'colors' => [
                "Not Processed" => "#ff4d4d",
                "Processed" => "#ffa500",
                "Handed to Courier" => "#1e90ff",
                "On the Way" => "#ffff66",
                "Delivered" => "#32cd32",
                "PC Configuration Ordered" => "#ff9800"
            ],
            'descriptions' => [
                "Not Processed" => "Your order has been received and is waiting to be processed.",
                "Processed" => "Your order has been processed and is being prepared for shipment.",
                "Handed to Courier" => "Your order has been handed over to the courier service.",
                "On the Way" => "Your order is on the way to your address.",
                "Delivered" => "Your order has been delivered successfully.",
                "PC Configuration Ordered" =>
                    "Your custom PC configuration has been ordered and is awaiting assembly."
            ],
            'icons' => [
                "Not Processed" => "fas fa-clock",
                "Processed" => "fas fa-cogs",
                "Handed to Courier" => "fas fa-handshake",
                "On the Way" => "fas fa-truck",
                "Delivered" => "fas fa-check-circle",
                "PC Configuration Ordered" => "fas fa-desktop"
            ],
            'steps' => [
                "Not Processed",
                "Processed",
                "Handed to Courier",
                "On the Way",
                "Delivered"
            ]
        ];
    }

    // ====================
    // HALADÁS SZÁMÍTÁS
    // ====================
    private function calculateProgress(string $status): float {
        if ($status === "PC Configuration Ordered") {
            return 25.0;
        }

        $steps = [
            "Not Processed",
            "Processed",
            "Handed to Courier",
            "On the Way",
            "Delivered"
        ];

        $index = array_search($status, $steps, true);

        if ($index === false) {
            return 20.0;
        }

        return (($index + 1) / count($steps)) * 100;
    }
}
