<?php
class TrackData {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function getOrderByIdAndEmail($order_id, $email) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ? AND customer_email = ?");
        $stmt->bind_param("is", $order_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $order = $result->fetch_assoc();
        $stmt->close();
        
        return $order;
    }

    public function updateOrderStatus($order_id, $status, $email) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND customer_email = ?");
        $stmt->bind_param("sis", $status, $order_id, $email);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }

    public function getOrderTrackingData($order_id, $email) {
        $order = $this->getOrderByIdAndEmail($order_id, $email);
        
        if (!$order) {
            return null;
        }

        return [
            'order' => $order,
            'status_info' => $this->getStatusInfo(),
            'progress' => $this->calculateProgress($order['status'] ?? 'Not Processed')
        ];
    }

    private function getStatusInfo() {
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
                "PC Configuration Ordered" => "Your custom PC configuration has been ordered and is awaiting assembly."
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
                "Delivered",
            ]
        ];
    }

    private function calculateProgress($status) {
        if ($status === "PC Configuration Ordered") {
            return 25; 
        }
    
        $steps = ["Not Processed", "Processed", "Handed to Courier", "On the Way", "Delivered"];
        $currentStepIndex = array_search($status, $steps);
    
        if ($currentStepIndex === false) {
            if ($status === "PC Configuration Ordered") {
                return 25;
            }
            return 20;
        }
    
        return ($currentStepIndex + 1) / count($steps) * 100;
    }
}
?>
