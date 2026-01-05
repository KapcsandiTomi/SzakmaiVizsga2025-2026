<?php
session_start();
require_once '../config.php';

// ====================
// JOGOSULTSÁG ELLENŐRZÉS
// ====================
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../index.php");
    exit();
}

// ====================
// VÁLTOZÓK
// ====================
$message = '';
$error = '';
$success = '';
$edit_mode = false;
$edit_category_id = 0;
$edit_category_name = '';
$edit_product_id = 0;
$edit_product_data = null;

// ====================
// KATEGÓRIA KEZELÉS
// ====================

//Új kategória hozzáadása
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    
    if (empty($category_name)) {
        $error = "Category name is required!";
    } else {
        $stmt = $conn->prepare("INSERT INTO pc_categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        
        if ($stmt->execute()) {
            $success = "✅ Category added successfully!";
        } else {
            $error = "❌ Error adding category: " . $stmt->error;
        }
        $stmt->close();
    }
}

//Kategória törlése
if (isset($_GET['delete_category']) && is_numeric($_GET['delete_category'])) {
    $cat_id = intval($_GET['delete_category']);
    
    // Ellenőrizzük, hogy vannak-e termékek a kategóriában
    $check = $conn->query("SELECT COUNT(*) as count FROM pc_products WHERE category_id = $cat_id");
    $row = $check->fetch_assoc();
    
    if ($row['count'] > 0) {
        $error = "❌ Cannot delete category! There are products in this category. Delete the products first.";
    } else {
        if ($conn->query("DELETE FROM pc_categories WHERE id = $cat_id")) {
            $success = "✅ Category deleted successfully!";
        } else {
            $error = "❌ Error deleting category: " . $conn->error;
        }
    }
}

//Kategória szerkesztése előkészítése
if (isset($_GET['edit_category']) && is_numeric($_GET['edit_category'])) {
    $cat_id = intval($_GET['edit_category']);
    $result = $conn->query("SELECT * FROM pc_categories WHERE id = $cat_id");
    
    if ($result && $row = $result->fetch_assoc()) {
        $edit_mode = true;
        $edit_category_id = $row['id'];
        $edit_category_name = $row['name'];
    }
}

//Kategória mentése szerkesztés után
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $cat_id = intval($_POST['category_id']);
    $category_name = trim($_POST['category_name']);
    
    if (empty($category_name)) {
        $error = "Category name is required!";
    } else {
        $stmt = $conn->prepare("UPDATE pc_categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $category_name, $cat_id);
        
        if ($stmt->execute()) {
            $success = "✅ Category updated successfully!";
            $edit_mode = false;
            $edit_category_id = 0;
            $edit_category_name = '';
        } else {
            $error = "❌ Error updating category: " . $stmt->error;
        }
        $stmt->close();
    }
}

//Kategória szerkesztésének megszakítása
if (isset($_GET['cancel_edit'])) {
    $edit_mode = false;
    $edit_category_id = 0;
    $edit_category_name = '';
}

// ====================
// TERMÉK KEZELÉS
// ====================

//Új termék hozzáadása
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = trim($_POST['product_name']);
    $product_price = floatval($_POST['product_price']);
    $category_id = intval($_POST['category_id']);
    $product_image = '';
    
    // Kép feltöltés kezelése
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // MAX 5MB
        
        if (in_array($_FILES['product_image']['type'], $allowed_types)) {
            if ($_FILES['product_image']['size'] <= $max_size) {
                $upload_dir = '../uploads/pc_products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                $filename = 'pc_' . time() . '_' . uniqid() . '.' . $file_extension;
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
                    $product_image = 'uploads/pc_products/' . $filename;
                } else {
                    $error = "❌ Error uploading image!";
                }
            } else {
                $error = "❌ Image size too large! Maximum 5MB allowed.";
            }
        } else {
            $error = "❌ Invalid image type! Only JPG, PNG, GIF, WEBP allowed.";
        }
    }
    
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO pc_products (name, price, image, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $product_name, $product_price, $product_image, $category_id);
        
        if ($stmt->execute()) {
            $success = "✅ Product added successfully!";
            // Űrlap reset
            $_POST = array();
        } else {
            $error = "❌ Error adding product: " . $stmt->error;
        }
        $stmt->close();
    }
}

