<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/controllers/',
        __DIR__ . '/models/',
        __DIR__ . '/config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    error_log("Class not found: " . $class);
});

try {
    require_once __DIR__ . '/config/database.php';
} catch (Exception $e) {
    die("Database initialization failed: " . $e->getMessage());
}

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/Szakmai/pc_builder';

if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

if ($request_uri === '') {
    $request_uri = '/';
}

$path = parse_url($request_uri, PHP_URL_PATH);
$query = parse_url($request_uri, PHP_URL_QUERY);

$query_params = [];
if ($query !== null && $query !== '') {
    parse_str($query, $query_params);
}

$path = rtrim($path, '/');

if (isset($_GET['debug'])) {
    echo "<pre>";
    echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
    echo "Base Path: " . $base_path . "\n";
    echo "Path: " . $path . "\n";
    echo "Query: " . ($query ?? 'null') . "\n";
    echo "Query Params: ";
    print_r($query_params);
    echo "</pre>";
}

try {
    if ($path === '' || $path === '/') {
        $controller = new CategoryController();
        $controller->index();
        exit;
    }

    if ($path === '/products') {
        $controller = new ProductController();
        $controller->getByCategory();
        exit;
    }

    if ($path === '/config/add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConfigController();
        $controller->addItem();
        exit;
    }

    if ($path === '/config/get') {
        $controller = new ConfigController();
        $controller->getItems();
        exit;
    }

    if ($path === '/config/remove' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new ConfigController();
        $controller->removeItem();
        exit;
    }

    if ($path === '/checkout') {
        $controller = new CheckoutController();
        $controller->index();
        exit;
    }

    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Endpoint not found: ' . $path]);
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>