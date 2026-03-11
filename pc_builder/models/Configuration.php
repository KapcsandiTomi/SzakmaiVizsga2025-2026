<?php
class Configuration {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getOrCreateConfigId($sessionId) {
        $sql = "SELECT id FROM pc_configurations WHERE session_id = ?";
        $config = $this->db->fetchOne($sql, [$sessionId]);
        
        if ($config) {
            return $config['id'];
        }
        
        $sql = "INSERT INTO pc_configurations (session_id) VALUES (?)";
        return $this->db->insert($sql, [$sessionId]);
    }
    
    public function addItem($configId, $categoryId, $productId) {
        $sql = "DELETE FROM pc_configuration_items WHERE configuration_id = ? AND category_id = ?";
        $this->db->query($sql, [$configId, $categoryId]);
        
        $sql = "INSERT INTO pc_configuration_items (configuration_id, category_id, product_id) VALUES (?, ?, ?)";
        return $this->db->insert($sql, [$configId, $categoryId, $productId]);
    }
    
    public function getItems($sessionId) {
        $sql = "SELECT i.id as item_id, p.name, p.price
                FROM pc_configuration_items i
                JOIN pc_products p ON p.id = i.product_id
                JOIN pc_configurations c ON c.id = i.configuration_id
                WHERE c.session_id = ?
                ORDER BY p.category_id";
        
        return $this->db->fetchAll($sql, [$sessionId]);
    }
    
    public function removeItem($itemId) {
        $sql = "DELETE FROM pc_configuration_items WHERE id = ?";
        return $this->db->query($sql, [$itemId]);
    }

    public function clearConfiguration($sessionId) {
        $sql = "DELETE i FROM pc_configuration_items i
                JOIN pc_configurations c ON c.id = i.configuration_id
                WHERE c.session_id = ?";
        $this->db->query($sql, [$sessionId]);
    
        $sql = "DELETE FROM pc_configurations WHERE session_id = ?";
        return $this->db->query($sql, [$sessionId]);
    }
}
?>