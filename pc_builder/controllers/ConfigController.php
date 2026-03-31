<?php
class ConfigController {
    private $configModel;
    private $productModel;
    
    public function __construct() {
        $this->configModel = new Configuration();
        $this->productModel = new Product();
    }
    
    public function addItem() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionId = session_id();
        
        $jsonInput = file_get_contents('php://input');
        if (empty($jsonInput)) {
            http_response_code(400);
            echo json_encode(['error' => 'No input data']);
            exit;
        }
        
        $input = json_decode($jsonInput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data: ' . json_last_error_msg()]);
            exit;
        }
    
        if (!isset($input['product_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing product_id']);
            exit;
        }
    
        $productId = (int)$input['product_id'];
        $product = $this->productModel->getById($productId);
    
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            exit;
        }
    
        try {
            $configId = $this->configModel->getOrCreateConfigId($sessionId);
            $result = $this->configModel->addItem($configId, $product['category_id'], $productId);
    
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Product added to configuration'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add item to configuration']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
    }
    
    public function getItems() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionId = session_id();
        $items = $this->configModel->getItems($sessionId);
        
        $total = 0;
        foreach ($items as $item) {
            $total += (float)$item['price'];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'items' => $items,
            'total' => $total,
            'count' => count($items)
        ]);
    }
    
    public function removeItem() {
        // JSON adatok olvasása
        $jsonInput = file_get_contents('php://input');
        if (empty($jsonInput)) {
            http_response_code(400);
            echo json_encode(['error' => 'No input data']);
            exit;
        }
        
        $input = json_decode($jsonInput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            exit;
        }
        
        if (!isset($input['item_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing item_id']);
            exit;
        }
        
        $itemId = (int)$input['item_id'];
        
        try {
            $result = $this->configModel->removeItem($itemId);
            
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to remove item']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
    }
}
?>