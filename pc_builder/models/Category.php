<?php
class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $sql = "SELECT id, name FROM pc_categories ORDER BY id";
        return $this->db->fetchAll($sql);
    }
    
    public function getById($id) {
        $sql = "SELECT id, name FROM pc_categories WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
}
?>