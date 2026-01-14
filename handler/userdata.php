<?php
class UserData {
    private $id;
    private $name;
    private $email;
    private $password;
    private $failed_attempts;
    private $last_failed_login;
    private $is_admin;
    
    const MAX_FAILED_ATTEMPTS = 5;
    const LOCKOUT_MINUTES = 10;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->failed_attempts = $data['failed_attempts'] ?? 0;
        $this->last_failed_login = $data['last_failed_login'] ?? null;
        $this->is_admin = $data['is_admin'] ?? 0;
    }
    
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getFailedAttempts() { return $this->failed_attempts; }
    public function getLastFailedLogin() { return $this->last_failed_login; }
    public function getIsAdmin() { return $this->is_admin; }
    
    public function setName($name) { 
        $this->name = $name; 
        return $this;
    }
    public function setEmail($email) { 
        $this->email = $email; 
        return $this;
    }
    public function setPassword($password) { 
        $this->password = $password; 
        return $this;
    }
    
    public static function validateFullName($name) {
        $name = trim($name);
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required.';
        elseif (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters long.';
        elseif (strlen($name) > 50) $errors[] = 'Name cannot exceed 50 characters.';
        elseif (!preg_match('/^[a-zA-Z\sáéíóöőúüűÁÉÍÓÖŐÚÜŰ\-]+$/', $name)) {
            $errors[] = 'Name can only contain letters, spaces and hyphens.';
        }
        return $errors;
    }
    
    public static function validateEmail($email) {
        $email = trim($email);
        $errors = [];
        if (empty($email)) $errors[] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
        elseif (!self::isValidEmailComOrHu($email)) $errors[] = 'Only email addresses ending with .com or .hu are allowed.';
        elseif (strlen($email) > 100) $errors[] = 'Email cannot exceed 100 characters.';
        return $errors;
    }
    
    public static function isValidEmailComOrHu($email) {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        return (bool) preg_match('/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.(com|hu)$/i', $email);
    }
    
    public static function checkPasswordStrength($password) {
        $errors = [];
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters long.';
        if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Password must contain at least one uppercase letter.';
        if (!preg_match('/[0-9]/', $password)) $errors[] = 'Password must contain at least one number.';
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            $errors[] = 'Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>).';
        }
        return $errors;
    }
    
    public function hashPassword() {
        if (!empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
        return $this;
    }
    
    public function verifyPassword($password) {
        if (empty($this->password)) return false;
        return password_verify($password, $this->password);
    }
    
    public function isLockedOut() {
        if ($this->failed_attempts >= self::MAX_FAILED_ATTEMPTS && $this->last_failed_login) {
            $lockoutTime = strtotime($this->last_failed_login) + (self::LOCKOUT_MINUTES * 60);
            return time() < $lockoutTime;
        }
        return false;
    }
    
    public function getLockoutTimeLeft() {
        if ($this->last_failed_login) {
            $lockoutEnd = strtotime($this->last_failed_login) + (self::LOCKOUT_MINUTES * 60);
            $timeLeft = $lockoutEnd - time();
            return max(1, ceil($timeLeft / 60));
        }
        return 0;
    }
}
?>
