<?php

class UserData {
    // Felhasználói adatok
    private $id;
    private $name;
    private $email;
    private $password;
    private $failedAttempts;
    private $lastFailedLogin;
    private $isAdmin;
    
    // Konstansok
    const MAX_FAILED_ATTEMPTS = 5;
    const LOCKOUT_MINUTES = 10;
    
    // Konstruktor
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->password = $data['password'] ?? '';
            $this->failedAttempts = $data['failed_attempts'] ?? 0;
            $this->lastFailedLogin = $data['last_failed_login'] ?? null;
            $this->isAdmin = $data['is_admin'] ?? 0;
        }
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function getFailedAttempts() {
        return $this->failedAttempts;
    }
    
    public function getLastFailedLogin() {
        return $this->lastFailedLogin;
    }
    
    public function getIsAdmin() {
        return $this->isAdmin;
    }
    
    // Setter metódusok
    public function setName($name) {
        $this->name = trim($name);
        return $this;
    }
    
    public function setEmail($email) {
        $this->email = trim($email);
        return $this;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function setFailedAttempts($attempts) {
        $this->failedAttempts = (int)$attempts;
        return $this;
    }
    
    public function setLastFailedLogin($timestamp) {
        $this->lastFailedLogin = $timestamp;
        return $this;
    }
    
    public function setIsAdmin($isAdmin) {
        $this->isAdmin = (int)$isAdmin;
        return $this;
    }
    
    // Validációs metódusok
    public static function validateFullName($name) {
        $name = trim($name);
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Name is required.';
        } elseif (strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters long.';
        } elseif (strlen($name) > 50) {
            $errors[] = 'Name cannot exceed 50 characters.';
        } elseif (!preg_match('/^[a-zA-Z\sáéíóöőúüűÁÉÍÓÖŐÚÜŰ\-]+$/', $name)) {
            $errors[] = 'Name can only contain letters, spaces and hyphens.';
        }
        
        return $errors;
    }
    
    public static function validateEmail($email) {
        $email = trim($email);
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif (!self::isValidEmailComOrHu($email)) {
            $errors[] = 'Only email addresses ending with .com or .hu are allowed.';
        } elseif (strlen($email) > 100) {
            $errors[] = 'Email cannot exceed 100 characters.';
        }
        
        return $errors;
    }
    
    public static function isValidEmailComOrHu($email) {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return (bool) preg_match('/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.(com|hu)$/i', $email);
    }
    
    public static function checkPasswordStrength($password) {
        $errors = [];
        
        // Minimum hossz
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        
        // Nagybetű
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        
        // Szám
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        
        // Speciális karakter
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            $errors[] = 'Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>).';
        }
        
        return $errors;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'failed_attempts' => $this->failedAttempts,
            'last_failed_login' => $this->lastFailedLogin,
            'is_admin' => $this->isAdmin
        ];
    }
    
    // TITKOSITAS
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return $this;
    }
    
    // Jelszó ellenőrzés
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
    
    // Brute force ellenőrzés
    public function isLockedOut() {
        if ($this->failedAttempts >= self::MAX_FAILED_ATTEMPTS && $this->lastFailedLogin !== null) {
            $lastFailTimestamp = strtotime($this->lastFailedLogin);
            $unlockTimestamp = $lastFailTimestamp + (self::LOCKOUT_MINUTES * 60);
            return time() < $unlockTimestamp;
        }
        return false;
    }

    public function getLockoutTimeLeft() {
        if ($this->lastFailedLogin === null) {
            return 0;
        }
        
        $lastFailTimestamp = strtotime($this->lastFailedLogin);
        $unlockTimestamp = $lastFailTimestamp + (self::LOCKOUT_MINUTES * 60);
        $secondsLeft = $unlockTimestamp - time();
        
        return max(0, ceil($secondsLeft / 60)); //Percben kiszamoljuk
    }
}