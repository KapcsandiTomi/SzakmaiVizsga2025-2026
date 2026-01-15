<?php

class OrderData {
    private PDO $conn;

    public function __construct(PDO $db_connection) {
        $this->conn = $db_connection;
    }

    // ====================
    // RENDELÉSEK EMAIL ALAPJÁN
    // ====================
    public function getOrdersByEmail(string $email): array {
        $stmt = $this->conn->prepare(
            "SELECT id, total_price, status, created_at, customer_name
             FROM orders
             WHERE customer_email = :email
             ORDER BY created_at DESC"
        );

        $stmt->execute(['email' => $email]);
        return $stmt->fetchAll();
    }

    // ====================
    // RENDELÉS LEKÉRÉS ID + EMAIL
    // ====================
    public function getOrderById(int $order_id, string $email): ?array {
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
    // RENDELÉS LÉTREHOZÁS
    // ====================
    public function createOrder(array $order_data): int|false {
        $stmt = $this->conn->prepare(
            "INSERT INTO orders
            (customer_email, customer_name, total_price, status, created_at)
            VALUES (:email, :name, :total_price, 'Not Processed', NOW())"
        );

        $success = $stmt->execute([
            'email'       => $order_data['email'],
            'name'        => $order_data['name'],
            'total_price' => $order_data['total_price']
        ]);

        return $success ? (int)$this->conn->lastInsertId() : false;
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
    // RENDELÉS TÖRLÉS
    // ====================
    public function deleteOrder(int $order_id, string $email): bool {
        $stmt = $this->conn->prepare(
            "DELETE FROM orders
             WHERE id = :id AND customer_email = :email"
        );

        return $stmt->execute([
            'id'    => $order_id,
            'email' => $email
        ]);
    }
}
