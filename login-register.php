<?php
ob_start();
session_start();

require_once 'config.php';
require_once 'handler/userhandler.php';

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
if (isset($_POST['forgot'])) {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
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
