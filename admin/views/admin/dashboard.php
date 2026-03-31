<?php
$headerPath = __DIR__ . '/../templates/header.php';
$navbarPath = __DIR__ . '/../templates/navbar.php';
$footerPath = __DIR__ . '/../templates/footer.php';

if (!file_exists($headerPath)) {
    die("Header template not found: " . $headerPath);
}
if (!file_exists($navbarPath)) {
    die("Navbar template not found: " . $navbarPath);
}
if (!file_exists($footerPath)) {
    die("Footer template not found: " . $footerPath);
}

require_once $headerPath;
require_once $navbarPath;
?>

<div class="admin-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-container">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-container">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <div class="admin-card">
        <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="admin-card text-center">
                    <h3><i class="fas fa-users"></i> Users</h3>
                    <p class="display-4"><?php echo is_array($users) ? count($users) : 0; ?></p>
                    <a href="index.php?page=users" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="admin-card text-center">
                    <h3><i class="fas fa-shopping-cart"></i> Orders</h3>
                    <p class="display-4"><?php echo is_array($orders) ? count($orders) : 0; ?></p>
                    <a href="index.php?page=orders" class="btn btn-primary">Manage Orders</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="admin-card text-center">
                    <h3><i class="fas fa-microchip"></i> PC Builder</h3>
                    <p class="display-4">-</p>
                    <a href="index.php?page=pc" class="btn btn-primary">PC Config Admin</a>
                </div>
            </div>
        </div>
    </div>
    <div class="admin-card">
        <h2><i class="fas fa-tools"></i> Maintenance Mode</h2>
        
        <?php
        $maintenanceFile = __DIR__ . '/../../../config/maintenance.json';
        $maintenanceData = json_decode(file_get_contents($maintenanceFile), true);
        $isMaintenanceOn = $maintenanceData['maintenance'] ?? false;
        ?>
        
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: <?php echo $isMaintenanceOn ? '#fecaca' : '#dcfce7'; ?>; border-radius: 10px; border-left: 5px solid <?php echo $isMaintenanceOn ? '#dc2626' : '#16a34a'; ?>;">
            <div>
                <h4 style="margin: 0 0 10px 0; color: <?php echo $isMaintenanceOn ? '#7f1d1d' : '#15803d'; ?>;">
                    <i class="fas <?php echo $isMaintenanceOn ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
                    Current Status: <strong><?php echo $isMaintenanceOn ? 'ON - Site Under Maintenance' : 'OFF - Site Operational'; ?></strong>
                </h4>
                <p style="margin: 0; color: <?php echo $isMaintenanceOn ? '#7f1d1d' : '#15803d'; ?>; font-size: 0.9rem;">
                    <?php echo $isMaintenanceOn ? 'Only admins can access. Users will see maintenance page.' : 'All users can access the site normally.'; ?>
                </p>
            </div>
            <button id="maintenanceToggle" class="btn <?php echo $isMaintenanceOn ? 'btn-danger' : 'btn-success'; ?>" style="min-width: 150px;">
                <i class="fas <?php echo $isMaintenanceOn ? 'fa-power-off' : 'fa-power-off'; ?>"></i> 
                <?php echo $isMaintenanceOn ? 'Turn OFF' : 'Turn ON'; ?>
            </button>
        </div>
    </div>

    <div class="admin-card">
        <h2><i class="fas fa-rocket"></i> Quick Actions</h2>
        
        <div class="row">
            <div class="col-md-3">
                <a href="index.php?page=users" class="btn btn-success btn-block btn-lg mb-3">
                    <i class="fas fa-user-plus"></i> Manage Users
                </a>
            </div>
            <div class="col-md-3">
                <a href="index.php?page=orders" class="btn btn-info btn-block btn-lg mb-3">
                    <i class="fas fa-edit"></i> Update Orders
                </a>
            </div>
            <div class="col-md-3">
                <a href="index.php?page=pc&action=add_category" class="btn btn-warning btn-block btn-lg mb-3">
                    <i class="fas fa-folder-plus"></i> Add Category
                </a>
            </div>
            <div class="col-md-3">
                <a href="index.php?page=pc&action=add_product" class="btn btn-primary btn-block btn-lg mb-3">
                    <i class="fas fa-microchip"></i> Add Product
                </a>
            </div>
        </div>
    </div>

</div>

<?php
require_once $footerPath;
?>