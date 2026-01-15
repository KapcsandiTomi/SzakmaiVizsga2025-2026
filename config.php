<?php
// ====================
// ADATBÁZIS KAPCSOLAT 
// ====================

$host = 'localhost';
$dbname = 'users_db';
$username = 'root';
$password = '';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    
    $conn = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {
    die('Database connection error: ' . $e->getMessage());
}


// ====================
// PHPMailer KONFIGURÁCIÓ (Gmail)
// ====================

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'kapcsitomo2022@gmail.com');
define('SMTP_PASSWORD', 'flqt nccv jkql zqgx'); 
define('SMTP_FROM_EMAIL', 'kapcsitomo2022@gmail.com');
define('SMTP_FROM_NAME', 'Aqua Mini Shop');
define('SMTP_SECURE', 'tls');
