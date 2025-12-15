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
    //CSS stílusok
    echo '<style>
    .add-to-config {
        background: linear-gradient(135deg, #00796b 0%, #004d40 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 3px 10px rgba(0, 121, 107, 0.2);
        position: relative;
        overflow: hidden;
        min-width: 160px;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .add-to-config:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 121, 107, 0.3);
        background: linear-gradient(135deg, #004d40 0%, #00796b 100%);
    }
    
    .add-to-config:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(0, 121, 107, 0.2);
    }
    
    .add-to-config:disabled {
        background: #cccccc;
        color: #666666;
        cursor: not-allowed;
        box-shadow: none;
        transform: none !important;
    }
    
    .add-to-config:disabled:hover {
        background: #cccccc;
        box-shadow: none;
    }
    
    .add-to-config .btn-icon {
        font-size: 16px;
        line-height: 1;
    }
    
    .add-to-config .btn-text {
        font-size: 14px;
    }
    
    /* Animáció hozzáadásakor */
    .add-to-config.added {
        background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
        animation: pulse 0.5s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .add-to-config.added .btn-icon {
        animation: checkmark 0.5s ease-in-out;
    }
    
    @keyframes checkmark {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    </style>';
    
    // Termékek listázása
    while ($p = $res->fetch_assoc()) {
        $priceFormatted = number_format($p['price'], 2, '.', ',');
        echo '<div class="modal-product">';
        echo '<div>';
        echo '<strong>' . htmlspecialchars($p['name']) . '</strong><br>';
        echo '<small>ID: ' . $p['id'] . '</small>';
        echo '</div>';
        echo '<div style="text-align: right;">';
        echo '<div style="font-weight: bold; color: #00796b; margin-bottom: 8px;">' . $priceFormatted . ' EUR</div>';
        echo '<button class="add-to-config" data-id="' . $p['id'] . '">';
        echo '<span class="btn-icon">➕</span>';
        echo '<span class="btn-text">Add to Build</span>';
        echo '</button>';
        echo '</div>';
        echo '</div>';
    }
}
$stmt->close();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Minden "Add to Build" gombhoz eseménykezelő
    document.querySelectorAll('.add-to-config').forEach(button => {
        button.addEventListener('click', function() {
            const button = this;
            const productId = button.getAttribute('data-id');
            
            //hozzáadás animáció
            button.classList.add('added');
            button.querySelector('.btn-icon').textContent = '✓';
            button.querySelector('.btn-text').textContent = 'Added';
            
            // 2 másodperc után visszaállítjuk
            setTimeout(() => {
                button.classList.remove('added');
                button.querySelector('.btn-icon').textContent = '➕';
                button.querySelector('.btn-text').textContent = 'Add to Build';
            }, 2000);
        });
    });
    
    // Konfiguráció frissítése
    function updateConfiguration() {
        console.log('Configuration should be updated');
    }
});
</script>