<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$baseUrl = 'index.php'; 
?>

<div class="admin-navbar">
    <h1><i class="fas fa-cogs"></i> Admin Panel</h1>
    <div class="nav-links">
        <a href="../fooldal.php"><i class="fas fa-home"></i> Home</a>
        <a href="../products.php"><i class="fas fa-store"></i> Products</a>
        <a href="<?php echo $baseUrl; ?>?page=pc" class="<?php echo $currentPage == 'pc' ? 'active' : ''; ?>">
            <i class="fas fa-microchip"></i> PC Config Admin
        </a>
        <a href="<?php echo $baseUrl; ?>?page=dashboard" class="<?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="<?php echo $baseUrl; ?>?page=users" class="<?php echo $currentPage == 'users' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="<?php echo $baseUrl; ?>?page=orders" class="<?php echo $currentPage == 'orders' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>