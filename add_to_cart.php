<?php
session_start();

if (!isset($_POST['product_name'], $_POST['product_price'], $_POST['product_image'])) {
    die('Missing product data!');
}

if (!isset($_SESSION['order'])) {
    $_SESSION['order'] = [];
}

$_SESSION['order'][] = [
    'name' => $_POST['product_name'],
    'price' => $_POST['product_price'],
    'image' => $_POST['product_image']
];

header('Location: /Szakmai/pages/myorder.php');
exit();