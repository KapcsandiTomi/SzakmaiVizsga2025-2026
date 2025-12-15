<?php
require '../config.php';
session_start();

$sid = session_id();
$q = $conn->prepare("
    SELECT i.id as item_id, p.name, p.price
    FROM pc_configuration_items i
    JOIN pc_products p ON p.id = i.product_id
    JOIN pc_configurations c ON c.id = i.configuration_id
    WHERE c.session_id = ?
    ORDER BY p.category_id
");

if (!$q) {
    // Ha hiba van, JSON hibát küldünk
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'count' => 0]);
    exit();
}

$q->bind_param("s", $sid);
$q->execute();
$res = $q->get_result();

$items = [];
$total = 0;
$count = 0;

while($row = $res->fetch_assoc()) {
    $items[] = [
        'item_id' => $row['item_id'],
        'name' => $row['name'],
        'price' => $row['price']
    ];
    $total += $row['price'];
    $count++;
}

$q->close();

// JSON válasz
header('Content-Type: application/json');
echo json_encode([
    'items' => $items,
    'total' => $total,
    'count' => $count,
    'success' => true
]);
?>