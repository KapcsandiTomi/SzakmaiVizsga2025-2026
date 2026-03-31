<?php
ob_start();
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../handler/userhandler.php';
require_once __DIR__ . '/../src/PHPMailer.php';
require_once __DIR__ . '/../src/Exception.php';
require_once __DIR__ . '/../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

$userHandler = null;
try {
    $userHandler = new UserHandler($conn);
} catch (Exception $e) {
    $_SESSION['login_error'] = 'System error. Please try again later.';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}

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

function clearForgotVerificationSession() {
    unset(
        $_SESSION['password_reset_code_hash'],
        $_SESSION['password_reset_code_email'],
        $_SESSION['password_reset_code_expires_at'],
        $_SESSION['forgot_code_expires_at']
    );
}

function generateVerificationCode(): string {
    return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function sendResetCodeEmail(string $email, string $code): bool {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Password reset verification code';
        $mail->Body =
            '<!doctype html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,sans-serif;">' .
            '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6fb;padding:24px 0;">' .
            '<tr><td align="center">' .
            '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:94%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">' .
            '<tr><td style="background:linear-gradient(135deg,#0ea5e9,#2563eb);padding:24px 28px;color:#ffffff;">' .
            '<h1 style="margin:0;font-size:24px;line-height:1.3;">Aqua Mini Shop</h1>' .
            '<p style="margin:6px 0 0 0;font-size:14px;opacity:0.92;">Password reset verification</p>' .
            '</td></tr>' .
            '<tr><td style="padding:28px;">' .
            '<h2 style="margin:0 0 12px 0;font-size:22px;color:#0f172a;">Your 6-digit code</h2>' .
            '<p style="margin:0 0 18px 0;color:#334155;font-size:15px;line-height:1.6;">Use this code to continue your password reset. The code is valid for <strong>2 minutes</strong>.</p>' .
            '<div style="margin:18px 0 20px 0;padding:16px;border:1px dashed #93c5fd;border-radius:12px;background:#eff6ff;text-align:center;">' .
            '<span style="display:inline-block;font-size:36px;letter-spacing:8px;font-weight:800;color:#1d4ed8;">' . htmlspecialchars($code) . '</span>' .
            '</div>' .
            '<p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">If you did not request this code, you can safely ignore this email.</p>' .
            '</td></tr>' .
            '<tr><td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;">This is an automated email from Aqua Mini Shop.</td></tr>' .
            '</table>' .
            '</td></tr></table></body></html>';
        $mail->AltBody = "Aqua Mini Shop password reset code: $code. This code is valid for 2 minutes. If you did not request this, ignore this email.";

        return $mail->send();
    } catch (MailException $e) {
        return false;
    }
}

function sendPasswordChangedEmail(string $email): bool {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your password was changed';
        $mail->Body =
            '<!doctype html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,sans-serif;">' .
            '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6fb;padding:24px 0;">' .
            '<tr><td align="center">' .
            '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:94%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">' .
            '<tr><td style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:24px 28px;color:#ffffff;">' .
            '<h1 style="margin:0;font-size:24px;line-height:1.3;">Aqua Mini Shop</h1>' .
            '<p style="margin:6px 0 0 0;font-size:14px;opacity:0.92;">Security notification</p>' .
            '</td></tr>' .
            '<tr><td style="padding:28px;">' .
            '<h2 style="margin:0 0 12px 0;font-size:22px;color:#0f172a;">Your password was changed</h2>' .
            '<p style="margin:0 0 12px 0;color:#334155;font-size:15px;line-height:1.6;">This is a confirmation that your Aqua Mini Shop account password has been updated successfully.</p>' .
            '<div style="margin:18px 0;padding:14px 16px;border:1px solid #bbf7d0;border-radius:12px;background:#f0fdf4;color:#166534;font-size:14px;line-height:1.5;">If you made this change, no further action is needed.</div>' .
            '<div style="margin:0;padding:14px 16px;border:1px solid #fecaca;border-radius:12px;background:#fef2f2;color:#991b1b;font-size:14px;line-height:1.5;">If this was not you, contact support immediately and secure your account.</div>' .
            '</td></tr>' .
            '<tr><td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;">This is an automated security email from Aqua Mini Shop.</td></tr>' .
            '</table>' .
            '</td></tr></table></body></html>';
        $mail->AltBody = 'Your Aqua Mini Shop password was changed successfully. If this was not you, contact support immediately.';

        return $mail->send();
    } catch (MailException $e) {
        return false;
    }
}

