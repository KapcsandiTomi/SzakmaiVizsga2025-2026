<?php
if (!isset($categories)) {
    $categories = [];
}
if (!isset($products)) {
    $products = [];
}
if (!isset($edit_mode)) {
    $edit_mode = false;
    $edit_category_id = 0;
    $edit_category_name = '';
}
if (!isset($edit_product_id)) {
    $edit_product_id = 0;
}
if (!isset($edit_product_data)) {
    $edit_product_data = null;
}
?>
<?php
$headerPath = __DIR__ . '/../templates/header.php';
$navbarPath = __DIR__ . '/../templates/navbar.php';
$footerPath = __DIR__ . '/../templates/footer.php';

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
        <h2><i class="fas fa-folder"></i> PC Categories Management</h2>
        
        <?php if ($edit_mode): ?>
            <form method="POST" action="index.php?page=pc&action=update_category" class="mb-4">
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
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Category
                            </button>
                            <a href="index.php?page=pc" class="btn btn-danger">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <form method="POST" action="index.php?page=pc&action=add_category" class="mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label">New Category Name</label>
                            <input type="text" name="category_name" class="form-control" 
                                   required placeholder="e.g., Processor, Graphics Card, etc.">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        <h4><i class="fas fa-list"></i> Existing Categories</h4>
        
        <?php if (!empty($categories)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?page=pc&action=edit_category&id=<?php echo $category['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?page=pc&action=delete_category&id=<?php echo $category['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
    <div class="admin-card">
        <h2><i class="fas fa-microchip"></i> PC Products Management</h2>
        
        <?php if ($edit_product_id > 0 && $edit_product_data): ?>
            <form method="POST" action="index.php?page=pc&action=update_product" enctype="multipart/form-data">
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
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($edit_product_data['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
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
                        <?php if (!empty($edit_product_data['image']) && file_exists('../../' . $edit_product_data['image'])): ?>
                            <div class="form-group">
                                <label class="form-label">Current Image</label>
                                <div>
                                    <img src="../../<?php echo htmlspecialchars($edit_product_data['image']); ?>" 
                                         alt="Current Product Image" class="current-image">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="action-buttons mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="index.php?page=pc" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        <?php else: ?>
            <form method="POST" action="index.php?page=pc&action=add_product" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" 
                                   required placeholder="e.g., NVIDIA RTX 4090 24GB">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="product_price" class="form-control" step="0.01" min="0"
                                   required placeholder="e.g., 1499.99">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
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
        
        <!-- PRODUCTS LIST -->
        <h4 class="mt-5"><i class="fas fa-list"></i> Existing Products</h4>
        
        <?php if (!empty($products)): ?>
            <div class="table-responsive">
                <table class="admin-table">
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
                        <?php foreach ($products as $product): 
                            $price = number_format($product['price'], 2);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td>
                                <?php if (!empty($product['image']) && file_exists('../../' . $product['image'])): ?>
                                    <img src="../../<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="Product Image" class="product-image">
                                <?php else: ?>
                                    <div class="text-muted">No image</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>
                                <span class="badge badge-primary"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                            </td>
                            <td>
                                <strong>$<?php echo $price; ?></strong>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?page=pc&action=edit_product&id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?page=pc&action=delete_product&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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

<?php require_once $footerPath; ?>