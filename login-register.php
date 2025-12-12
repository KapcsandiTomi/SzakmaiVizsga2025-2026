<?php
ob_start();
session_start();
require_once 'config.php';

function valid_email_com_or_hu($email) {
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return (bool) preg_match('/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.(com|hu)$/i', $email);
}

define('MAX_FAILED_ATTEMPTS', 5);
define('LOCKOUT_MINUTES', 10);

// ================ REGISTER ================
if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passwordPlain = $_POST['password'] ?? '';

    
    if ($name === '' || $email === '' || $passwordPlain === '') {
        $_SESSION['register_error'] = 'Please fill in all fields.';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

    if (!valid_email_com_or_hu($email)) {
        $_SESSION['register_error'] = 'Only email addresses ending with .com or .hu are allowed.';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

    
    if (strlen($passwordPlain) < 6) {
        $_SESSION['register_error'] = 'Password must be at least 6 characters long.';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

    $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

   
    $stmt = $conn->prepare("SELECT email FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = 'This email is already registered.';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO `4` (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $passwordHash);
    if ($stmt->execute()) {
        $_SESSION['register_success'] = 'Registration successful. You can now log in.';
        $_SESSION['active_form'] = 'login';
    } else {
        $_SESSION['register_error'] = 'An error occurred during registration.';
        $_SESSION['active_form'] = 'register';
    }

    header("Location: index.php");
    exit();
}

// ================ LOGIN ================
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['login_error'] = 'Please enter both email and password.';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
    if (!valid_email_com_or_hu($email)) {
        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, email, password, failed_attempts, last_failed_login, is_admin FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        usleep(300000);
        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }

    $user = $result->fetch_assoc();

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
            
            $resetStmt = $conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
            $resetStmt->bind_param("i", $user['id']);
            $resetStmt->execute();
            $failed = 0;
        }
    }

   
    if (password_verify($password, $user['password'])) {
        
        $stmt = $conn->prepare("UPDATE `4` SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: fooldal.php");
        exit();
    } else {
        
        $stmt = $conn->prepare("UPDATE `4` SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();

        $_SESSION['login_error'] = 'Incorrect email or password.';
        $_SESSION['active_form'] = 'login';
        header("Location: index.php");
        exit();
    }
}

// ================ FORGOT PASSWORD ================
if (isset($_POST['forgot'])) {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($email === '' || $newPassword === '' || $confirmPassword === '') {
        $_SESSION['forgot_error'] = 'Please fill in all fields.';
        $_SESSION['active_form'] = 'forgot';
        header("Location: index.php");
        exit();
    }

    if (!valid_email_com_or_hu($email)) {
        $_SESSION['forgot_error'] = 'Only email addresses ending with .com or .hu are allowed.';
        $_SESSION['active_form'] = 'forgot';
        header("Location: index.php");
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['forgot_error'] = 'Passwords do not match.';
        $_SESSION['active_form'] = 'forgot';
        header("Location: index.php");
        exit();
    }

    if (strlen($newPassword) < 6) {
        $_SESSION['forgot_error'] = 'Password must be at least 6 characters long.';
        $_SESSION['active_form'] = 'forgot';
        header("Location: index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $_SESSION['forgot_error'] = 'This email is not registered.';
        $_SESSION['active_form'] = 'forgot';
        header("Location: index.php");
        exit();
    }

    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE `4` SET password = ?, failed_attempts = 0, last_failed_login = NULL WHERE email = ?");
    $stmt->bind_param("ss", $passwordHash, $email);
    if ($stmt->execute()) {
        $_SESSION['forgot_success'] = 'Password changed successfully. You can now log in.';
        $_SESSION['active_form'] = 'login';
    } else {
        $_SESSION['forgot_error'] = 'An error occurred while updating the password.';
        $_SESSION['active_form'] = 'forgot';
    }

    header("Location: index.php");
    exit();
}

ob_end_flush();
?>
