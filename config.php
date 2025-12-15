<?php
// ====================
// ADATBÁZIS KAPCSOLAT
// ====================
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

// ====================
// PHPMailer KONFIGURÁCIÓ (Gmail-hez)
// ===================

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'kapcsitomo2022@gmail.com');  
define('SMTP_PASSWORD', 'flqt nccv jkql zqgx');       
define('SMTP_FROM_EMAIL', 'kapcsitomo2022@gmail.com');
define('SMTP_FROM_NAME', 'Aqua Mini Shop');
define('SMTP_SECURE', 'tls');  
?>