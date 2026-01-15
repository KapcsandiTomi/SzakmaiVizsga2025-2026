<?php
class PCModel {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getAllCategories() {
        $stmt = $this->conn->query("SELECT * FROM pc_categories ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategoryById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM pc_categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addCategory($name) {
        $stmt = $this->conn->prepare("INSERT INTO pc_categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }
    
    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare("UPDATE pc_categories SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }
    
    public function deleteCategory($id) {
        $stmt = $this->conn->prepare("DELETE FROM pc_categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getAllProducts() {
        $stmt = $this->conn->query("
            SELECT p.*, c.name as category_name 
            FROM pc_products p 
            LEFT JOIN pc_categories c ON p.category_id = c.id 
            ORDER BY p.category_id, p.name
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM pc_products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addProduct($name, $price, $image, $category_id) {
        $stmt = $this->conn->prepare("INSERT INTO pc_products (name, price, image, category_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $price, $image, $category_id]);
    }
    
    public function updateProduct($id, $name, $price, $image, $category_id) {
        $stmt = $this->conn->prepare("UPDATE pc_products SET name = ?, price = ?, image = ?, category_id = ? WHERE id = ?");
        return $stmt->execute([$name, $price, $image, $category_id, $id]);
    }
    
    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM pc_products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function hasProductsInCategory($categoryId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM pc_products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'] > 0;
    }
}
?>
