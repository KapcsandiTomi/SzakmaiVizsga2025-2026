<?php
class ProductController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    public function getByCategory() {
        if (!isset($_GET['cat']) || !is_numeric($_GET['cat'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Valid category ID required']);
            exit;
        }
        
        $categoryId = (int)$_GET['cat'];
        
        try {
            $products = $this->productModel->getByCategory($categoryId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products)
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
    }
}
?>