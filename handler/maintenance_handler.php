<?php
session_start();

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$maintenanceFile = __DIR__ . '/../config/maintenance.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'toggle') {
        $data = json_decode(file_get_contents($maintenanceFile), true);
        $data['maintenance'] = !$data['maintenance'];
        
        file_put_contents($maintenanceFile, json_encode($data, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'success' => true,
            'maintenance' => $data['maintenance'],
            'message' => $data['maintenance'] ? 'Maintenance mode ON' : 'Maintenance mode OFF'
        ]);
        exit;
    }
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
?>
