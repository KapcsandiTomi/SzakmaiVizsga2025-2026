<?php
// ============================================================================
// KONFIGURÁCIÓ FÁJL ÉS MUNKAMENET BETÖLTÉSE
// ============================================================================
require '../config.php';
session_start();

// ============================================================================
// MUNKAMENET AZONOSÍTÓ LEKÉRÉSE
// ============================================================================
$sid = session_id();

// ============================================================================
// PC KONFIGURÁCIÓ TERMÉKEINEK LEKÉRDEZÉSE AZ ADATBÁZISBÓL
// ============================================================================
$q = $conn->prepare("
    SELECT i.id as item_id, p.name, p.price
    FROM pc_configuration_items i
    JOIN pc_products p ON p.id = i.product_id
    JOIN pc_configurations c ON c.id = i.configuration_id
    WHERE c.session_id = ?
    ORDER BY p.category_id
");
$q->bind_param("s", $sid);
$q->execute();
$res = $q->get_result();

// ============================================================================
// ÖSSZEG VÁLTOZÓ INICIALIZÁLÁSA
// ============================================================================
$total = 0;

// ============================================================================
// ÜRES KONFIGURÁCIÓ ELLENŐRZÉSE
// ============================================================================
if ($res->num_rows === 0) {
    echo '<div style="text-align: center; padding: 20px; color: #666;">No products in configuration yet.</div>';
} else {
    // ========================================================================
    // TERMÉKEK MEGJELENÍTÉSE HTML FORMÁTUMBAN
    // ========================================================================
    while($row = $res->fetch_assoc()) {
        // ====================================================================
        // ÁR FORMÁZÁSA ÉS ÖSSZEG SZÁMÍTÁSA
        // ====================================================================
        $priceFormatted = number_format($row['price'], 2, '.', ',');
        $total += $row['price'];
        
        // ====================================================================
        // TERMÉK KARTYA HTML GENERÁLÁSA
        // ====================================================================
        echo '<div class="product">';
        echo '<div>';
        echo '<span class="product-name">' . htmlspecialchars($row['name']) . '</span><br>';
        echo '<span class="product-price" style="color: #00796b; font-weight: bold;">' . $priceFormatted . ' EUR</span>';
        echo '</div>';
        echo '<button class="delete-btn" onclick="removeFromConfig(' . $row['item_id'] . ')">Delete</button>';
        echo '</div>';
    }
}

// ============================================================================
// ADATBÁZIS KAPCSOLAT LEZÁRÁSA
// ============================================================================
$q->close();
?>