<?php
ob_start();
session_start();

//Adatbázis kapcsolat
require_once 'config.php';
require_once 'handler/userhandler.php';

// PHPMailer fájlok 
require_once 'src/Exception.php';
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ============================================================================
// SESSION HELPER FUNKCIÓK
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

function setActiveForm($form) {
    $_SESSION['active_form'] = $form;
}

// ============================================================================
// EMAIL KÜLDÉS FUNKCIÓ
// ============================================================================
function sendRegistrationEmail($name, $email) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP konfiguráció
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


$userHandler = new UserHandler($conn);

// REGISZTRÁCIÓ
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
        setActiveForm('register');
        header("Location: index.php");
        exit();
    }
    
    // UserData osztály validáció
    $nameErrors = UserData::validateFullName($name);
    if (!empty($nameErrors)) {
        $missingFields[] = 'name';
        $_SESSION['register_error'] = implode(' ', $nameErrors);
        setActiveForm('register');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    $emailErrors = UserData::validateEmail($email);
    if (!empty($emailErrors)) {
        $missingFields[] = 'email';
        $_SESSION['register_error'] = implode(' ', $emailErrors);
        setActiveForm('register');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    $passwordErrors = UserData::checkPasswordStrength($password);
    if (!empty($passwordErrors)) {
        $missingFields[] = 'password';
        $_SESSION['register_error'] = implode(' ', $passwordErrors);
        setActiveForm('register');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    try {
        // UserData objektum létrehozása
        $userData = new UserData();
        $userData->setName($name)
                 ->setEmail($email)
                 ->setPassword($password);
        
        // Regisztráció a UserHandlerrel
        if ($userHandler->register($userData)) {
            // Email küldés
            sendRegistrationEmail($name, $email);
            
            // Session tisztítása
            clearMissingFields();
            $_SESSION['register_success'] = 'Registration successful. You can now log in.';
            setActiveForm('login');
        }
    } catch (Exception $e) {
        $missingFields = ['name', 'email', 'password'];
        setMissingFields($missingFields);
        $_SESSION['register_error'] = $e->getMessage();
        setActiveForm('register');
    }
    
    header("Location: index.php");
    exit();
}

// BEJELENTKEZÉS
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
        setActiveForm('login');
        header("Location: index.php");
        exit();
    }
    
    // Email formátum ellenőrzése
    if (!UserData::isValidEmailComOrHu($email)) {
        $missingFields[] = 'login_email';
        $_SESSION['login_error'] = 'Incorrect email or password.';
        setActiveForm('login');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    try {
        // Bejelentkezés a UserHandlerrel
        if ($userHandler->login($email, $password)) {
            $user = $userHandler->getUserByEmail($email);
            
            if ($user) {
                // Session változók beállítása
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['name'] = $user->getName();
                $_SESSION['email'] = $user->getEmail();
                $_SESSION['is_admin'] = $user->getIsAdmin();
                
                // Hiányzó mezők törlése
                clearMissingFields();
                header("Location: fooldal.php");
                exit();
            }
        }
    } catch (Exception $e) {
        $missingFields = ['login_email', 'login_password'];
        setMissingFields($missingFields);
        $_SESSION['login_error'] = $e->getMessage();
        setActiveForm('login');
        header("Location: index.php");
        exit();
    }
}

// JELSZÓ VISSZAÁLLÍTÁS
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
        setActiveForm('forgot');
        header("Location: index.php");
        exit();
    }
    
    // Email végződés ellenőrzése
    if (!UserData::isValidEmailComOrHu($email)) {
        $missingFields[] = 'forgot_email';
        $_SESSION['forgot_error'] = 'Only email addresses ending with .com or .hu are allowed.';
        setActiveForm('forgot');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Jelszavak egyezése
    if ($newPassword !== $confirmPassword) {
        $missingFields = ['forgot_new_password', 'forgot_confirm_password'];
        $_SESSION['forgot_error'] = 'Passwords do not match.';
        setActiveForm('forgot');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    // Jelszó erősség ellenőrzése
    $passwordErrors = UserData::checkPasswordStrength($newPassword);
    if (!empty($passwordErrors)) {
        $missingFields = ['forgot_new_password', 'forgot_confirm_password'];
        $_SESSION['forgot_error'] = implode(' ', $passwordErrors);
        setActiveForm('forgot');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    try {
        // Jelszó visszaállítás a
        if ($userHandler->resetPassword($email, $newPassword)) {
            clearMissingFields();
            $_SESSION['forgot_success'] = 'Password changed successfully. You can now log in.';
            setActiveForm('login');
        }
    } catch (Exception $e) {
        $missingFields = ['forgot_email', 'forgot_new_password', 'forgot_confirm_password'];
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = $e->getMessage();
        setActiveForm('forgot');
    }
    
    header("Location: index.php");
    exit();
}

ob_end_flush();
?>
