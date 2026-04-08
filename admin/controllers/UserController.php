<?php
class UserController {
    private $conn;
    private $userModel;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->userModel = new UserModel($connection);
    }
    
    public function index() {
        $users = $this->userModel->getAllUsers();
        
        $viewPath = __DIR__ . '/../views/admin/users.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View file not found: " . $viewPath);
        }
    }
    
    public function delete($id) {
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "You cannot delete yourself!";
            header("Location: index.php?page=users");
            exit();
        }
        
        if ($this->userModel->deleteUser($id)) {
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting user!";
        }
        
        header("Location: index.php?page=users");
        exit();
    }
    
    public function makeAdmin($id) {
        session_start();
        
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "You are already admin!";
            header("Location: index.php?page=users");
            exit();
        }
        
        if ($this->userModel->makeAdmin($id)) {
            $_SESSION['success'] = "Admin rights granted!";
        } else {
            $_SESSION['error'] = "Error granting admin rights!";
        }
        
        header("Location: index.php?page=users");
        exit();
    }
    
    public function removeAdmin($id) {
        session_start();
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "You cannot remove yourself!";
            header("Location: index.php?page=users");
            exit();
        }
        
        if ($this->userModel->removeAdmin($id)) {
            $_SESSION['success'] = "Admin rights removed!";
        } else {
            $_SESSION['error'] = "Error removing admin rights!";
        }
        
        header("Location: index.php?page=users");
        exit();
    }
}
?>
