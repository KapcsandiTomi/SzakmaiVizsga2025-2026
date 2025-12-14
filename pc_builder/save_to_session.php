<?php
require '../config.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['configuration'])) {
    $_SESSION['pc_configuration'] = $data['configuration'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No configuration data']);
}
?>

<!-- MEGNÉZZÜK, hogy létezik e a lista -->