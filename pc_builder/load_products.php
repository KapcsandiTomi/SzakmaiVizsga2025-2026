<?php
// ============================================================================
// KONFIGURÁCIÓ FÁJL ÉS MUNKAMENET BETÖLTÉSE
// ============================================================================
require '../config.php';
session_start();

// ============================================================================
// KATEGÓRIA ID LEKÉRÉSE A GET PARAMÉTERBŐL
// ============================================================================
$cat = (int)$_GET['cat'];

// ============================================================================
// TERMÉKEK LEKÉRDEZÉSE AZ ADATBÁZISBÓL A KATEGÓRIA ALAPJÁN
// ============================================================================
$stmt = $conn->prepare("SELECT id, name, price FROM pc_products WHERE category_id = ? ORDER BY price");
$stmt->bind_param("i", $cat);
$stmt->execute();
$res = $stmt->get_result();

// ============================================================================
// ÜRES KATEGÓRIA ELLENŐRZÉSE
// ============================================================================
if ($res->num_rows === 0) {
    echo '<div style="text-align: center; padding: 20px; color: #666;">No products available in this category.</div>';
} else {
    // ========================================================================
    // CSS STÍLUSOK GENERÁLÁSA A MODAL TERMÉKEKHEZ
    // ========================================================================
    echo '<style>
    .modal-product {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        margin-bottom: 12px;
        background-color: #ffffff;
        transition: transform 0.2s, box-shadow 0.2s;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .modal-product:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .modal-product > div:first-child {
        flex: 1;
    }
    .modal-product > div:last-child {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
    }
    .add-to-config {
        background: linear-gradient(135deg, #00796b 0%, #004d40 100%);
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 3px 10px rgba(0, 121, 107, 0.2);
        transition: all 0.3s ease;
    }
    .add-to-config:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 121, 107, 0.3);
        background: linear-gradient(135deg, #004d40 0%, #00796b 100%);
    }
    .add-to-config.added {
        background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
        animation: pulse 0.4s ease-in-out;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    </style>';

    // ========================================================================
    // TERMÉKEK MEGJELENÍTÉSE HTML FORMÁTUMBAN
    // ========================================================================
    while ($p = $res->fetch_assoc()) {
        $priceFormatted = number_format($p['price'], 2, '.', ',');
        echo '<div class="modal-product">';
        echo '<div>';
        echo '<strong>' . htmlspecialchars($p['name']) . '</strong><br>';
        echo '<small>ID: ' . $p['id'] . '</small>';
        echo '</div>';
        echo '<div>';
        echo '<div style="font-weight:bold; color:#00796b; margin-bottom:5px;">' . $priceFormatted . ' $</div>';
        echo '<button class="add-to-config" data-id="' . $p['id'] . '">';
        echo '<span class="btn-icon">➕</span>';
        echo '<span class="btn-text">Add to Build</span>';
        echo '</button>';
        echo '</div>';
        echo '</div>';
    }
}

// ============================================================================
// ADATBÁZIS KAPCSOLAT LEZÁRÁSA
// ============================================================================
$stmt->close();
?>

<!-- ==========================================================================
// JAVASCRIPT: GOMB INTERAKTIVITÁS - HOZZÁADÁS VISSZAJELZÉS
// ========================================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-config').forEach(button => {
        button.addEventListener('click', function() {
            const btn = this;
            // ====================================================================
            // GOMB ÁLLAPOT VÁLTÁS: "ADDED" STÍLUS ALKALMAZÁSA
            // ====================================================================
            btn.classList.add('added');
            btn.querySelector('.btn-icon').textContent = '✓';
            btn.querySelector('.btn-text').textContent = 'Added';
            
            // ====================================================================
            // VISSZAÁLLÍTÁS 2 MÁSODPERC MÚLVA
            // ====================================================================
            setTimeout(() => {
                btn.classList.remove('added');
                btn.querySelector('.btn-icon').textContent = '➕';
                btn.querySelector('.btn-text').textContent = 'Add to Build';
            }, 2000);
        });
    });
});
</script>