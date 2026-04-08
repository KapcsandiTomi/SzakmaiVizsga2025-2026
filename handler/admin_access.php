<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$defaultReturn = '/Szakmai/pages/fooldal.php';
$returnTo = $_POST['return_to'] ?? $defaultReturn;

if (!is_string($returnTo) || strpos($returnTo, '/Szakmai/') !== 0) {
    $returnTo = $defaultReturn;
}

$returnSeparator = strpos($returnTo, '?') === false ? '?' : '&';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $defaultReturn);
    exit();
}

if (!isset($_SESSION['user_id'], $_SESSION['is_admin']) || (int) $_SESSION['is_admin'] !== 1) {
    unset($_SESSION['admin_gate_passed']);
    header('Location: ' . $returnTo . $returnSeparator . 'admin_error=denied&admin_prompt=1');
    exit();
}

$expectedPassword = 'G7$kP!9zQ@4Lm#Xv2R^aW8tB';
$enteredPassword = $_POST['admin_password'] ?? '';

if (is_string($enteredPassword) && hash_equals($expectedPassword, $enteredPassword)) {
    $_SESSION['admin_gate_passed'] = true;
    header('Location: /Szakmai/admin/index.php');
    exit();
}

unset($_SESSION['admin_gate_passed']);
header('Location: ' . $returnTo . $returnSeparator . 'admin_error=1&admin_prompt=1');
exit();
