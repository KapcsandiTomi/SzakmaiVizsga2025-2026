<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['is_admin'] != 1) {
    header("Location: /Szakmai/pages/fooldal.php");
    exit();
}

if (empty($_SESSION['admin_gate_passed'])) {
    header("Location: /Szakmai/pages/fooldal.php?admin_prompt=1");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

function loadController($controller) {
    $controllerFile = __DIR__ . '/controllers/' . $controller . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
    } else {
        die("Controller not found: " . $controller . " at " . $controllerFile);
    }
}

function loadModel($model) {
    $modelFile = __DIR__ . '/models/' . $model . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
    } else {
        die("Model not found: " . $model . " at " . $modelFile);
    }
}

loadModel('UserModel');
loadModel('OrderModel');
loadModel('PCModel');

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

try {
    switch ($page) {
        case 'users':
            loadController('UserController');
            $controller = new UserController($conn);
            
            switch ($action) {
                case 'delete':
                    $controller->delete($id);
                    break;
                case 'make_admin':
                    $controller->makeAdmin($id);
                    break;
                case 'remove_admin':
                    $controller->removeAdmin($id);
                    break;
                default:
                    $controller->index();
            }
            break;
            
        case 'orders':
            loadController('OrderController');
            $controller = new OrderController($conn);
            
            switch ($action) {
                case 'update_status':
                    $controller->updateStatus($_POST);
                    break;
                case 'delete':
                    $controller->delete($id);
                    break;
                default:
                    $controller->index();
            }
            break;
            
        case 'pc':
            loadController('PCController');
            $controller = new PCController($conn);
            
            switch ($action) {
                case 'add_category':
                    $controller->addCategory($_POST);
                    break;
                case 'edit_category':
                    $controller->editCategory($id);
                    break;
                case 'update_category':
                    $controller->updateCategory($_POST);
                    break;
                case 'delete_category':
                    $controller->deleteCategory($id);
                    break;
                case 'add_product':
                    $controller->addProduct($_POST, $_FILES);
                    break;
                case 'edit_product':
                    $controller->editProduct($id);
                    break;
                case 'update_product':
                    $controller->updateProduct($_POST, $_FILES);
                    break;
                case 'delete_product':
                    $controller->deleteProduct($id);
                    break;
                default:
                    $controller->index();
            }
            break;
            
        default:
            loadController('AdminController');
            $controller = new AdminController($conn);
            $controller->index();
            break;
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 8px;'>";
    echo "<h3>Something happened:</h3>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Sort:</strong> " . htmlspecialchars($e->getLine()) . "</p>";
    echo "<a href='index.php' style='color: #721c24; text-decoration: underline;'>Back to the main page!</a>";
    echo "</div>";
}

if (isset($conn) && $conn instanceof PDO) {
    $conn = null; 
}
?>
