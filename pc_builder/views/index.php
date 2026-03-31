<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - PC Configurator</title>
    <link rel="stylesheet" href="/Szakmai/pc_builder/assets/css/style.css">
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
</head>
<body>

<div class="main-container">
    <a href="../products.php" class="back-button">← Return to Products</a>
    
    <div class="header">
        <h1 class="page-title">🎮 PC Configurator</h1>
        <p class="page-subtitle">Build your dream computer by selecting components from each category</p>
    </div>
    
    <div class="category-list">
        <h2 class="category-title">📦 Select Components</h2>
        
        <?php if (empty($categories)): ?>
            <div class="error-message">No categories available. Please check database connection.</div>
        <?php else: ?>
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item">
                        <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                        <button class="select-btn add" data-cat="<?php echo $category['id']; ?>">
                            SELECT +
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
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
    
    <div class="footer">
        <p>© <?php echo date('Y'); ?> Aqua Mini Shop | PC Configurator</p>
        <p>Need help? Contact: <strong>aquaminishop@gmail.com</strong></p>
    </div>
</div>

<script src="/Szakmai/pc_builder/assets/js/script.js"></script>

</body>
</html>
