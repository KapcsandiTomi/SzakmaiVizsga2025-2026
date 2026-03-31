<?php
session_start();
$profile_success = $_SESSION['profile_success'] ?? '';
$profile_error = $_SESSION['profile_error'] ?? '';
$password_success = $_SESSION['password_success'] ?? '';
$password_error = $_SESSION['password_error'] ?? '';
$picture_success = $_SESSION['picture_success'] ?? '';
$picture_error = $_SESSION['picture_error'] ?? '';
$password_code_success = $_SESSION['password_code_success'] ?? '';

$isPasswordCodeStep = isset($_SESSION['password_code_verified']) && $_SESSION['password_code_verified'];
$passwordCodeEmail = $_SESSION['password_code_email'] ?? '';
$passwordCodeRemaining = 0;
if (isset($_SESSION['password_code_expires_at']) && $_SESSION['password_code_expires_at'] > time()) {
    $passwordCodeRemaining = $_SESSION['password_code_expires_at'] - time();
}

unset(
    $_SESSION['profile_success'],
    $_SESSION['profile_error'],
    $_SESSION['password_success'],
    $_SESSION['password_error'],
    $_SESSION['picture_success'],
    $_SESSION['picture_error'],
    $_SESSION['password_code_success']
);

require_once __DIR__ . '/../handler/profilehandler.php';

// Maintenance check
require_once __DIR__ . '/../handler/maintenance_check.php';

$handler = new ProfileHandler();
$user = $handler->getCurrentUser();

if (!$user) {
    header('Location: index.php');
    exit();
}

$favorites = $handler->getUserFavorites();

