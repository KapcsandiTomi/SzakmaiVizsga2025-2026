<?php 
require '../config.php'; 

// Session indítása
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//ADATBÁZIS KAPCSOLAT
if ($conn->connect_error) {
    die("<div style='text-align:center; padding:50px; color:#e53935;'><h3>Database Connection Error</h3><p>Please try again later.</p></div>");
}

// SQL, DDOS ELLENI VÉDELEM
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Kategóriák lekérdezése, hogy megvan-e?
$categories = [];
$category_query = "SELECT id, name FROM pc_categories ORDER BY id";
$category_result = $conn->query($category_query);

if (!$category_result) {
    $error = "Unable to load categories. Please try again later.";
} else {
    if ($category_result->num_rows > 0) {
        while($category = $category_result->fetch_assoc()) {
            $categories[] = [
                'id' => (int)$category['id'],
                'name' => clean_input($category['name'])
            ];
        }
    }
    $category_result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Aqua Mini Shop - PC Configurator</title>
    <link rel="icon" href="../letoles.jpg" type="image/png">
    <style>
        /* Reset és alap stílusok */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            color: #004d40;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        /* Fő konténer */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 77, 64, 0.1);
            overflow: hidden;
            padding: 30px;
        }
        
        /* Fejléc */
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #80cbc4;
        }
        
        .page-title {
            color: #00796b;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .page-subtitle {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 30px;
        }
        
        /* Vissza gomb */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #00796b;
            font-weight: 600;
            padding: 12px 25px;
            background: #f8fdff;
            border: 2px solid #80cbc4;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }
        
        .back-button:hover {
            background: #00796b;
            color: white;
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 121, 107, 0.2);
        }
        
        /* Kategória lista */
        .category-list {
            margin-bottom: 40px;
        }
        
        .category-title {
            color: #004d40;
            font-size: 1.8em;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .category-item {
            background: white;
            border: 2px solid #80cbc4;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .category-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 121, 107, 0.15);
            border-color: #00796b;
            background: #f8fdff;
        }
        
        .category-name {
            font-size: 1.2em;
            font-weight: 600;
            color: #004d40;
        }
        
        .select-btn {
            background: #00bcd4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .select-btn:hover {
            background: #0097a7;
            transform: scale(1.05);
        }
        
        /* Konfigurációs rész */
        .config-section {
            background: #f8fdff;
            border-radius: 10px;
            padding: 30px;
            margin: 40px 0;
            border: 2px solid #80cbc4;
        }
        
        .config-title {
            color: #004d40;
            font-size: 1.8em;
            margin-bottom: 25px;
            text-align: center;
        }
        
        #config {
            min-height: 200px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border: 1px dashed #80cbc4;
            margin-bottom: 20px;
        }
        
        .empty-config {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .empty-config-icon {
            font-size: 3em;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .config-item {
            background: white;
            border: 1px solid #e0f7fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .config-item:hover {
            background: #f1f8e9;
            border-color: #00bcd4;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            color: #004d40;
            margin-bottom: 5px;
        }
        
        .item-price {
            color: #00796b;
            font-weight: bold;
        }
        
        .delete-btn {
            background: #e53935;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .delete-btn:hover {
            background: #b71c1c;
            transform: scale(1.05);
        }
        
        /* Total price display */
        .total-price {
            text-align: right;
            font-size: 1.3em;
            font-weight: bold;
            color: #00796b;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #80cbc4;
        }
        
        /* Checkout gomb */
        .checkout-section {
            text-align: center;
            margin-top: 30px;
        }
        
        #checkout {
            display: none;
            background: linear-gradient(135deg, #00796b 0%, #004d40 100%);
            color: white;
            border: none;
            padding: 18px 45px;
            font-size: 1.3em;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 121, 107, 0.3);
            margin: 0 auto;
        }
        
        #checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 121, 107, 0.4);
            background: linear-gradient(135deg, #004d40 0%, #00796b 100%);
        }
        
        /* Modal stílusok */
        #modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
        }
        
        #modal.active {
            display: flex;
        }
        
        #modal-content {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 800px;
            border-radius: 15px;
            position: relative;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 30px;
            cursor: pointer;
            color: #00796b;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            background: #f8fdff;
        }
        
        #close:hover {
            background: #00796b;
            color: white;
            transform: rotate(90deg);
        }
        
        #modal-body {
            margin-top: 20px;
        }
        
        /* Product cards in modal */
        .product-card {
            background: white;
            border: 2px solid #e0f7fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            border-color: #00bcd4;
            box-shadow: 0 5px 15px rgba(0, 188, 212, 0.1);
            transform: translateY(-2px);
        }
        
        .product-info {
            flex: 1;
        }
        
        .product-name {
            font-weight: 600;
            color: #004d40;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        
        .product-id {
            color: #666;
            font-size: 0.9em;
        }
        
        .product-price {
            font-weight: bold;
            color: #00796b;
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        
        .add-btn {
            background: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            background: #388e3c;
            transform: scale(1.05);
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0f7fa;
            color: #666;
            font-size: 0.9em;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }
            
            .page-title {
                font-size: 2em;
            }
            
            .category-grid {
                grid-template-columns: 1fr;
            }
            
            .category-item {
                padding: 15px;
            }
            
            #checkout {
                padding: 15px 30px;
                font-size: 1.1em;
            }
            
            #modal-content {
                padding: 20px;
                margin: 10px;
            }
        }
        
        /* Error message */
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #c62828;
            margin: 20px 0;
            text-align: center;
        }
        
        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #00796b;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Vissza gomb -->
    <a href="../products.php" class="back-button">
        ← Return to Products
    </a>
    
    <!-- Fejléc -->
    <div class="header">
        <h1 class="page-title">🎮 PC Configurator</h1>
        <p class="page-subtitle">Build your dream computer by selecting components from each category</p>
    </div>
    
    <!-- Hibaüzenet ha szükséges-->
    <?php if (isset($error)): ?>
        <div class="error-message">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <!-- Kategóriák -->
    <div class="category-list">
        <h2 class="category-title">📦 Select Components</h2>
        
        <?php if (empty($categories)): ?>
            <div class="error-message">
                No categories available. Please check database connection.
            </div>
        <?php else: ?>
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item">
                        <div class="category-name"><?php echo $category['name']; ?></div>
                        <button class="select-btn add" data-cat="<?php echo $category['id']; ?>">
                            SELECT +
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Konfiguráció -->
    <div class="config-section">
        <h2 class="config-title">🛠️ Your PC Configuration</h2>
        
        <div id="config">
            <div class="empty-config">
                <div class="empty-config-icon">⚙️</div>
                <h3>Start Building Your PC</h3>
                <p>No components selected yet.</p>
                <p>Choose components from the categories above to begin.</p>
            </div>
        </div>
        
        <div class="checkout-section">
            <button id="checkout">
                🛒 Proceed to Checkout & Pay
            </button>
        </div>
    </div>
    
    <!-- Modal ablak -->
    <div id="modal">
        <div id="modal-content">
            <span id="close">✖</span>
            <h3 style="color: #00796b; margin-bottom: 20px;">Select Product</h3>
            <div id="modal-body">
                <div style="text-align: center; padding: 40px;">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 15px; color: #666;">Loading products...</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>© <?php echo date('Y'); ?> Aqua Mini Shop | PC Configurator</p>
        <p>Need help? Contact: <strong>kapcsandi.tomi@gmail.com</strong></p>
    </div>
</div>

<script src="script.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('PC Configurator initialized');
    
    // Ellenőrizzük az elemeket
    const checkoutBtn = document.getElementById('checkout');
    const configContainer = document.getElementById('config');
    
    console.log('Checkout button:', checkoutBtn);
    console.log('Config container:', configContainer);
    
    // ESC billentyű a modal bezárásához
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('modal');
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            modal.classList.remove('active');
        }
    });
});
</script>
</body>
</html>