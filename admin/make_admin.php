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
// ADMINISTRÁTORI JOG KINEVELÉSÉRE KIJELÖLT FELHASZNÁLÓ AZONOSÍTÓJA
// ============================================
// A GET paraméterből biztonságosan kinyerjük a felhasználó ID-ját
$id = intval($_GET['id']); // intval() biztonságosan alakítja egész számmá

// ============================================
// ÖNMAGUNK ADMINNAK NEVEZÉSÉNEK MEGAKADÁLYOZÁSA
// ============================================
// Ellenőrizzük, hogy a felhasználó nem próbálja-e önmagát adminná tenni
// (ami felesleges, mert már admin)
if ($id == $_SESSION['user_id']) {
    header("Location: admin.php?msg=You-are-already-admin");
    exit();
}

// ============================================
// ADMIN JOGOSULTSÁG MEGADÁSA AZ ADATBÁZISBAN
// ============================================
// SQL UPDATE utasítás előkészítése admin jog megadására
$query = $conn->prepare("UPDATE `4` SET is_admin = 1 WHERE id = ?");
// "i" = int
$query->bind_param("i", $id); 
$query->execute(); 

// ============================================
// SIKERES MŰVELET UTÁNI ÁTIRÁNYÍTÁS
// ============================================
// Sikeres admin jog adományozás után visszairányítjuk az admin felületre üzenettel
header("Location: admin.php?msg=Admin-granted");
exit(); 
?>