function normalizeFavoriteProductLink($productLink) {
    $productLink = trim((string) $productLink);

    if ($productLink === '') {
        return '#';
    }

    if (preg_match('#^(https?:)?//#i', $productLink)) {
        return $productLink;
    }

    if (strpos($productLink, '../products/') === 0) {
        return $productLink;
    }

    if (strpos($productLink, 'products/') === 0) {
        return '../' . $productLink;
    }

    if (preg_match('/^[A-Za-z0-9_-]+\.php$/', $productLink)) {
        return '../products/' . $productLink;
    }

    return $productLink;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - My Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="stylesheet" href="../assets/css/user-navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
    <style>
        .alert-message {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideIn 0.5s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .alert-message.success {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
            border-left: 5px solid #1b5e20;
        }
        
        .alert-message.error {
            background: linear-gradient(135deg, #f44336, #c62828);
            color: white;
            border-left: 5px solid #b71c1c;
        }
        
        .alert-message i {
            font-size: 20px;
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
        
        .form-error {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .input-error {
            border-color: #f44336 !important;
            background: rgba(244, 67, 54, 0.05) !important;
        }
        
        .input-success {
            border-color: #4caf50 !important;
            background: rgba(76, 175, 80, 0.05) !important;
        }
        
        .favorites-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-top: 30px;
            border: 1px solid #eaeaea;
        }
        
        .favorites-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #007bff;
        }
        
        .favorites-header h3 {
            color: #2c3e50;
            font-size: 22px;
            margin: 0;
        }
        
        .favorites-count {
            background: #007bff;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .favorite-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #eaeaea;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .favorite-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-color: #007bff;
        }
        
        .favorite-image {
            width: 100%;
            height: 150px;
            object-fit: contain;
            border-radius: 8px;
            background: white;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .favorite-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .favorite-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .btn-view {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s ease;
        }
        
        .btn-view:hover {
            background: #0056b3;
            color: white;
        }
        
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }
        
        .btn-remove:hover {
            background: #c82333;
        }
        
        .no-favorites {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .no-favorites i {
            font-size: 48px;
            color: #dee2e6;
            margin-bottom: 15px;
        }
        
        .no-favorites p {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .no-favorites .btn-browse {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 25px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .no-favorites .btn-browse:hover {
            background: #0056b3;
        }
        
        @media (max-width: 768px) {
            .favorites-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            .favorites-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
        
        .favorite-date {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container-fluid bg-light p-0">
    <div class="row gx-0 d-none d-lg-flex">
      <div class="col-lg-7 px-5 text-start">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-map-marker-alt text-primary me-2"></small>
          <small>Gardonyi Road, Isaszeg, Hungary</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center py-3">
          <small class="far fa-clock text-primary me-2"></small>
          <small>Mon - Sat: 09:00 AM - 07:00 PM</small>
        </div>
      </div>
      <div class="col-lg-5 px-5 text-end">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-phone-alt text-primary me-2"></small>
          <small>+36 70 123 4567</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center">
          <a class="btn btn-sm-square bg-white text-primary me-1" href="https://www.facebook.com/groups/780808788334912"><i class="fab fa-facebook-f"></i></a>
          <a class="btn btn-sm-square bg-white text-primary me-1" href="https://x.com/tamas_kapc343"><i class="fa-brands fa-x-twitter"></i></a>
          <a class="btn btn-sm-square bg-white text-primary me-0" href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>

<?php
$currentPage = 'profile.php';
include __DIR__ . '/includes/user-navbar.php';
?>
</div>

<!-- Profil Tartalom -->
<div class="profile-container">
    <div class="profile-content">
        <!-- Bal oldal - Profil információk -->
        <div class="profile-left">
            <div class="profile-card">
                <div class="profile-header">
                    <h1><i class="fas fa-user-circle"></i> MY PROFILE</h1>
                </div>
                
                <div class="profile-avatar">
                    <?php if ($user['profile_pic']): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                             class="avatar-image" 
                             alt="Profile Picture">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data" id="quickUploadForm" class="mt-3">
                        <input type="file" name="profile_pic" id="profile_pic" accept="image/*" class="d-none">
                        <label for="profile_pic" class="btn-change-avatar">
                            <i class="fas fa-camera me-2"></i> Change Picture
                        </label>
                    </form>
                </div>
                
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p class="email"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="favorites-count-small">
                        <i class="fas fa-heart text-danger me-1"></i>
                        <?php echo count($favorites); ?> Favourites
                    </p>
                </div>
            </div>
        </div>

        <!-- Jobb oldal - Beállítások -->
        <div class="profile-right">
            <!-- Üzenetek megjelenítése -->
            <?php if ($profile_success): ?>
                <div class="alert-message success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($profile_success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($profile_error): ?>
                <div class="alert-message error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($profile_error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($password_success): ?>
                <div class="alert-message success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($password_success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($password_error): ?>
                <div class="alert-message error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($password_error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($picture_success): ?>
                <div class="alert-message success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($picture_success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($picture_error): ?>
                <div class="alert-message error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($picture_error); ?>
                </div>
            <?php endif; ?>

            <!-- Profil adatok szerkesztése -->
            <div class="settings-section">
                <h3><i class="fas fa-user-edit me-2"></i> Edit Profile</h3>
                <form method="post" class="profile-form">
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" name="name" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" 
                                   placeholder="Enter your username" 
                                   required 
                                   class="<?php echo $profile_error ? 'input-error' : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   placeholder="Enter your email" 
                                   required 
                                   class="<?php echo $profile_error ? 'input-error' : ''; ?>">
                        </div>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-save">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
                </form>
            </div>

            <!-- Jelszó változtatás -->
            <div class="settings-section">
                <h3><i class="fas fa-lock me-2"></i> Change Password</h3>
                <?php if ($password_code_success): ?>
                    <div class="alert-message success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($password_code_success); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($password_error): ?>
                    <div class="alert-message error">
                        <i class="fas fa-times-circle"></i>
                        <span><?php echo htmlspecialchars($password_error); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!$isPasswordCodeStep): ?>
                    <!-- Step 1: Send verification code -->
                    <form method="post" class="profile-form">
                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" 
                                       name="password_email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                                       placeholder="Your email" 
                                       readonly 
                                       class="<?php echo $password_error ? 'input-error' : ''; ?>">
                            </div>
                        </div>
                        <p style="color:#64748b;font-size:13px;margin-top:8px;">We'll send a verification code to this email address.</p>
                        <button type="submit" name="send_password_code" class="btn-change-password">
                            <i class="fas fa-paper-plane me-2"></i> Send Verification Code
                        </button>
                    </form>
                <?php else: ?>
                    <!-- Step 2: Enter code and new password -->
                    <form method="post" class="profile-form">
                        <input type="hidden" name="password_email_hidden" value="<?php echo htmlspecialchars($passwordCodeEmail); ?>">
                        
                        <div class="form-group">
                            <label>Verification Code</label>
                            <p style="color:#64748b;font-size:13px;margin-bottom:8px;">Code expires in <span id="passwordCodeTimer">2:00</span></p>
                            <div class="input-with-icon">
                                <i class="fas fa-key"></i>
                                <input type="text" 
                                       name="password_verification_code" 
                                       placeholder="Enter 6-digit code" 
                                       maxlength="6" 
                                       required 
                                       class="<?php echo $password_error ? 'input-error' : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>New Password</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-key"></i>
                                    <input type="password" 
                                           name="new_password" 
                                           placeholder="Enter new password" 
                                           required 
                                           class="<?php echo $password_error ? 'input-error' : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-key"></i>
                                    <input type="password" 
                                           name="confirm_password" 
                                           placeholder="Confirm new password" 
                                           required 
                                           class="<?php echo $password_error ? 'input-error' : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <button type="submit" name="change_password_verify" class="btn-change-password">
                                <i class="fas fa-check me-2"></i> Change Password
                            </button>
                            <button type="submit" name="cancel_password_code" class="btn-change-password" style="background:#6b7280;">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                        </div>
                    </form>
                    
                    <script>
                        (function() {
                            let remaining = <?php echo (int)$passwordCodeRemaining; ?>;
                            const timerEl = document.getElementById('passwordCodeTimer');
                            
                            function updateTimer() {
                                const mins = Math.floor(remaining / 60);
                                const secs = remaining % 60;
                                timerEl.textContent = mins + ':' + (secs < 10 ? '0' : '') + secs;
                                
                                if (remaining > 0) {
                                    remaining--;
                                    setTimeout(updateTimer, 1000);
                                } else {
                                    timerEl.textContent = 'Expired';
                                }
                            }
                            
                            updateTimer();
                        })();
                    </script>
                <?php endif; ?>
            </div>

            <!-- Profilkép feltöltés -->
            <div class="settings-section">
                <h3><i class="fas fa-image me-2"></i> Profile Picture</h3>
                <form method="post" enctype="multipart/form-data" class="profile-form">
                    <div class="upload-area <?php echo $picture_error ? 'input-error' : ($picture_success ? 'input-success' : ''); ?>">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag & drop your image here or <span class="browse-text">browse</span></p>
                        <small>Max file size: 5MB. Allowed: JPG, PNG, GIF</small>
                        <input type="file" name="profile_pic" accept="image/*" required>
                    </div>
                    <button type="submit" name="change_picture" class="btn-upload">
                        <i class="fas fa-upload me-2"></i> Upload Picture
                    </button>
                </form>
            </div>
            
            <!-- Kedvencek szekció -->
            <div class="favorites-section">
                <div class="favorites-header">
                    <h3><i class="fas fa-heart text-danger me-2"></i> MY FAVOURITES</h3>
                    <div class="favorites-count">
                        <?php echo count($favorites); ?> items
                    </div>
                </div>
                
                <?php if (count($favorites) > 0): ?>
                    <div class="favorites-grid">
                        <?php foreach ($favorites as $favorite): ?>
                            <div class="favorite-item">
                                <img src="<?php echo htmlspecialchars($favorite['product_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($favorite['product_name']); ?>" 
                                     class="favorite-image">
                                
                                <div class="favorite-name">
                                    <?php echo htmlspecialchars($favorite['product_name']); ?>
                                </div>
                                
                                <div class="favorite-date">
                                    Added: <?php echo date('Y-m-d', strtotime($favorite['created_at'])); ?>
                                </div>
                                
                                <div class="favorite-actions">
                                    <a href="<?php echo htmlspecialchars(normalizeFavoriteProductLink($favorite['product_link'])); ?>" 
                                       class="btn-view">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-favorites">
                        <i class="fas fa-heart"></i>
                        <p>You haven't added any favorites yet.</p>
                        <p>Browse our products and click the heart icon to add items to your favorites!</p>
                        <a href="products.php" class="btn-browse">
                            <i class="fas fa-shopping-bag me-2"></i> Browse Products
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer>
  <div class="container">
    <div class="footer-col">
      <h2>OUR MOTTO<div class="underline"><span></span></div></h2>
      <p class="footer-para">Innovation starts here – with machines designed for creators and professionals who demand more. Whether you're editing, rendering, or building the future, our systems are ready to keep up with your vision.</p>
    </div>
    <div class="footer-col">
      <h3 class="text-office">
        Office<div class="underline"><span></span></div>
      </h3>
      <p>Street No 8</p><p>Gárdonyi Géza</p><p>Isaszeg, 2117, Hungary</p>
      <p class="email">aquaminishop@gmail.com</p>
      <p class="phone">+36 70 123 4567</p>
    </div>
    <div class="footer-col">
      <h3>Menu<div class="underline"><span></span></div></h3>
      <ul>
        <li><a href="fooldal.php">Home</a></li><li><a href="products.php">Products</a></li><li><a href="about.php">About us</a></li><li><a href="faq.php">FAQ</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h2>FOLLOW US<div class="underline"><span></span></div></h2>
      <div class="social-icons">
        <a href="https://www.facebook.com/groups/780808788334912"><i class="fab fa-facebook-f"></i></a>
        <a href="https://x.com/tamas_kapc343"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
</footer>

<script>
document.getElementById('profile_pic').addEventListener('change', function() {
    if (this.files.length > 0) {
        const form = document.createElement('form');
        form.method = 'post';
        form.enctype = 'multipart/form-data';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'change_picture';
        input.value = '1';
        form.appendChild(input);
        
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'profile_pic';
        fileInput.files = this.files;
        form.appendChild(fileInput);
        
        document.body.appendChild(form);
        form.submit();
    }
});
document.querySelector('.browse-text').addEventListener('click', function() {
    document.querySelector('input[name="profile_pic"]').click();
});

setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-message');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>

<script src="../assets/js/javas.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/chat.js"></script>
</body>
</html>

