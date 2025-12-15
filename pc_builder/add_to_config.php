<?php
require '../config.php';
session_start();

// JSON adat feldolgozása
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing product_id']);
    exit;
}

$pid = (int)$data['product_id'];
$sid = session_id();

// 1. Konfiguráció ID lekérdezése
$stmt = $conn->prepare("SELECT id FROM pc_configurations WHERE session_id = ?");
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->bind_result($cid);
$stmt->fetch();
$stmt->close();

// 2. Ha nincs, létrehozzuk
if (!$cid) {
    $stmt = $conn->prepare("INSERT INTO pc_configurations (session_id) VALUES (?)");
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    $cid = $stmt->insert_id;
    $stmt->close();
}

// 3. Termék kategóriájának lekérdezése
$stmt = $conn->prepare("SELECT category_id FROM pc_products WHERE id = ?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$stmt->bind_result($cat_id);
if (!$stmt->fetch()) {
    $stmt->close();
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}
$stmt->close();

// 4. Régi termék törlése ugyanabból a kategóriából
$stmt = $conn->prepare("DELETE FROM pc_configuration_items WHERE configuration_id = ? AND category_id = ?");
$stmt->bind_param("ii", $cid, $cat_id);
$stmt->execute();
$stmt->close();

// 5. Új termék hozzáadása
$stmt = $conn->prepare("INSERT INTO pc_configuration_items (configuration_id, category_id, product_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $cid, $cat_id, $pid);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
?>