<?php
ob_start();
session_start();
require_once 'config.php';

// ============================================================================
// EMAIL VALIDÁCIÓ FUNKCIÓ - .COM VAGY .HU VÉGŰ EMAIL-CÍMEKET FOGAD EL
// ============================================================================
function valid_email_com_or_hu($email) {
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return (bool) preg_match('/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.(com|hu)$/i', $email);
}

// ============================================================================
// JELSZÓ ERŐSSÉG ELLENŐRZÉSE
// ============================================================================
function checkPasswordStrength($password) {
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

// ============================================================================
// TELJES NEV ELLENŐRZÉSE
// ============================================================================
function validateFullName($name) {
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

// ============================================================================
// EMAIL FORMÁTUM RÉSZLETEZETT ELLENŐRZÉSE
// ============================================================================
function validateEmail($email) {
    $email = trim($email);
    $errors = [];
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } elseif (!valid_email_com_or_hu($email)) {
        $errors[] = 'Only email addresses ending with .com or .hu are allowed.';
    } elseif (strlen($email) > 100) {
        $errors[] = 'Email cannot exceed 100 characters.';
    }
    
    return $errors;
}

// ============================================================================
// BRUTE FORCE VÉDELEM KONSTANSOK
// ============================================================================
define('MAX_FAILED_ATTEMPTS', 5);
define('LOCKOUT_MINUTES', 10);

// ============================================================================
// HIÁNYZÓ MEZŐK ID-JEINEK TÁROLÁSA SESSION-BEN
// ============================================================================
function setMissingFields($fields) {
    $_SESSION['missing_fields'] = $fields;
}

function getMissingFields() {
    return $_SESSION['missing_fields'] ?? [];
}

function clearMissingFields() {
    unset($_SESSION['missing_fields']);
}

// ============================================================================
// REGISZTRÁCIÓ FELDOLGOZÁSA
// ============================================================================
if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passwordPlain = $_POST['password'] ?? '';
    
    $missingFields = [];
    $validationErrors = [];
    
    // ========================================================================
    // HIÁNYZÓ MEZŐK ELLENŐRZÉSE ÉS JELÖLÉSE
    // ========================================================================
    if ($name === '') {
        $missingFields[] = 'name';
        $validationErrors[] = 'Name is required.';
    }
    
    if ($email === '') {
        $missingFields[] = 'email';
        $validationErrors[] = 'Email is required.';
    }
    
    if ($passwordPlain === '') {
        $missingFields[] = 'password';
        $validationErrors[] = 'Password is required.';
    }
    
    // Ha vannak hiányzó mezők, azonnal vissza
    if (!empty($missingFields)) {
        setMissingFields($missingFields);
        $_SESSION['register_error'] = implode(' ', $validationErrors);
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // RÉSZLETEZETT NEV ELLENŐRZÉS
    // ========================================================================
    $nameErrors = validateFullName($name);
    if (!empty($nameErrors)) {
        $missingFields[] = 'name';
        $_SESSION['register_error'] = implode(' ', $nameErrors);
        $_SESSION['active_form'] = 'register';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // RÉSZLETEZETT EMAIL ELLENŐRZÉS
    // ========================================================================
    $emailErrors = validateEmail($email);
    if (!empty($emailErrors)) {
        $missingFields[] = 'email';
        $_SESSION['register_error'] = implode(' ', $emailErrors);
        $_SESSION['active_form'] = 'register';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // JELSZÓ ERŐSSÉG ELLENŐRZÉSE
    // ========================================================================
    $passwordErrors = checkPasswordStrength($passwordPlain);
    if (!empty($passwordErrors)) {
        $missingFields[] = 'password';
        $_SESSION['register_error'] = implode(' ', $passwordErrors);
        $_SESSION['active_form'] = 'register';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // EMAIL-CÍM FOGLALTSÁG ELLENŐRZÉSE
    // ========================================================================
    $stmt = $conn->prepare("SELECT email FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $missingFields[] = 'email';
        $_SESSION['register_error'] = 'This email is already registered.';
        $_SESSION['active_form'] = 'register';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // JELSZÓ HASH-ELÉSE BIZTONSÁGI OKOKBÓL
    // ========================================================================
    $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
    
    // ========================================================================
    // FELHASZNÁLÓ MENTÉSE AZ ADATBÁZISBA
    // ========================================================================
    $stmt = $conn->prepare("INSERT INTO `4` (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $passwordHash);
    
    if ($stmt->execute()) {
        // Sikeres regisztráció - mezők tisztítása
        clearMissingFields();
        $_SESSION['register_success'] = 'Registration successful. You can now log in.';
        $_SESSION['active_form'] = 'login';
    } else {
        $missingFields = ['name', 'email', 'password'];
        setMissingFields($missingFields);
        $_SESSION['register_error'] = 'An error occurred during registration. Please try again.';
        $_SESSION['active_form'] = 'register';
    }
    
    header("Location: index.php");
    exit();
}

// ============================================================================
// BEJELENTKEZÉS FELDOLGOZÁSA
// ============================================================================
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $missingFields = [];
    $validationErrors = [];
    
    // ========================================================================
    // HIÁNYZÓ MEZŐK ELLENŐRZÉSE
    // ========================================================================
    if ($email === '') {
        $missingFields[] = 'login_email';
        $validationErrors[] = 'Email is required.';
    }
    
    if ($password === '') {
        $missingFields[] = 'login_password';
        $validationErrors[] = 'Password is required.';
    }
    
    if (!empty($missingFields)) {
        setMissingFields($missingFields);
        $_SESSION['login_error'] = implode(' ', $validationErrors);
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // EMAIL FORMÁTUM ELLENŐRZÉSE
    // ========================================================================
    if (!valid_email_com_or_hu($email)) {
        $missingFields[] = 'login_email';
        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // FELHASZNÁLÓ KERESÉSE AZ ADATBÁZISBAN
    // ========================================================================
    $stmt = $conn->prepare("SELECT id, name, email, password, failed_attempts, last_failed_login, is_admin FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        // ====================================================================
        // BRUTE FORCE VÉDELEM: KÉSLELTETÉS HAMIS FELHASZNÁLÓK ESETÉN
        // ====================================================================
        usleep(300000);
        $missingFields = ['login_email', 'login_password'];
        setMissingFields($missingFields);
        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // ========================================================================
    // BRUTE FORCE VÉDELEM: BEJELENTKEZÉSI KISÉRLETEK ELLENŐRZÉSE
    // ========================================================================
    $failed = (int)$user['failed_attempts'];
    $lastFail = $user['last_failed_login']; 
    
    if ($failed >= MAX_FAILED_ATTEMPTS && $lastFail !== null) {
        $lastFailTs = strtotime($lastFail);
        $unlockTs = $lastFailTs + (LOCKOUT_MINUTES * 60);
        if (time() < $unlockTs) {
            $minutesLeft = ceil(($unlockTs - time()) / 60);
            $_SESSION['login_error'] = "Too many failed login attempts. Please try again in $minutesLeft minute(s).";
            $_SESSION['active_form'] = 'login';
            header("Location: index.php");
            exit();
        } else {
            // ================================================================
            // IDŐLEJÁRAT LEJÁRT - SIKERTELEN KISÉRLETEK VISSZAÁLLÍTÁSA
            // ================================================================
            $resetStmt = $conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
            $resetStmt->bind_param("i", $user['id']);
            $resetStmt->execute();
            $failed = 0;
        }
    }
    
    // ========================================================================
    // JELSZÓ ELLENŐRZÉSE
    // ========================================================================
    if (password_verify($password, $user['password'])) {
        // ====================================================================
        // SIKERES BEJELENTKEZÉS - KISÉRLETEK VISSZAÁLLÍTÁSA
        // ====================================================================
        $stmt = $conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        
        // Hiányzó mezők tisztítása
        clearMissingFields();
        
        // ====================================================================
        // SESSION VÁLTOZÓK BEÁLLÍTÁSA
        // ====================================================================
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: fooldal.php");
        exit();
    } else {
        // ====================================================================
        // HIBÁS JELSZÓ - KISÉRLETEK NÖVELÉSE
        // ====================================================================
        $stmt = $conn->prepare("UPDATE `4` SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        
        $missingFields = ['login_email', 'login_password'];
        setMissingFields($missingFields);
        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
}

// ============================================================================
// JELSZÓ VISSZAÁLLÍTÁS FELDOLGOZÁSA
// ============================================================================
if (isset($_POST['forgot'])) {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $missingFields = [];
    $validationErrors = [];
    
    // ========================================================================
    // HIÁNYZÓ MEZŐK ELLENŐRZÉSE
    // ========================================================================
    if ($email === '') {
        $missingFields[] = 'forgot_email';
        $validationErrors[] = 'Email is required.';
    }
    
    if ($newPassword === '') {
        $missingFields[] = 'forgot_new_password';
        $validationErrors[] = 'New password is required.';
    }
    
    if ($confirmPassword === '') {
        $missingFields[] = 'forgot_confirm_password';
        $validationErrors[] = 'Password confirmation is required.';
    }
    
    if (!empty($missingFields)) {
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = implode(' ', $validationErrors);
        $_SESSION['active_form'] = 'forgot';
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // EMAIL VÉGŰDIK ELLENŐRZÉSE (.COM VAGY .HU)
    // ========================================================================
    if (!valid_email_com_or_hu($email)) {
        $missingFields[] = 'forgot_email';
        $_SESSION['forgot_error'] = 'Only email addresses ending with .com or .hu are allowed.';
        $_SESSION['active_form'] = 'forgot';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // JELSZAVAK EGYEZÉSÉNEK ELLENŐRZÉSE
    // ========================================================================
    if ($newPassword !== $confirmPassword) {
        $missingFields = ['forgot_new_password', 'forgot_confirm_password'];
        $_SESSION['forgot_error'] = 'Passwords do not match.';
        $_SESSION['active_form'] = 'forgot';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // JELSZÓ ERŐSSÉG ELLENŐRZÉSE
    // ========================================================================
    $passwordErrors = checkPasswordStrength($newPassword);
    if (!empty($passwordErrors)) {
        $missingFields = ['forgot_new_password', 'forgot_confirm_password'];
        $_SESSION['forgot_error'] = implode(' ', $passwordErrors);
        $_SESSION['active_form'] = 'forgot';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // EMAIL-CÍM LÉTEZÉSÉNEK ELLENŐRZÉSE
    // ========================================================================
    $stmt = $conn->prepare("SELECT id FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $missingFields[] = 'forgot_email';
        $_SESSION['forgot_error'] = 'This email is not registered.';
        $_SESSION['active_form'] = 'forgot';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // ========================================================================
    // ÚJ JELSZÓ HASH-ELÉSE ÉS FRISSÍTÉS
    // ========================================================================
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE `4` SET password = ?, failed_attempts = 0, last_failed_login = NULL WHERE email = ?");
    $stmt->bind_param("ss", $passwordHash, $email);
    
    if ($stmt->execute()) {
        clearMissingFields();
        $_SESSION['forgot_success'] = 'Password changed successfully. You can now log in.';
        $_SESSION['active_form'] = 'login';
    } else {
        $missingFields = ['forgot_email', 'forgot_new_password', 'forgot_confirm_password'];
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = 'An error occurred while updating the password.';
        $_SESSION['active_form'] = 'forgot';
    }
    
    header("Location: index.php");
    exit();
}

// ============================================================================
// OUTPUT BUFFER LEZÁRÁSA
// ============================================================================
ob_end_flush();