//Termék törlése
if (isset($_GET['delete_product']) && is_numeric($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    
    // Először lekérjük a kép elérési útját
    $result = $conn->query("SELECT image FROM pc_products WHERE id = $product_id");
    if ($result && $row = $result->fetch_assoc()) {
        // Töröljük a képet, ha van
        if (!empty($row['image']) && file_exists('../' . $row['image'])) {
            unlink('../' . $row['image']);
        }
    }
    
    if ($conn->query("DELETE FROM pc_products WHERE id = $product_id")) {
        $success = "✅ Product deleted successfully!";
    } else {
        $error = "❌ Error deleting product: " . $conn->error;
    }
}

//Termék szerkesztése előkészítése
if (isset($_GET['edit_product']) && is_numeric($_GET['edit_product'])) {
    $product_id = intval($_GET['edit_product']);
    $result = $conn->query("SELECT * FROM pc_products WHERE id = $product_id");
    
    if ($result && $row = $result->fetch_assoc()) {
        $edit_product_id = $row['id'];
        $edit_product_data = $row;
    }
}

//Termék mentése szerkesztés után
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = trim($_POST['product_name']);
    $product_price = floatval($_POST['product_price']);
    $category_id = intval($_POST['category_id']);
    $current_image = $_POST['current_image'] ?? '';
    $product_image = $current_image;
    
    // Új kép feltöltése, ha van
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024;
        
        if (in_array($_FILES['product_image']['type'], $allowed_types)) {
            if ($_FILES['product_image']['size'] <= $max_size) {
                $upload_dir = '../uploads/pc_products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Régi kép törlése, ha van
                if (!empty($current_image) && file_exists('../' . $current_image)) {
                    unlink('../' . $current_image);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                $filename = 'pc_' . time() . '_' . uniqid() . '.' . $file_extension;
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
                    $product_image = 'uploads/pc_products/' . $filename;
                }
            }
        }
    }
    
    $stmt = $conn->prepare("UPDATE pc_products SET name = ?, price = ?, image = ?, category_id = ? WHERE id = ?");
    $stmt->bind_param("sdsii", $product_name, $product_price, $product_image, $category_id, $product_id);
    
    if ($stmt->execute()) {
        $success = "✅ Product updated successfully!";
        $edit_product_id = 0;
        $edit_product_data = null;
    } else {
        $error = "❌ Error updating product: " . $stmt->error;
    }
    $stmt->close();
}

// 5. Termék szerkesztésének megszakítása
if (isset($_GET['cancel_product_edit'])) {
    $edit_product_id = 0;
    $edit_product_data = null;
}

// ====================
// ADATOK BETÖLTÉSE
// ====================

// Kategóriák betöltése
$categories_result = $conn->query("SELECT * FROM pc_categories ORDER BY id ASC");

