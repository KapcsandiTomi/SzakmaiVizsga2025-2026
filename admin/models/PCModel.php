<?php
class PCModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getAllCategories() {
        $result = $this->conn->query("SELECT * FROM pc_categories ORDER BY id ASC");
        $categories = [];
        
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    public function getCategoryById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM pc_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function addCategory($name) {
        $stmt = $this->conn->prepare("INSERT INTO pc_categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }
    
    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare("UPDATE pc_categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }
    
    public function deleteCategory($id) {
        $stmt = $this->conn->prepare("DELETE FROM pc_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function getAllProducts() {
        $result = $this->conn->query("
            SELECT p.*, c.name as category_name 
            FROM pc_products p 
            LEFT JOIN pc_categories c ON p.category_id = c.id 
            ORDER BY p.category_id, p.name
        ");
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM pc_products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function addProduct($name, $price, $image, $category_id) {
        $stmt = $this->conn->prepare("INSERT INTO pc_products (name, price, image, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $name, $price, $image, $category_id);
        return $stmt->execute();
    }
    
    public function updateProduct($id, $name, $price, $image, $category_id) {
        $stmt = $this->conn->prepare("UPDATE pc_products SET name = ?, price = ?, image = ?, category_id = ? WHERE id = ?");
        $stmt->bind_param("sdsii", $name, $price, $image, $category_id, $id);
        return $stmt->execute();
    }
    
    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM pc_products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function hasProductsInCategory($categoryId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM pc_products WHERE category_id = ?");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
}
?>