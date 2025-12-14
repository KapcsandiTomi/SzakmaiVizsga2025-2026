<?php
require '../config.php';
session_start();

// Session ID lekérése
$sid = session_id();

// Konfiguráció termékeinek lekérdezése
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

$total = 0;
if ($res->num_rows === 0) {
    echo '<div style="text-align: center; padding: 20px; color: #666;">No products in configuration yet.</div>';
} else {
    // Termékek megjelenítése
    while($row = $res->fetch_assoc()) {
        $priceFormatted = number_format($row['price'], 2, '.', ',');
        $total += $row['price'];
        echo '<div class="product">';
        echo '<div>';
        echo '<span class="product-name">' . htmlspecialchars($row['name']) . '</span><br>';
        echo '<span class="product-price" style="color: #00796b; font-weight: bold;">' . $priceFormatted . ' EUR</span>';
        echo '</div>';
        echo '<button class="delete-btn" onclick="removeFromConfig(' . $row['item_id'] . ')">Delete</button>';
        echo '</div>';
    }
}

$q->close();
?>