<?php
// config.php - az adatbázis kapcsolat
$host = 'localhost';
$dbname = 'users_db';
$username = 'root';
$password = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Hibakezelés
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Karakterkódolás beállítása
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>