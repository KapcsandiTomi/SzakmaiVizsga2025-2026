<?php
session_start();

require_once 'config.php';
require_once 'handler/userhandler.php';

$userCount = 0;
try {
    $userHandler = new UserHandler($conn);
    $userCount = $userHandler->getUserCount();
} catch (Exception $e) {
    $userCount = 0;
}

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'forgot' => $_SESSION['forgot_error'] ?? ''
];

$success = [
    'register' => $_SESSION['register_success'] ?? '',
    'forgot' => $_SESSION['forgot_success'] ?? ''
];

$activeForm = $_SESSION['active_form'] ?? 'login';
$missingFields = $_SESSION['missing_fields'] ?? [];

unset(
    $_SESSION['login_error'],
    $_SESSION['register_error'],
    $_SESSION['forgot_error'],
    $_SESSION['register_success'],
    $_SESSION['forgot_success'],
    $_SESSION['active_form'],
    $_SESSION['missing_fields']
);

function showError($error) {
    if (!empty($error)) {
        return "<div class='error-message'><i class='fas fa-exclamation-circle'></i> " . htmlspecialchars($error) . "</div>";
    }
    return '';
}

function showSuccess($msg) {
    if (!empty($msg)) {
        return "<div class='success-message'><i class='fas fa-check-circle'></i> " . htmlspecialchars($msg) . "</div>";
    }
    return '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}

function isActiveTab($tabName, $activeForm) {
    return $tabName === $activeForm ? 'active' : '';
}

function isMissingField($fieldName, $missingFields) {
    return in_array($fieldName, $missingFields) ? 'missing-field' : '';
}

function isMissingLoginField($fieldType, $missingFields) {
    $fieldName = 'login_' . $fieldType;
    return in_array($fieldName, $missingFields) ? 'missing-field' : '';
}

function isMissingForgotField($fieldType, $missingFields) {
    $fieldName = 'forgot_' . $fieldType;
    return in_array($fieldName, $missingFields) ? 'missing-field' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Portal - Login & Registration</title>
    <link rel="icon" href="letoles.jpg" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
        }

        .main-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            min-height: 700px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            background: white;
            position: relative;
            z-index: 2;
        }
        
        .form-side {
            flex: 1;
            padding: 0;
            background: white;
            display: flex;
            flex-direction: column;
        }
        
        .welcome-side {
            flex: 1;
            background: linear-gradient(135deg, 
                rgba(7, 185, 212, 0.9),
                rgba(34, 174, 106, 0.9),
                rgba(77, 108, 182, 0.9)
            );
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .form-tabs {
            display: flex;
            background: #f8f9fa;
            border-radius: 15px 15px 0 0;
            overflow: hidden;
            border-bottom: 2px solid rgba(7, 185, 212, 0.2);
        }
        
        .tab-btn {
            flex: 1;
            padding: 20px 15px;
            border: none;
            background: transparent;
            color: #5d6d7e;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            position: relative;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .tab-btn i {
            font-size: 18px;
            transition: transform 0.3s ease;
        }
        
        .tab-btn.active {
            color: white;
            background: linear-gradient(135deg, #07b9d4, #22ae6a);
            box-shadow: 0 4px 15px rgba(7, 185, 212, 0.3);
        }
        
        .tab-btn.active i {
            transform: scale(1.1);
        }
        
        .tab-btn:not(.active):hover {
            color: #07b9d4;
            background: rgba(7, 185, 212, 0.1);
        }
        
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: white;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        
        .form-container {
            background: white;
            border-radius: 0 0 15px 15px;
            padding: 40px 35px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            flex: 1;
        }
        
        .form-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .form-content.active {
            display: block;
        }
        
        .form-content h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            color: #07b9d4;
            font-size: 18px;
            z-index: 1;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #07b9d4;
            background: white;
            box-shadow: 0 0 0 3px rgba(7, 185, 212, 0.1);
        }
        
        .missing-field {
            border-color: #ff4757 !important;
            background: rgba(255, 71, 87, 0.05) !important;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .field-requirement {
            font-size: 12px;
            color: #ff4757;
            margin-top: 5px;
            padding-left: 10px;
            display: none;
        }
        
        .missing-field ~ .field-requirement {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .error-message, .success-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.5s ease;
        }
        
        .error-message {
            background: linear-gradient(135deg, #ff7675, #d63031);
            color: white;
            box-shadow: 0 4px 15px rgba(214, 48, 49, 0.2);
        }
        
        .success-message {
            background: linear-gradient(135deg, #55efc4, #00b894);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 184, 148, 0.2);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #07b9d4, #22ae6a);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(7, 185, 212, 0.3);
        }
        
        .submit-btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .password-requirements {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #07b9d4;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .requirement:last-child {
            margin-bottom: 0;
        }
        
        .requirement.valid {
            color: #27ae60;
        }
        
        .requirement .indicator {
            font-size: 12px;
            width: 20px;
            display: inline-block;
        }
        
        .form-options {
            margin: 20px 0;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }
        
        .checkbox-wrapper input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        
        .custom-checkbox {
            width: 22px;
            height: 22px;
            min-width: 22px;
            background: white;
            border: 2px solid #cbd5e0;
            border-radius: 6px;
            position: relative;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .checkbox-wrapper:hover .custom-checkbox {
            border-color: #07b9d4;
            background: rgba(7, 185, 212, 0.05);
        }
        
        .checkbox-wrapper input:checked ~ .custom-checkbox {
            background: linear-gradient(135deg, #07b9d4, #22ae6a);
            border-color: #07b9d4;
            animation: checkPop 0.2s ease;
        }
        
        @keyframes checkPop {
            0% { transform: scale(0.9); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .custom-checkbox::after {
            content: '';
            position: absolute;
            display: none;
            left: 7px;
            top: 3px;
            width: 6px;
            height: 11px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .checkbox-wrapper input:checked ~ .custom-checkbox::after {
            display: block;
        }
        
        .checkbox-text {
            line-height: 1.4;
            color: #5d6d7e;
            font-size: 14px;
            cursor: pointer;
        }
        
        .terms-link {
            color: #07b9d4;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .terms-link:hover {
            text-decoration: underline;
            color: #1a8f9e;
        }
        
        .welcome-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path fill="rgba(255,255,255,0.05)" d="M44.5,-74.5C56.8,-68.7,65.3,-56.8,74.4,-44.4C83.5,-32,93.2,-19.2,94.8,-5.2C96.4,8.8,90,20.9,80.5,31.8C71,42.7,58.3,52.4,44.4,58.5C30.6,64.6,15.3,67.1,0.3,66.6C-14.7,66.2,-29.3,62.7,-42.8,56.2C-56.3,49.7,-68.6,40.1,-74.8,27.4C-81,14.7,-81.1,-1.2,-77.6,-16.3C-74,-31.5,-67,-45.9,-56.3,-53.5C-45.7,-61.2,-31.3,-62.2,-18.8,-68.7C-6.3,-75.2,4.4,-87.2,17,-85.4C29.6,-83.7,44,-68.2,44.5,-74.5Z" transform="translate(100 100)"/></svg>');
            opacity: 0.3;
            animation: waveMove 20s linear infinite;
            pointer-events: none;
        }
        
        @keyframes waveMove {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-100px, -100px) rotate(360deg); }
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .logo i {
            font-size: 40px;
            animation: spin 20s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .welcome-side h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .tagline {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 50px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        
        .features {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .feature i {
            font-size: 32px;
            background: rgba(255, 255, 255, 0.1);
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .feature:hover i {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .feature h4 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .feature p {
            opacity: 0.8;
            font-size: 14px;
        }
        
        .stats {
            display: flex;
            justify-content: space-between;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .stat h3 {
            font-size: 36px;
            margin-bottom: 5px;
        }
        
        .stat p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .aqua-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a2980, #26d0ce);
            z-index: -1;
            overflow: hidden;
        }
        
        .water-bubbles {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }
        
        .bubble-1 {
            width: 80px;
            height: 80px;
            left: 10%;
            top: 20%;
            animation-delay: 0s;
        }
        
        .bubble-2 {
            width: 120px;
            height: 120px;
            right: 15%;
            top: 40%;
            animation-delay: 3s;
        }
        
        .bubble-3 {
            width: 60px;
            height: 60px;
            left: 20%;
            bottom: 30%;
            animation-delay: 6s;
        }
        
        .bubble-4 {
            width: 100px;
            height: 100px;
            right: 25%;
            bottom: 20%;
            animation-delay: 9s;
        }
        
        .bubble-5 {
            width: 70px;
            height: 70px;
            left: 50%;
            top: 10%;
            animation-delay: 12s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-100px) rotate(180deg);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 992px) {
            body {
                padding: 20px;
                align-items: flex-start;
            }
            
            .main-container {
                flex-direction: column;
                max-width: 600px;
                margin: 0 auto;
                min-height: auto;
            }
            
            .welcome-side {
                order: -1;
                padding: 40px;
                border-radius: 24px 24px 0 0;
            }
            
            .welcome-side h1 {
                font-size: 36px;
            }
            
            .form-tabs {
                border-radius: 15px 15px 0 0;
            }
            
            .form-container {
                border-radius: 0 0 15px 15px;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            
            .main-container {
                border-radius: 15px;
                min-height: auto;
            }
            
            .stats {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .welcome-side {
                padding: 30px 20px;
            }
            
            .welcome-side h1 {
                font-size: 28px;
            }
            
            .tab-btn {
                padding: 15px 10px;
                font-size: 14px;
            }
            
            .tab-btn i {
                font-size: 16px;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 24px;
            }
            
            .logo i {
                font-size: 30px;
            }
            
            .feature {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<div class="aqua-background">
    <div class="water-bubbles">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
        <div class="bubble bubble-4"></div>
        <div class="bubble bubble-5"></div>
    </div>
</div>

<div class="main-container">
    <div class="form-side">
        <div class="form-tabs">
            <button class="tab-btn <?php echo isActiveTab('login', $activeForm); ?>" data-tab="login">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </button>
            <button class="tab-btn <?php echo isActiveTab('register', $activeForm); ?>" data-tab="register">
                <i class="fas fa-user-plus"></i>
                <span>Register</span>
            </button>
            <button class="tab-btn <?php echo isActiveTab('forgot', $activeForm); ?>" data-tab="forgot">
                <i class="fas fa-key"></i>
                <span>Reset Password</span>
            </button>
        </div>
        
        <div class="form-container">
            <div id="login-content" class="form-content <?php echo isActiveForm('login', $activeForm); ?>">
                <form action="login-register.php" method="post">
                    <h2><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
                    <p class="form-subtitle">Please sign in to your account</p>
                    
                    <?php 
                    echo showError($errors['login']);
                    echo showSuccess($success['register']);
                    echo showSuccess($success['forgot']);
                    ?>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" 
                                   class="<?php echo isMissingLoginField('email', $missingFields); ?>"
                                   placeholder="Email address" required>
                        </div>
                        <div class="field-requirement">Please enter your email address</div>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" 
                                   class="<?php echo isMissingLoginField('password', $missingFields); ?>"
                                   placeholder="Password" required>
                        </div>
                        <div class="field-requirement">Please enter your password</div>
                    </div>
                    
                    <button type="submit" name="login" class="submit-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>
            </div>
            
            <div id="register-content" class="form-content <?php echo isActiveForm('register', $activeForm); ?>">
                <form action="login-register.php" method="post" id="registerForm">
                    <h2><i class="fas fa-user-plus"></i> Create Account</h2>
                    <p class="form-subtitle">Join our Aqua Community</p>
                    
                    <?php echo showError($errors['register']); ?>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="name" 
                                   class="<?php echo isMissingField('name', $missingFields); ?>"
                                   placeholder="Full name" required>
                        </div>
                        <div class="field-requirement">Please enter your full name</div>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" 
                                   class="<?php echo isMissingField('email', $missingFields); ?>"
                                   placeholder="Email address" required>
                        </div>
                        <div class="field-requirement">Please enter a valid email address</div>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="registerPassword" 
                                   class="<?php echo isMissingField('password', $missingFields); ?>"
                                   placeholder="Create password" required>
                        </div>
                        <div class="field-requirement">Password is required and must meet all requirements</div>
                        
                        <div class="password-requirements">
                            <div class="requirement" id="req-length">
                                <span class="indicator" id="ind-length">●</span>
                                <span>At least 6 characters</span>
                            </div>
                            <div class="requirement" id="req-uppercase">
                                <span class="indicator" id="ind-uppercase">●</span>
                                <span>One uppercase letter</span>
                            </div>
                            <div class="requirement" id="req-number">
                                <span class="indicator" id="ind-number">●</span>
                                <span>One number</span>
                            </div>
                            <div class="requirement" id="req-special">
                                <span class="indicator" id="ind-special">●</span>
                                <span>One special character (!@#$%^&*)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="terms" id="termsCheckbox" required>
                            <span class="custom-checkbox"></span>
                            <span class="checkbox-text">
                                I agree to the <a href="terms.php" class="terms-link" target="_blank">Terms & Conditions</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" name="register" id="registerButton" class="submit-btn">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                </form>
            </div>
            
            <div id="forgot-content" class="form-content <?php echo isActiveForm('forgot', $activeForm); ?>">
                <form action="login-register.php" method="post">
                    <h2><i class="fas fa-key"></i> Reset Password</h2>
                    <p class="form-subtitle">Enter your details to reset your password</p>
                    
                    <?php 
                    echo showError($errors['forgot']);
                    echo showSuccess($success['forgot']);
                    ?>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" 
                                   class="<?php echo isMissingForgotField('email', $missingFields); ?>"
                                   placeholder="Enter your registered email" required>
                        </div>
                        <div class="field-requirement">Please enter your registered email</div>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="new_password" 
                                   class="<?php echo isMissingForgotField('new_password', $missingFields); ?>"
                                   placeholder="New password" required>
                        </div>
                        <div class="field-requirement">Please enter a new password</div>
                    </div>
                    
                    <div class="input-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="confirm_password" 
                                   class="<?php echo isMissingForgotField('confirm_password', $missingFields); ?>"
                                   placeholder="Confirm new password" required>
                        </div>
                        <div class="field-requirement">Please confirm your new password</div>
                    </div>
                    <button type="submit" name="forgot" class="submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="welcome-side">
        <div class="welcome-content">
            <div class="logo">
                <i class="fas fa-water"></i>
                <span>Aqua Mini Shop</span>
            </div>
            <h1>Dive Into Our Webshop</h1>
            <p class="tagline">Experience the power of gaming. Built for speed, precision, and total immersion. Every move counts, every moment matters. This machine is designed for those who play to win.</p>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <h4>Secure & Safe</h4>
                        <p>Bank-level encryption for your data</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-bolt"></i>
                    <div>
                        <h4>Lightning Fast</h4>
                        <p>Instant authentication process</p>
                    </div>
                </div>
                <div class="feature">
                    <i class="fas fa-mobile-alt"></i>
                    <div>
                        <h4>Mobile Friendly</h4>
                        <p>Works perfectly on all devices</p>
                    </div>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat">
                    <h3><?php echo number_format($userCount); ?>+</h3>
                    <p>Total Users</p>
                </div>
                <div class="stat">
                    <h3>99.9%</h3>
                    <p>Uptime</p>
                </div>
                <div class="stat">
                    <h3>24/7</h3>
                    <p>Support</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const formContents = document.querySelectorAll('.form-content');
    const registerPassword = document.getElementById('registerPassword');
    const registerButton = document.getElementById('registerButton');
    const termsCheckbox = document.getElementById('termsCheckbox');
    
    function checkPasswordStrength() {
        if (!registerPassword) return true;
        
        const password = registerPassword.value;
        
        const requirements = {
            length: password.length >= 6,
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*()\-_=+{};:,<.>]/.test(password)
        };
        
        const allValid = requirements.length && requirements.uppercase && requirements.number && requirements.special;
        
        if (document.getElementById('req-length')) {
            updateRequirement('length', requirements.length);
            updateRequirement('uppercase', requirements.uppercase);
            updateRequirement('number', requirements.number);
            updateRequirement('special', requirements.special);
        }
        
        if (registerButton) {
            registerButton.disabled = !allValid;
        }
        
        if (!allValid && password.length > 0) {
            registerPassword.classList.add('missing-field');
        } else if (allValid) {
            registerPassword.classList.remove('missing-field');
        }
        
        return allValid;
    }
    
    function updateRequirement(type, isValid) {
        const reqElement = document.getElementById(`req-${type}`);
        const indicator = document.getElementById(`ind-${type}`);
        if (reqElement && indicator) {
            if (isValid) {
                reqElement.classList.add('valid');
                indicator.textContent = '✓';
                indicator.style.color = '#2ecc71';
            } else {
                reqElement.classList.remove('valid');
                indicator.textContent = '●';
                indicator.style.color = '#e74c3c';
            }
        }
    }
    
    function validateTerms() {
        if (!termsCheckbox) return true;
        const isChecked = termsCheckbox.checked;
        if (!isChecked) {
            const wrapper = termsCheckbox.closest('.checkbox-wrapper');
            if (wrapper) {
                wrapper.style.animation = 'shake 0.5s ease';
                setTimeout(() => {
                    wrapper.style.animation = '';
                }, 500);
            }
        }
        return isChecked;
    }
    
    if (registerPassword) {
        registerPassword.addEventListener('input', checkPasswordStrength);
        checkPasswordStrength();
    }
    
    if (registerButton && registerPassword) {
        registerButton.disabled = true;
    }
    
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const isPasswordValid = checkPasswordStrength();
            const isTermsValid = validateTerms();
            
            if (!isPasswordValid) {
                e.preventDefault();
                if (registerPassword) {
                    registerPassword.classList.add('missing-field');
                    setTimeout(() => {
                        registerPassword.classList.remove('missing-field');
                    }, 1000);
                }
                alert('Please meet all password requirements before registering.');
                return false;
            }
            
            if (!isTermsValid) {
                e.preventDefault();
                alert('Please accept the Terms & Conditions to continue.');
                return false;
            }
        });
    }
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            formContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === tabId + '-content') {
                    content.classList.add('active');
                    setTimeout(() => {
                        const firstInput = content.querySelector('input');
                        if (firstInput) firstInput.focus();
                    }, 100);
                }
            });
            
            if (tabId === 'register') {
                setTimeout(checkPasswordStrength, 100);
            }
        });
    });
    
    setTimeout(() => {
        const firstMissingField = document.querySelector('.missing-field');
        if (firstMissingField) {
            firstMissingField.focus();
        }
    }, 300);
    
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('missing-field') && this.value.trim() !== '') {
                this.classList.remove('missing-field');
                const requirement = this.closest('.input-group')?.querySelector('.field-requirement');
                if (requirement) {
                    requirement.style.display = 'none';
                }
            }
        });
    });
});
</script>
</body>
</html>
