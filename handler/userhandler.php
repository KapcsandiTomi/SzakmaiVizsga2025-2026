<?php
require_once 'userdata.php';

class UserHandler {
    private $conn;
    private $user;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->user = null;
    }
    
    // Regisztráció
    public function register(UserData $userData) {
        try {
            // Email egyediség ellenőrzése
            if ($this->emailExists($userData->getEmail())) {
                throw new Exception('This email is already registered.');
            }
            
            // Jelszó hash-elése
            $userData->hashPassword();
            
            // Felhasználó mentése
            $stmt = $this->conn->prepare("INSERT INTO `4` (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", 
                $userData->getName(),
                $userData->getEmail(),
                $userData->getPassword()
            );
            
            if ($stmt->execute()) {
                $userData = new UserData([
                    'id' => $stmt->insert_id,
                    'name' => $userData->getName(),
                    'email' => $userData->getEmail()
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
    
    // Bejelentkezés
    public function login($email, $password) {
        try {
            // Felhasználó keresése
            $stmt = $this->conn->prepare("SELECT * FROM `4` WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                usleep(300000);
                throw new Exception('Incorrect email or password.');
            }
            
            $userData = $result->fetch_assoc();
            $this->user = new UserData($userData);
            
            // Brute force ellenőrzés
            if ($this->user->isLockedOut()) {
                $minutesLeft = $this->user->getLockoutTimeLeft();
                throw new Exception("Too many failed login attempts. Please try again in $minutesLeft minute(s).");
            }
            
            // Jelszó ellenőrzés
            if ($this->user->verifyPassword($password)) {
                // Sikeres bejelentkezés - reset
                $this->resetFailedAttempts($this->user->getId());
                return true;
            } else {
                // Sikertelen kísérlet - növelés
                $this->incrementFailedAttempts($this->user->getId());
                throw new Exception('Incorrect email or password.');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    // Jelszó visszaállítás
    public function resetPassword($email, $newPassword) {
        try {
            // Felhasználó ellenőrzése
            if (!$this->emailExists($email)) {
                throw new Exception('This email is not registered.');
            }
            
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare("UPDATE `4` SET password = ?, failed_attempts = 0, last_failed_login = NULL WHERE email = ?");
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
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    
    private function incrementFailedAttempts($userId) {
        $stmt = $this->conn->prepare("UPDATE `4` SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
    
    private function resetFailedAttempts($userId) {
        $stmt = $this->conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
    
    // Felhasználó lekérdezése
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM `4` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            return new UserData($userData);
        }
        
        return null;
    }
    
    // Aktuális felhasználó
    public function getCurrentUser() {
        return $this->user;
    }
    
    // Felhasználók száma
    public function getUserCount() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM `4`");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }
}
?>