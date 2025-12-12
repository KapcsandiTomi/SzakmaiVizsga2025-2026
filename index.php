<?php
session_start();

//Hibaakezelés
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'forgot' => $_SESSION['forgot_error'] ?? ''
];

//Siker megjelenitése
$success = [
    'register' => $_SESSION['register_success'] ?? '',
    'forgot' => $_SESSION['forgot_success'] ?? ''
];

//Jelenleg ürlap meghatározása
$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error){
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function showSuccess($msg){
    return !empty($msg) ? "<p class='success-message'>$msg</p>" : '';
}

function isActiveForm($formName, $activeForm){
    return $formName === $activeForm ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Login & Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">

    <!-- LOGIN -->
    <div class="form-box <?= isActiveForm('login', $activeForm);?>" id="login-form">
        <form action="login-register.php" method="post">
            <h2>Login</h2>
            <?= showError($errors['login']);?>
            <?= showSuccess($success['register']);?>
            <?= showSuccess($success['forgot']);?>
            
            <input type="email" name="email" placeholder="Type your email, here" required>
            <input type="password" name="password" placeholder="Type your password, here" required>
            <button type="submit" name="login">Login</button>

            <p>Don't have an account? 
                <a href="#" onclick="showForm('register-form'); return false;">Register</a>
            </p>

            <p><a href="#" onclick="showForm('forgot-form'); return false;">Forgot your password?</a></p>
        </form>
    </div>

    <!-- REGISTER -->
    <div class="form-box <?= isActiveForm('register', $activeForm);?>" id="register-form">
        <form action="login-register.php" method="post">
            <h2>Register</h2>
            <?= showError($errors['register']);?>

            <input type="text" name="name" placeholder="Type your name, here" required>
            <input type="email" name="email" placeholder="Type your email, here" required>
            <input type="password" name="password" placeholder="Type your password, here" required>
            <button type="submit" name="register">Register</button>

            <p>Already have an account? 
                <a href="#" onclick="showForm('login-form'); return false;">Login</a>
            </p>
        </form>
    </div>

    <!-- FORGOT PASSWORD -->
    <div class="form-box <?= isActiveForm('forgot', $activeForm);?>" id="forgot-form">
        <form action="login-register.php" method="post">
            <h2>Forgot Password</h2>

            <?= showError($errors['forgot']);?>
            <?= showSuccess($success['forgot']);?>

            <input type="email" name="email" placeholder="Enter your registered email" required>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit" name="forgot">Change Password</button>

            <p>Remembered your password? 
                <a href="#" onclick="showForm('login-form'); return false;">Login</a>
            </p>
        </form>
    </div>

</div>

<script src="javas.js"></script>
</body>
</html>
