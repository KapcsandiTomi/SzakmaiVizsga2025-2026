<?php
class PCController {
    private $conn;
    private $pcModel;
    private $uploadDir = '../uploads/pc_products/';
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->pcModel = new PCModel($connection);
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    
    public function index() {
        $categories = $this->pcModel->getAllCategories();
        $products = $this->pcModel->getAllProducts();
        require_once __DIR__ . '/../views/pc/dashboard.php';
    }
    
    public function addCategory($postData) {
        session_start();
        
        if (isset($postData['category_name'])) {
            $name = trim($postData['category_name']);
            
            if (empty($name)) {
                $_SESSION['error'] = "Category name is required!";
            } else {
                if ($this->pcModel->addCategory($name)) {
                    $_SESSION['success'] = "Category added successfully!";
                } else {
                    $_SESSION['error'] = "Error adding category!";
                }
            }
        }
        
        header("Location: index.php?page=pc");
        exit();
    }
    
    public function editCategory($id) {
        $category = $this->pcModel->getCategoryById($id);
        $categories = $this->pcModel->getAllCategories();
        $products = $this->pcModel->getAllProducts();
        
        require_once __DIR__ . '/../views/pc/dashboard.php';
    }
    
    public function updateCategory($postData) {
        session_start();
        
        if (isset($postData['category_id'], $postData['category_name'])) {
            $id = intval($postData['category_id']);
            $name = trim($postData['category_name']);
            
            if (empty($name)) {
                $_SESSION['error'] = "Category name is required!";
            } else {
                if ($this->pcModel->updateCategory($id, $name)) {
                    $_SESSION['success'] = "Category updated successfully!";
                } else {
                    $_SESSION['error'] = "Error updating category!";
                }
            }
        }
        
        header("Location: index.php?page=pc");
        exit();
    }
    
    public function deleteCategory($id) {
        session_start();
        
        if ($this->pcModel->hasProductsInCategory($id)) {
            $_SESSION['error'] = "Cannot delete category! There are products in this category.";
        } else {
            if ($this->pcModel->deleteCategory($id)) {
                $_SESSION['success'] = "Category deleted successfully!";
            } else {
                $_SESSION['error'] = "Error deleting category!";
            }
        }
        
        header("Location: index.php?page=pc");
        exit();
    }
    
    public function addProduct($postData, $files) {
        session_start();
        
        if (isset($postData['product_name'], $postData['product_price'], $postData['category_id'])) {
            $name = trim($postData['product_name']);
            $price = floatval($postData['product_price']);
            $category_id = intval($postData['category_id']);
            $image = '';
            
            if (isset($files['product_image']) && $files['product_image']['error'] == 0) {
                $image = $this->handleImageUpload($files['product_image']);
                if ($image === false) {
                    header("Location: index.php?page=pc");
                    exit();
                }
            }
            
            if ($this->pcModel->addProduct($name, $price, $image, $category_id)) {
                $_SESSION['success'] = "Product added successfully!";
            } else {
                $_SESSION['error'] = "Error adding product!";
            }
        }
        
        header("Location: index.php?page=pc");
        exit();
    }
    
    public function editProduct($id) {
        $product = $this->pcModel->getProductById($id);
        $categories = $this->pcModel->getAllCategories();
        $products = $this->pcModel->getAllProducts();
        
        require_once __DIR__ . '/../views/pc/dashboard.php';
    }
    
    public function updateProduct($postData, $files) {
        session_start();
        
        if (isset($postData['product_id'], $postData['product_name'], $postData['product_price'], $postData['category_id'])) {
            $id = intval($postData['product_id']);
            $name = trim($postData['product_name']);
            $price = floatval($postData['product_price']);
            $category_id = intval($postData['category_id']);
            $current_image = $postData['current_image'] ?? '';
            $image = $current_image;
            
            
            if (isset($files['product_image']) && $files['product_image']['error'] == 0) {
                $new_image = $this->handleImageUpload($files['product_image']);
                if ($new_image !== false) {
                    if (!empty($current_image) && file_exists('../' . $current_image)) {
                        unlink('../' . $current_image);
                    }
                    $image = $new_image;
                }
            }
            
            if ($this->pcModel->updateProduct($id, $name, $price, $image, $category_id)) {
                $_SESSION['success'] = "Product updated successfully!";
            } else {
                $_SESSION['error'] = "Error updating product!";
            }
        }
        
        header("Location: index.php?page=pc");
        exit();
    }
    
    public function deleteProduct($id) {
        session_start();
        
        $product = $this->pcModel->getProductById($id);
        if ($product && !empty($product['image']) && file_exists('../' . $product['image'])) {
            unlink('../' . $product['image']);
        }
        
        if ($this->pcModel->deleteProduct($id)) {
            $_SESSION['success'] = "Product deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting product!";
        }
        
        header("Location: index.php?page=pc");
        exit();
    }
    
    private function handleImageUpload($file) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; 
        
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Invalid image type! Only JPG, PNG, GIF, WEBP allowed.";
            return false;
        }
        
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = "Image size too large! Maximum 5MB allowed.";
            return false;
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'pc_' . time() . '_' . uniqid() . '.' . $file_extension;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/pc_products/' . $filename;
        } else {
            $_SESSION['error'] = "Error uploading image!";
            return false;
        }
    }
}
?>