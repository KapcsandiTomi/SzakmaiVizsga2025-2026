<?php
$maintenanceFile = __DIR__ . '/../config/maintenance.json';
if (file_exists($maintenanceFile)) {
    $maintenanceData = json_decode(file_get_contents($maintenanceFile), true);
    $isMaintenanceOn = $maintenanceData['maintenance'] ?? false;
    
    if ($isMaintenanceOn) {
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
        $isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['email']);
        
        if ($isLoggedIn && !$isAdmin) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            if ($currentPage !== 'maintenance.php' && $currentPage !== 'logout.php') {
                header('Location: /Szakmai/pages/maintenance.php');
                exit;
            }
        }
    }
}
?>
