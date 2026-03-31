<?php
// ============================================
// MUNKAMENET KEZDÉS
// ============================================
session_start();

// ============================================
// TERMÉK ADATOK ELLENŐRZÉSE
// ============================================
// Ellenőrizzük, hogy mindhárom kötelező termékadat érkezett-e
// Ezen adatok nélkül nem tudjuk hozzáadni a terméket a rendeléshez
if (!isset($_POST['product_name'], $_POST['product_price'], $_POST['product_image'])) {
    die("Missing product data!"); 
}

// ============================================
// RENDELÉS MUNKAMENET INICIALIZÁLÁSA
// ============================================
// Ha még nem létezik a rendelés munkamenet-változó, létrehozzuk egy üres tömbként
if (!isset($_SESSION['order'])) {
    $_SESSION['order'] = []; // 
}

// ============================================
// TERMÉK HOZZÁADÁSA A RENDELÉSHEZ
// ============================================
// Új termék hozzáadása a rendelési tömbhöz a következő adatokkal:
$_SESSION['order'][] = [
    'name' => $_POST['product_name'],   
    'price' => $_POST['product_price'], 
    'image' => $_POST['product_image']  
];

// ============================================
// ÁTIRÁNYÍTÁS A RENDELÉS ÖSSZEGZŐ OLDALRA
// ============================================
// Sikeres hozzáadás után átirányítjuk a felhasználót a rendelés összegző oldalra
header("Location: myorder.php"); // Átirányítás a myorder.php oldalra
exit; 
?>