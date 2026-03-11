<?php

class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct() {
        try {
            // CONFIG BETÖLTÉSE
            $configPaths = [
                dirname(__DIR__) . '/config.php',
                $_SERVER['DOCUMENT_ROOT'] . '/Szak/config.php'
            ];

            $configLoaded = false;
            foreach ($configPaths as $path) {
                if (file_exists($path)) {
                    require $path;
                    if (isset($host, $dbname, $username, $password)) {
                        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
                        $this->connection = new PDO($dsn, $username, $password, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]);
                        $configLoaded = true;
                        break;
                    }
                }
            }

            if (!$configLoaded) {
                throw new Exception("Config not found or variables missing (host, dbname, username, password)");
            }

        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    // ====================
    // SINGLETON GET INSTANCE
    // ====================
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // ====================
    // CONNECTION LEKÉRÉSE
    // ====================
    public function getConnection(): PDO {
        return $this->connection;
    }

    // ====================
    // PREPARED QUERY FUTTATÁSA
    // ====================
    public function query(string $sql, array $params = []): PDOStatement|false {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }

    // ====================
    // TÖBB SOR LEKÉRÉSE
    // ====================
    public function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return [];
        return $stmt->fetchAll();
    }

    // ====================
    // EGY SOR LEKÉRÉSE
    // ====================
    public function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return null;
        return $stmt->fetch() ?: null;
    }

    // ====================
    // BESZÚRÁS UTÁNI LAST INSERT ID
    // ====================
    public function insert(string $sql, array $params = []): int|false {
        $stmt = $this->query($sql, $params);
        if (!$stmt) return false;
        return (int)$this->connection->lastInsertId();
    }
}
