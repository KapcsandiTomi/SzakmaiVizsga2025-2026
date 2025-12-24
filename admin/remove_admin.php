<?php
// ============================================
// MUNKAMENET KEZDÉS ÉS KONFIGURÁCIÓ
// ============================================
session_start(); 
require_once '../config.php'; 

// ============================================
// ADMIN JOGOSULTSÁG ELLENŐRZÉSE
// ============================================
// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve ÉS admin jogosultsággal rendelkezik
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    exit("Access denied."); // Ha nem admin, hozzáférés megtagadva
}

// ============================================
// ADMIN JOG VISSZAVONÁSÁRA KIJELÖLT FELHASZNÁLÓ AZONOSÍTÓJA
// ============================================
// A GET paraméterből biztonságosan kinyerjük a felhasználó ID-ját
$id = intval($_GET['id']); // intval() biztonságosan alakítja egész számmá

// ============================================
// ÖNMAGUNK ADMIN JOGÁNAK VISSZAVONÁSÁNAK MEGAKADÁLYOZÁSA
// ============================================
// Ellenőrizzük, hogy a felhasználó nem próbálja-e önmaga admin jogát visszavonni
// Ez kritikus, mert önmagad admin jogának elvétele zárolná a rendszert!
if ($id == $_SESSION['user_id']) {
    header("Location: admin.php?msg=You-cannot-remove-yourself");
    exit(); 
}

// ============================================
// ADMIN JOGOSULTSÁG VISSZAVONÁSA AZ ADATBÁZISBAN
// ============================================
$query = $conn->prepare("UPDATE `4` SET is_admin = 0 WHERE id = ?");
// "i" = integer paraméter típusa
$query->bind_param("i", $id); // Biztonságos paraméter kötés SQL injection ellen
$query->execute();

// ============================================
// SIKERES MŰVELET UTÁNI ÁTIRÁNYÍTÁS
// ============================================
// Sikeres admin jog visszavonás után visszairányítjuk az admin felületre üzenettel
header("Location: admin.php?msg=Admin-removed");
exit();
?>