// Termékek betöltése
$products_result = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM pc_products p 
    LEFT JOIN pc_categories c ON p.category_id = c.id 
    ORDER BY p.category_id, p.name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - PC Configurator Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../letoles.jpg" type="image/png">
    <style>
        :root {
            --primary: #4facfe;
            --primary-dark: #2a8bf2;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .admin-navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .admin-navbar h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .admin-container {
            max-width: 1600px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .admin-card {
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
            margin-bottom: 30px;
            padding: 25px;
            border: none;
            overflow: hidden;
        }
        
        .admin-card h2 {
            margin-bottom: 25px;
            color: var(--primary-dark);
            padding-bottom: 15px;
            border-bottom: 3px solid #f0f0f0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid var(--success);
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid var(--danger);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0f7fa;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            outline: none;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-warning {
            background: var(--warning);
            color: #212529;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85em;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border: none;
        }
        
        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .table tbody tr:hover {
            background: #f8faff;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e0f7fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3em;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .current-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e0f7fa;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 0 15px;
            }
            
            .admin-card {
                padding: 20px;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<!-- ==================== -->
<!-- NAVBAR -->
<!-- ==================== -->
<div class="admin-navbar">
    <h1><i class="fas fa-cogs"></i> PC Configurator Admin</h1>
    <div class="nav-links">
        <a href="../admin/admin.php"><i class="fas fa-arrow-left"></i> Back to Main Admin</a>
        <a href="../products.php"><i class="fas fa-store"></i> Products</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- ==================== -->
<!-- FŐ TARTALOM -->
<!-- ==================== -->
<div class="admin-container">
    
    <!-- ==================== -->
    <!-- ALERTS -->
    <!-- ==================== -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- ==================== -->
    <!-- KATEGÓRIA KEZELÉS -->
    <!-- ==================== -->
    <div class="admin-card">
        <h2><i class="fas fa-folder"></i> PC Categories Management</h2>
        
        <?php if ($edit_mode): ?>
            <!-- KATEGÓRIA SZERKESZTÉS -->
            <form method="POST" class="mb-4">
                <input type="hidden" name="category_id" value="<?php echo $edit_category_id; ?>">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($edit_category_name); ?>" 
                                   required placeholder="Enter category name">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="action-buttons">
                            <button type="submit" name="update_category" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Category
                            </button>
                            <a href="?cancel_edit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <!-- ÚJ KATEGÓRIA HOZZÁADÁSA -->
            <form method="POST" class="mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label">New Category Name</label>
                            <input type="text" name="category_name" class="form-control" 
                                   required placeholder="e.g., Processor, Graphics Card, etc.">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" name="add_category" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        
        <!-- KATEGÓRIÁK LISTÁJA -->
        <h4><i class="fas fa-list"></i> Existing Categories</h4>
        
        <?php if ($categories_result && $categories_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($category = $categories_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_category=<?php echo $category['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete_category=<?php echo $category['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h4>No Categories Found</h4>
                <p>Add your first PC component category using the form above.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ==================== -->
    <!-- TERMÉK KEZELÉS -->
    <!-- ==================== -->
    <div class="admin-card">
        <h2><i class="fas fa-microchip"></i> PC Products Management</h2>
        
        <?php if ($edit_product_id > 0 && $edit_product_data): ?>
            <!-- TERMÉK SZERKESZTÉS -->
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $edit_product_id; ?>">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($edit_product_data['image'] ?? ''); ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($edit_product_data['name']); ?>" 
                                   required placeholder="e.g., NVIDIA RTX 4090 24GB">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="product_price" class="form-control" step="0.01" min="0"
                                   value="<?php echo htmlspecialchars($edit_product_data['price']); ?>" 
                                   required placeholder="e.g., 1499.99">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php 
                                $cat_result = $conn->query("SELECT * FROM pc_categories ORDER BY name");
                                while ($cat = $cat_result->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($edit_product_data['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image. Max 5MB.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($edit_product_data['image']) && file_exists('../' . $edit_product_data['image'])): ?>
                            <div class="form-group">
                                <label class="form-label">Current Image</label>
                                <div>
                                    <img src="../<?php echo htmlspecialchars($edit_product_data['image']); ?>" 
                                         alt="Current Product Image" class="current-image">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="action-buttons mt-3">
                    <button type="submit" name="update_product" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="?cancel_product_edit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        <?php else: ?>
            <!-- ÚJ TERMÉK HOZZÁADÁSA -->
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" 
                                   value="<?php echo isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : ''; ?>" 
                                   required placeholder="e.g., NVIDIA RTX 4090 24GB">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="product_price" class="form-control" step="0.01" min="0"
                                   value="<?php echo isset($_POST['product_price']) ? htmlspecialchars($_POST['product_price']) : ''; ?>" 
                                   required placeholder="e.g., 1499.99">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php 
                                $cat_result = $conn->query("SELECT * FROM pc_categories ORDER BY name");
                                while ($cat = $cat_result->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $cat['id']; ?>"
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="add_product" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Product Image (Optional)</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*">
                            <small class="text-muted">Optional. Max 5MB. Supported: JPG, PNG, GIF, WEBP</small>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        
        <!-- TERMÉKEK LISTÁJA -->
        <h4 class="mt-5"><i class="fas fa-list"></i> Existing Products</h4>
        
        <?php if ($products_result && $products_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price ($)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $products_result->fetch_assoc()): 
                            $price = number_format($product['price'], 2);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td>
                                <?php if (!empty($product['image']) && file_exists('../' . $product['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="Product Image" class="product-image">
                                <?php else: ?>
                                    <div class="text-muted">No image</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            </td>
                            <td>
                                <strong>$<?php echo $price; ?></strong>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit_product=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete_product=<?php echo $product['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-microchip"></i>
                <h4>No Products Found</h4>
                <p>Add your first PC component using the form above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==================== -->
<!-- JAVASCRIPT -->
<!-- ==================== -->
<script>
// ALERT-ek eltünnek 5mp mulva
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.style.display = 'none';
        }, 500);
    });
}, 5000);

//Az ÁR megadásának vlaidálása
document.querySelectorAll('input[name="product_price"]').forEach(input => {
    input.addEventListener('input', function() {
        let value = parseFloat(this.value);
        if (value < 0) {
            this.value = 0;
        }
    });
});

//IMAGE
const imageInput = document.querySelector('input[name="product_image"]');
if (imageInput) {
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                if (!preview) {
                    const div = document.createElement('div');
                    div.id = 'image-preview';
                    div.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; border-radius: 8px; margin-top: 10px;">`;
                    imageInput.parentNode.appendChild(div);
                } else {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; border-radius: 8px; margin-top: 10px;">`;
                }
            }
            reader.readAsDataURL(file);
        }
    });
}
</script>

</body>
</html>