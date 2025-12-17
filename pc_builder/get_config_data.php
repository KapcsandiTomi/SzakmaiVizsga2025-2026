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
// ADATBÁZIS LEKÉRDEZÉS ELŐKÉSZÍTÉSE: PC KONFIGURÁCIÓ ELEMEK BETÖLTÉSE
// ============================================================================
$q = $conn->prepare("
    SELECT i.id as item_id, p.name, p.price
    FROM pc_configuration_items i
    JOIN pc_products p ON p.id = i.product_id
    JOIN pc_configurations c ON c.id = i.configuration_id
    WHERE c.session_id = ?
    ORDER BY p.category_id
");

// ============================================================================
// ADATBÁZIS LEKÉRDEZÉS SIKERTELENSÉGÉNEK KEZELÉSE
// ============================================================================
if (!$q) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'count' => 0]);
    exit();
}

// ============================================================================
// LEKÉRDEZÉS VÉGREHAJTÁSA ÉS EREDMÉNYEK FELDOLGOZÁSA
// ============================================================================
$q->bind_param("s", $sid);
$q->execute();
$res = $q->get_result();

// ============================================================================
// VÁLTOZÓK INICIALIZÁLÁSA
// ============================================================================
$items = [];
$total = 0;
$count = 0;

// ============================================================================
// EREDMÉNYEK FELDOLGOZÁSA ÉS TÖMB SZERVEZÉSE
// ============================================================================
while($row = $res->fetch_assoc()) {
    $items[] = [
        'item_id' => $row['item_id'],
        'name' => $row['name'],
        'price' => $row['price']
    ];
    $total += $row['price'];
    $count++;
}

// ============================================================================
// ADATBÁZIS KAPCSOLAT LEZÁRÁSA
// ============================================================================
$q->close();

// ============================================================================
// JSON VÁLASZ ELŐKÉSZÍTÉSE ÉS KÜLDÉSE
// ============================================================================
header('Content-Type: application/json');
echo json_encode([
    'items' => $items,
    'total' => $total,
    'count' => $count,
    'success' => true
]);
?>