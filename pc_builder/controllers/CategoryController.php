<?php
class CategoryController {
    private $categoryModel;
    
    public function __construct() {
        $this->categoryModel = new Category();
    }
    
    public function index() {
        try {
            $categories = $this->categoryModel->getAll();
            
            if (empty($categories)) {
                error_log("No categories found in database");
            }
            
            require_once dirname(__DIR__) . '/views/index.php';
        } catch (Exception $e) {
            die("Error loading categories: " . $e->getMessage());
        }
    }
}
?>