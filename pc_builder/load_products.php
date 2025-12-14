<?php
require '../config.php';
session_start();

// Kategória ID lekérése és validálása
$cat = (int)$_GET['cat'];

// Termékek lekérdezése az adott kategóriából
$stmt = $conn->prepare("SELECT id, name, price FROM pc_products WHERE category_id = ? ORDER BY price");
$stmt->bind_param("i", $cat);
$stmt->execute();
$res = $stmt->get_result();

// Ellenőrizzük, vannak-e termékek
if ($res->num_rows === 0) {
    echo '<div style="text-align: center; padding: 20px; color: #666;">No products available in this category.</div>';
} else {
    // Termékek listázása
    while ($p = $res->fetch_assoc()) {
        $priceFormatted = number_format($p['price'], 2, '.', ',');
        echo '<div class="modal-product">';
        echo '<div>';
        echo '<strong>' . htmlspecialchars($p['name']) . '</strong><br>';
        echo '<small>ID: ' . $p['id'] . '</small>';
        echo '</div>';
        echo '<div style="text-align: right;">';
        echo '<div style="font-weight: bold; color: #00796b; margin-bottom: 5px;">' . $priceFormatted . ' EUR</div>';
        echo '<button class="add-to-config" data-id="' . $p['id'] . '">Add to Configuration</button>';
        echo '</div>';
        echo '</div>';
    }
}
$stmt->close();
?>