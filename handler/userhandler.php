<?php
require_once 'userdata.php';

class UserHandler {
    private $conn;
    private $user;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->user = null;
    }
    
    public function register(UserData $userData) {
        try {
            if ($this->emailExists($userData->getEmail())) {
                throw new Exception('This email is already registered.');
            }
            
            if (empty($userData->getName()) || empty($userData->getEmail()) || empty($userData->getPassword())) {
                throw new Exception('All fields are required.');
            }
            
            $userData->hashPassword();
            
            $stmt = $this->conn->prepare("INSERT INTO `4` (name, email, password, failed_attempts, last_failed_login, is_admin) VALUES (?, ?, ?, 0, NULL, 0)");
            if (!$stmt) {
                throw new Exception('Database error during registration.');
            }
            
            $name = $userData->getName();
            $email = $userData->getEmail();
            $password = $userData->getPassword();
            
            $stmt->bind_param("sss", $name, $email, $password);
            
            if ($stmt->execute()) {
                $userData = new UserData([
                    'id' => $stmt->insert_id,
                    'name' => $userData->getName(),
                    'email' => $userData->getEmail(),
                    'failed_attempts' => 0,
                    'last_failed_login' => null,
                    'is_admin' => 0
                ]);
                $this->user = $userData;
                return true;
            } else {
                throw new Exception('Database error during registration.');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `4` WHERE email = ?");
            if (!$stmt) {
                throw new Exception('Database connection error.');
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                usleep(300000);
                throw new Exception('Incorrect email or password.');
            }
            
            $userData = $result->fetch_assoc();
            $this->user = new UserData($userData);
            
            if ($this->user->isLockedOut()) {
                $minutesLeft = $this->user->getLockoutTimeLeft();
                throw new Exception("Too many failed login attempts. Please try again in $minutesLeft minute(s).");
            }
            
            if ($this->user->verifyPassword($password)) {
                $this->resetFailedAttempts($this->user->getId());
                return true;
            } else {
                $this->incrementFailedAttempts($this->user->getId());
                throw new Exception('Incorrect email or password.');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function resetPassword($email, $newPassword) {
        try {
            if (!$this->emailExists($email)) {
                throw new Exception('This email is not registered.');
            }
            
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare("UPDATE `4` SET password = ?, failed_attempts = 0, last_failed_login = NULL WHERE email = ?");
            if (!$stmt) {
                throw new Exception('Database error during password reset.');
            }
            
            $stmt->bind_param("ss", $passwordHash, $email);
            
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception('Database error during password reset.');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM `4` WHERE email = ?");
        if (!$stmt) return false;
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    
    private function incrementFailedAttempts($userId) {
        $stmt = $this->conn->prepare("UPDATE `4` SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }
    }
    
    private function resetFailedAttempts($userId) {
        $stmt = $this->conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }
    }
    
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM `4` WHERE email = ?");
        if (!$stmt) return null;
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            return new UserData($userData);
        }
        
        return null;
    }
    
    public function getCurrentUser() {
        return $this->user;
    }
    
    public function getUserCount() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `4`");
        if (!$stmt) return 0;
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }
}
?>
