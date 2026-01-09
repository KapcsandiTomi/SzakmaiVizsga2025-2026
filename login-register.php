<?php
ob_start();
session_start();
require_once 'config.php';

// PHPMailer fájlok 
require_once 'src/Exception.php';
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ============================================================================
// EMAIL VALIDÁCIÓ FUNKCIÓ - CSAK .COM VAGY .HU VÉGŰ EMAILEKET FOGAD EL
// ============================================================================
function isValidEmailComOrHu($email) {
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
// TELJES NÉV ELLENŐRZÉSE
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
// EMAIL FORMÁTUM ELLENŐRZÉSE
// ============================================================================
function validateEmail($email) {
    $email = trim($email);
    $errors = [];
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } elseif (!isValidEmailComOrHu($email)) {
        $errors[] = 'Only email addresses ending with .com or .hu are allowed.';
    } elseif (strlen($email) > 100) {
        $errors[] = 'Email cannot exceed 100 characters.';
    }
    
    return $errors;
}

// ============================================================================
// REGISZTRÁCIÓS EMAIL KÜLDÉSE PHPMailer-REL
// ============================================================================
function sendRegistrationEmail($name, $email) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP konfiguráció (config.php-ban kell definiálni)
        $mail->isSMTP();
        $mail->Host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $mail->Password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->CharSet = 'UTF-8';
        
        // Feladó és címzett
        $mail->setFrom(
            defined('EMAIL_FROM') ? EMAIL_FROM : 'aquaminishop@gmail.com', 
            'Aqua Mini Shop Team'
        );
        $mail->addAddress($email, $name);
        $mail->addReplyTo(
            defined('EMAIL_REPLY_TO') ? EMAIL_REPLY_TO : 'aquaminishop@gmail.com', 
            'Support'
        );
        
        // Email tartalma
        $mail->isHTML(true);
        $mail->Subject = 'Registration Successful';
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Registration Successful</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { background: #4c93afff; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; }
                .footer { background: #f1f1f1; text-align: center; padding: 15px; font-size: 12px; color: #666; }
                .info-box { background: #f8f9fa; border-left: 4px solid #4cafa7ff; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Registration Successful!</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>$name</strong>,</p>
                    <p>Thank you for registering on our website. Your registration has been completed successfully.</p>
                    
                    <div class='info-box'>
                        <h3>Registration Details:</h3>
                        <p><strong>Name:</strong> $name</p>
                        <p><strong>Email:</strong> $email</p>
                        <p><strong>Registration Time:</strong> " . date('Y-m-d H:i:s') . "</p>
                    </div>
                    
                    <p>You can now log in to your account on our website.</p>
                    <p>If you did not register, please ignore this email.</p>
                    <p>Best regards,<br><strong>Aqua Mini Shop Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message, please do not reply.</p>
                    <p>&copy; " . date('Y') . " All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Dear $name,\n\nThank you for registering on our website.\n\nRegistration Details:\nName: $name\nEmail: $email\nTime: " . date('Y-m-d H:i:s') . "\n\nBest regards,\nThe Website Team";
        
        // Email küldése
        if ($mail->send()) {
            return true;
        } else {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return false;
        }
    } catch (Exception $e) {
        error_log("PHPMailer Exception: " . $e->getMessage());
        return false;
    }
}

// ============================================================================
// BRUTE FORCE VÉDELEM KONSTANSOK
// ============================================================================
define('MAX_FAILED_ATTEMPTS', 5);
define('LOCKOUT_MINUTES', 10);

// ============================================================================
// HIÁNYZÓ MEZŐK ID-JEINEK TÁROLÁSA 
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
// REGISZTRÁCIÓ
// ============================================================================
if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $missingFields = [];
    $validationErrors = [];
    
    // Hiányzó mezők ellenőrzése
    if ($name === '') {
        $missingFields[] = 'name';
        $validationErrors[] = 'Name is required.';
    }
    
    if ($email === '') {
        $missingFields[] = 'email';
        $validationErrors[] = 'Email is required.';
    }
    
    if ($password === '') {
        $missingFields[] = 'password';
        $validationErrors[] = 'Password is required.';
    }
    
    if (!empty($missingFields)) {
        setMissingFields($missingFields);
        $_SESSION['register_error'] = implode(' ', $validationErrors);
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }
    
    // Részletes név ellenőrzés
    $nameErrors = validateFullName($name);
    if (!empty($nameErrors)) {
        $missingFields[] = 'name';
        $_SESSION['register_error'] = implode(' ', $nameErrors);
        $_SESSION['active_form'] = 'register';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Részletes email ellenőrzés
    $emailErrors = validateEmail($email);
    if (!empty($emailErrors)) {
        $missingFields[] = 'email';
        $_SESSION['register_error'] = implode(' ', $emailErrors);
        $_SESSION['active_form'] = 'register';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Jelszó erősség ellenőrzése
    $passwordErrors = checkPasswordStrength($password);
    if (!empty($passwordErrors)) {
        $missingFields[] = 'password';
        $_SESSION['register_error'] = implode(' ', $passwordErrors);
        $_SESSION['active_form'] = 'register';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Email cím foglaltságának ellenőrzése
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
    
    // Jelszó hash-elése
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Felhasználó mentése az adatbázisba
    $stmt = $conn->prepare("INSERT INTO `4` (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $passwordHash);
    
    if ($stmt->execute()) {
        // Regisztrációs email küldése
        sendRegistrationEmail($name, $email);
        
        // Mezők tisztítása és sikeres üzenet
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
// BEJELENTKEZÉS
// ============================================================================
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $missingFields = [];
    $validationErrors = [];
    
    // Hiányzó mezők ellenőrzése
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
    
    // Email formátum ellenőrzése
    if (!isValidEmailComOrHu($email)) {
        $missingFields[] = 'login_email';
        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Felhasználó keresése az adatbázisban
    $stmt = $conn->prepare("SELECT id, name, email, password, failed_attempts, last_failed_login, is_admin FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        // Brute force védelem: késleltetés hamis felhasználók esetén
        usleep(300000);
        $missingFields = ['login_email', 'login_password'];
        setMissingFields($missingFields);
        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Brute force védelem: bejelentkezési kísérletek ellenőrzése
    $failedAttempts = (int)$user['failed_attempts'];
    $lastFailedLogin = $user['last_failed_login']; 
    
    if ($failedAttempts >= MAX_FAILED_ATTEMPTS && $lastFailedLogin !== null) {
        $lastFailTimestamp = strtotime($lastFailedLogin);
        $unlockTimestamp = $lastFailTimestamp + (LOCKOUT_MINUTES * 60);
        if (time() < $unlockTimestamp) {
            $minutesLeft = ceil(($unlockTimestamp - time()) / 60);
            $_SESSION['login_error'] = "Too many failed login attempts. Please try again in $minutesLeft minute(s).";
            $_SESSION['active_form'] = 'login';
            header("Location: index.php");
            exit();
        } else {
            // Zárlat lejárt - sikertelen kísérletek visszaállítása
            $resetStmt = $conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
            $resetStmt->bind_param("i", $user['id']);
            $resetStmt->execute();
            $failedAttempts = 0;
        }
    }
    
    // Jelszó ellenőrzése
    if (password_verify($password, $user['password'])) {

        // Sikeres bejelentkezés - kísérletek visszaállítása
        $stmt = $conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        
        // Hiányzó mezők 
        clearMissingFields();
        
        //változók beállítása
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: fooldal.php");
        exit();
    } else {
        // Helytelen jelszó és kísérletek növelése
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
// JELSZÓ VISSZAÁLLÍTÁS 
// ============================================================================
if (isset($_POST['forgot'])) {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $missingFields = [];
    $validationErrors = [];
    
    // Hiányzó mezők ellenőrzése
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
    
    // Email végződés ellenőrzése (.com vagy .hu)
    if (!isValidEmailComOrHu($email)) {
        $missingFields[] = 'forgot_email';
        $_SESSION['forgot_error'] = 'Only email addresses ending with .com or .hu are allowed.';
        $_SESSION['active_form'] = 'forgot';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Jelszavak egyezésének ellenőrzése
    if ($newPassword !== $confirmPassword) {
        $missingFields = ['forgot_new_password', 'forgot_confirm_password'];
        $_SESSION['forgot_error'] = 'Passwords do not match.';
        $_SESSION['active_form'] = 'forgot';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Jelszó erősség ellenőrzése
    $passwordErrors = checkPasswordStrength($newPassword);
    if (!empty($passwordErrors)) {
        $missingFields = ['forgot_new_password', 'forgot_confirm_password'];
        $_SESSION['forgot_error'] = implode(' ', $passwordErrors);
        $_SESSION['active_form'] = 'forgot';
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Email cím létezésének ellenőrzése
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
    
    // Új jelszó hash-elése és frissítése
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
// LEZÁRÁS
// ============================================================================
ob_end_flush();
?>