function sendWelcomeEmail(string $name, string $email): bool {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Aqua Mini Shop!';
        $mail->Body =
            '<!doctype html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,sans-serif;">' .
            '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6fb;padding:24px 0;">' .
            '<tr><td align="center">' .
            '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:94%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">' .
            '<tr><td style="background:linear-gradient(135deg,#0ea5e9,#2563eb);padding:32px 28px;color:#ffffff;text-align:center;">' .
            '<h1 style="margin:0;font-size:32px;line-height:1.2;">Welcome to</h1>' .
            '<p style="margin:8px 0 0 0;font-size:28px;font-weight:700;">Aqua Mini Shop</p>' .
            '</td></tr>' .
            '<tr><td style="padding:32px 28px;">' .
            '<h2 style="margin:0 0 8px 0;font-size:22px;color:#0f172a;">Hey, ' . htmlspecialchars($name) . '! 👋</h2>' .
            '<p style="margin:0 0 16px 0;color:#334155;font-size:15px;line-height:1.6;">Welcome to Aqua Mini Shop! Your account has been successfully created and you\'re ready to start shopping.</p>' .
            '<div style="margin:20px 0;padding:16px;border:1px solid #dbeafe;border-radius:12px;background:#f0f9ff;color:#0c4a6e;font-size:14px;line-height:1.6;">' .
            '<strong>Your account details:</strong><br/>Email: ' . htmlspecialchars($email) . '</div>' .
            '<p style="margin:16px 0;color:#334155;font-size:15px;line-height:1.6;">Log in to your account to explore our products and manage your profile:</p>' .
            '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px auto;">' .
            '<tr><td style="background:linear-gradient(135deg,#2563eb,#1d4ed8);padding:12px 28px;border-radius:8px;">' .
            '<a href="https://szakmai.local/index.php" target="_blank" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:600;font-size:15px;">Log In to Your Account</a>' .
            '</td></tr></table>' .
            '<p style="margin:20px 0 12px 0;color:#334155;font-size:15px;line-height:1.6;">Here\'s what you can do with your account:</p>' .
            '<ul style="margin:0 0 16px 0;padding-left:20px;color:#334155;font-size:14px;line-height:1.8;">' .
            '<li>Browse our amazing products</li>' .
            '<li>Save favorites for later</li>' .
            '<li>Review and track your orders</li>' .
            '<li>Manage your profile and preferences</li>' .
            '</ul>' .
            '<p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;border-top:1px solid #e2e8f0;padding-top:16px;">If you have any questions, feel free to contact our support team.</p>' .
            '</td></tr>' .
            '<tr><td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;text-align:center;">Happy shopping! 🎉</td></tr>' .
            '</table>' .
            '</td></tr></table></body></html>';
        $mail->AltBody = "Welcome to Aqua Mini Shop, $name! Your account has been created successfully. Log in at https://szakmai.local/index.php to start shopping.";

        return $mail->send();
    } catch (MailException $e) {
        return false;
    }
}

