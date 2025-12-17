<?php
// ============================================
// MUNKAMENET KEZDÉS ÉS KONFIGURÁCIÓ
// ============================================
session_start(); 
require_once '../config.php'; // Adatbázis kapcsolat betöltése

// ============================================
// ADMIN JOGOSULTSÁG ELLENŐRZÉSE
// ============================================
// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve és admin-e
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    exit("Access denied."); // Ha nem admin, hozzáférés megtagadva
}

// ============================================
// TÖRLENDŐ FELHASZNÁLÓ ID MEGKAPÁSA
// ============================================
// A GET paraméterből kinyerjük a törlendő felhasználó ID-ját
$id = intval($_GET['id']); // intval() biztonságosan alakítja számmá

// ============================================
// ÖNMAGUNK TÖRLÉSÉNEK MEGAKADÁLYOZÁSA
// ============================================
// Ellenőrizzük, hogy a felhasználó nem próbálja-e önmagát törölni
if ($id == $_SESSION['user_id']) {
    exit("You cannot delete yourself!"); // Önmagad törlésének blokkolása
}

// ============================================
// ADATBÁZIS TÖRLÉSI MŰVELET
// ============================================
// Felkészítjük a törlés SQL parancsát
$stmt = $conn->prepare("DELETE FROM `4` WHERE id = ?");

$stmt->bind_param("i", $id); // A paraméter biztonságos kötése i: int
$stmt->execute(); // Parancs végrehajtása

// ============================================
// SIKERES TÖRLÉS UTÁNI ÁTIRÁNYÍTÁS
// ============================================
// Sikeres törlés után visszairányítjuk az admin felületre üzenettel
header("Location: admin.php?msg=User-deleted");
exit(); 
?>