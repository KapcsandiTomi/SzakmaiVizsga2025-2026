<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $configPaths = [
                dirname(dirname(__DIR__)) . '/config.php',  
                dirname(__DIR__) . '/../config.php',        
                $_SERVER['DOCUMENT_ROOT'] . '/Szak/config.php'
            ];
            
            $configLoaded = false;
            foreach ($configPaths as $configPath) {
                if (file_exists($configPath)) {
                    require_once $configPath;
                    if (isset($conn)) {
                        $this->connection = $conn;
                        $configLoaded = true;
                        break;
                    }
                }
            }
            
            if (!$configLoaded) {
                throw new Exception("Config file not found or $conn variable not set");
            }

            if ($this->connection->connect_error) {
                throw new Exception("Database connection failed: " . $this->connection->connect_error);
            }
            
        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            
            if (!empty($params)) {
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                }
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            return $stmt;
            
        } catch (Exception $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return [];
        
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return null;
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }
    
    public function insert($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return false;
        
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }
}
?>