// REGISZTRÁCIÓ
if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $terms = $_POST['terms'] ?? '';
    
    $missingFields = [];
    $validationErrors = [];
    
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
    
    if ($terms !== 'on') {
        $validationErrors[] = 'You must accept the terms & conditions.';
    }
    
    if (!empty($missingFields)) {
        setMissingFields($missingFields);
        $_SESSION['register_error'] = implode(' ', $validationErrors);
        setActiveForm('register');
        header("Location: index.php");
        exit();
    }
    
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
        $userData = new UserData();
        $userData->setName($name)
                 ->setEmail($email)
                 ->setPassword($password);
        
        if ($userHandler->register($userData)) {
            clearMissingFields();
            $mailSent = sendWelcomeEmail($name, $email);
            $_SESSION['register_success'] = $mailSent
                ? 'Registration successful! Welcome email sent. You can now log in.'
                : 'Registration successful! You can now log in. Welcome email could not be sent.';
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
    
    if (!UserData::isValidEmailComOrHu($email)) {
        $missingFields[] = 'login_email';
        $_SESSION['login_error'] = 'Invalid email format. Only .com or .hu domains allowed.';
        setActiveForm('login');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    try {
        if ($userHandler->login($email, $password)) {
            $user = $userHandler->getUserByEmail($email);
            
            if ($user) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['name'] = $user->getName();
                $_SESSION['email'] = $user->getEmail();
                $_SESSION['is_admin'] = $user->getIsAdmin();
                
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
    
    header("Location: index.php");
    exit();
}

// JELSZÓ VISSZAÁLLÍTÁS
if (isset($_POST['send_reset_code'])) {
    $email = trim($_POST['email'] ?? '');
    $missingFields = [];

    if ($email === '') {
        $missingFields[] = 'forgot_email';
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = 'Email is required to send a verification code.';
        setActiveForm('forgot');
        header("Location: index.php");
        exit();
    }

    if (!UserData::isValidEmailComOrHu($email)) {
        $missingFields[] = 'forgot_email';
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = 'Invalid email format. Only .com or .hu domains allowed.';
        setActiveForm('forgot');
        header("Location: index.php");
        exit();
    }

    try {
        if (!$userHandler->emailExists($email)) {
            throw new Exception('This email is not registered.');
        }

        $verificationCode = generateVerificationCode();
        $expiresAt = time() + 120;

        if (!sendResetCodeEmail($email, $verificationCode)) {
            throw new Exception('Failed to send verification code email. Please try again.');
        }

        $_SESSION['password_reset_code_hash'] = hash('sha256', $verificationCode);
        $_SESSION['password_reset_code_email'] = $email;
        $_SESSION['password_reset_code_expires_at'] = $expiresAt;
        $_SESSION['forgot_code_expires_at'] = $expiresAt;

        clearMissingFields();
        $_SESSION['forgot_success'] = 'Verification code sent. Enter it within 2 minutes.';
        setActiveForm('forgot');
    } catch (Exception $e) {
        $missingFields[] = 'forgot_email';
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = $e->getMessage();
        setActiveForm('forgot');
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['forgot'])) {
    $email = trim($_POST['email'] ?? '');
    if ($email === '' && !empty($_SESSION['password_reset_code_email'])) {
        $email = (string) $_SESSION['password_reset_code_email'];
    }
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $verificationCode = trim($_POST['verification_code'] ?? '');
    
    $missingFields = [];
    $validationErrors = [];
    
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

    if ($verificationCode === '') {
        $missingFields[] = 'forgot_verification_code';
        $validationErrors[] = 'Verification code is required.';
    }
    
    if (!empty($missingFields)) {
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = implode(' ', $validationErrors);
        setActiveForm('forgot');
        header("Location: index.php");
        exit();
    }
    
    if (!UserData::isValidEmailComOrHu($email)) {
        $missingFields[] = 'forgot_email';
        $_SESSION['forgot_error'] = 'Only email addresses ending with .com or .hu are allowed.';
        setActiveForm('forgot');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }
    
    if ($newPassword !== $confirmPassword) {
        $missingFields = ['forgot_new_password', 'forgot_confirm_password'];
        $_SESSION['forgot_error'] = 'Passwords do not match.';
        setActiveForm('forgot');
        setMissingFields($missingFields);
        header("Location: index.php");
        exit();
    }

    $storedCodeHash = $_SESSION['password_reset_code_hash'] ?? '';
    $storedCodeEmail = $_SESSION['password_reset_code_email'] ?? '';
    $codeExpiresAt = (int)($_SESSION['password_reset_code_expires_at'] ?? 0);

    if ($storedCodeHash === '' || $storedCodeEmail === '' || $storedCodeEmail !== $email) {
        $missingFields = ['forgot_email', 'forgot_verification_code'];
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = 'Please request a verification code for this email first.';
        setActiveForm('forgot');
        header("Location: index.php");
        exit();
    }

    if (time() > $codeExpiresAt) {
        clearForgotVerificationSession();
        $missingFields = ['forgot_verification_code'];
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = 'Verification code expired. Please request a new code.';
        setActiveForm('forgot');
        header("Location: index.php");
        exit();
    }

    if (!hash_equals($storedCodeHash, hash('sha256', $verificationCode))) {
        $missingFields = ['forgot_verification_code'];
        setMissingFields($missingFields);
        $_SESSION['forgot_error'] = 'Invalid verification code.';
        setActiveForm('forgot');
        header("Location: index.php");
        exit();
    }
    
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
        if ($userHandler->resetPassword($email, $newPassword)) {
            clearForgotVerificationSession();
            clearMissingFields();
            $notificationSent = sendPasswordChangedEmail($email);
            $_SESSION['forgot_success'] = $notificationSent
                ? 'Password changed successfully. A confirmation email was sent.'
                : 'Password changed successfully. Confirmation email could not be sent.';
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
