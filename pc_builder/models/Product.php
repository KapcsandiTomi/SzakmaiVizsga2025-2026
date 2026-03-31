<?php
class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getByCategory($categoryId) {
        $sql = "SELECT id, name, price FROM pc_products WHERE category_id = ? ORDER BY price";
        return $this->db->fetchAll($sql, [$categoryId]);
    }
    
    public function getById($productId) {
        $sql = "SELECT id, name, price, category_id FROM pc_products WHERE id = ?";
        return $this->db->fetchOne($sql, [$productId]);
    }
}
?>