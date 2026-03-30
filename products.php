<?php
// ====================
// MUNKAMENET INDÍTÁSA
// ====================
session_start();

// HA NINCS EMAIL A SESSIONBEN → FŐOLDAL
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}

// ====================
// ADATBÁZIS KAPCSOLAT
// ====================
require_once 'config.php';

// ====================
// FELHASZNÁLÓ AZONOSÍTÁSA
// ====================
$stmt = $conn->prepare(
    "SELECT id FROM `4` WHERE email = :email"
);
$stmt->execute([
    'email' => $_SESSION['email']
]);

$user_id = $stmt->fetchColumn();
if (!$user_id) {
    header('Location: index.php');
    exit;
}

// ====================
// ÜZENETEK
// ====================
$message = '';
$message_type = '';

// ====================
// KEDVENC HOZZÁADÁS / TÖRLÉS
// ====================
if (isset($_POST['toggle_favorite'])) {

    $product_id    = $_POST['product_id'] ?? '';
    $product_name  = $_POST['product_name'] ?? '';
    $product_image = $_POST['product_image'] ?? '';
    $product_link  = $_POST['product_link'] ?? '';

    // Ellenőrzés: már kedvenc?
    $stmt = $conn->prepare(
        "SELECT id FROM favorites
         WHERE user_id = :user_id AND product_id = :product_id"
    );
    $stmt->execute([
        'user_id'    => $user_id,
        'product_id' => $product_id
    ]);

    if ($stmt->fetch()) {
        // TÖRLÉS
        $stmt = $conn->prepare(
            "DELETE FROM favorites
             WHERE user_id = :user_id AND product_id = :product_id"
        );
        $stmt->execute([
            'user_id'    => $user_id,
            'product_id' => $product_id
        ]);

        $message = 'Product removed from favorites!';
        $message_type = 'success';

    } else {
        // HOZZÁADÁS
        $stmt = $conn->prepare(
            "INSERT INTO favorites
            (user_id, product_id, product_name, product_image, product_link)
            VALUES (:user_id, :product_id, :product_name, :product_image, :product_link)"
        );

        $stmt->execute([
            'user_id'       => $user_id,
            'product_id'    => $product_id,
            'product_name'  => $product_name,
            'product_image' => $product_image,
            'product_link'  => $product_link
        ]);

        $message = 'Product added to favorites!';
        $message_type = 'success';
    }

    // SESSION KEDVENCEK FRISSÍTÉSE
    $stmt = $conn->prepare(
        "SELECT product_id FROM favorites WHERE user_id = :user_id"
    );
    $stmt->execute(['user_id' => $user_id]);

    $_SESSION['favorites'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// ====================
// KEDVENCEK BETÖLTÉSE SESSIONBE (HA NINCS)
// ====================
if (!isset($_SESSION['favorites'])) {
    $stmt = $conn->prepare(
        "SELECT product_id FROM favorites WHERE user_id = :user_id"
    );
    $stmt->execute(['user_id' => $user_id]);

    $_SESSION['favorites'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Products</title>
    <link rel="stylesheet" href="products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="letoles.jpg" type="image/png">
    <style>
        .product-item {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .favorite-form {
            margin-top: 10px;
            width: 100%;
        }
        
        .favorite-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            color: white;
            width: 100%;
            font-size: 15px;
            margin-top: 8px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .favorite-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .favorite-btn:hover:before {
            left: 100%;
        }
        
        .favorite-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .favorite-btn.active {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            box-shadow: 0 4px 15px rgba(253, 160, 133, 0.3);
        }
        
        .favorite-btn.active:hover {
            box-shadow: 0 6px 20px rgba(253, 160, 133, 0.4);
        }
        
        .favorite-btn.active .fa-heart {
            color: #ff4757;
            animation: heartbeat 1.5s ease infinite;
        }
        
        .favorite-btn .fa-heart {
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #eaeaea;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .contentBx {
            flex-grow: 1;
            padding: 20px;
        }
        
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            transform: translateX(400px);
            transition: transform 0.4s ease;
            border-left: 4px solid #4CAF50;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            border-left-color: #4CAF50;
        }
        
        .notification.error {
            border-left-color: #f44336;
        }
        
        .notification-icon {
            font-size: 24px;
        }
        
        .notification.success .notification-icon {
            color: #4CAF50;
        }
        
        .notification.error .notification-icon {
            color: #f44336;
        }
        
        @keyframes heartbeat {
            0% { transform: scale(1); }
            5% { transform: scale(1.1); }
            10% { transform: scale(1); }
            15% { transform: scale(1.1); }
            20% { transform: scale(1); }
            100% { transform: scale(1); }
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
                <a class="btn btn-sm-square bg-white text-primary me-1" href="https://x.com/tamas_kapc343"><i class="fab fa-twitter"></i></a>
                <a class="btn btn-sm-square bg-white text-primary me-0" href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
    <a href="fooldal.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <div class="logo-4">
            <span class="aqua">AQUA</span>
            <span class="mini-shop">MINI SHOP</span>
        </div>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="fooldal.php" class="nav-item nav-link active">Home</a>
            <a href="about.php" class="nav-item nav-link">About US</a>
            <a href="products.php" class="nav-item nav-link">Products</a>
            <a href="rateus.php" class="nav-item nav-link">RATE US</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="pagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    CONTACT
                </a>
                <ul class="dropdown-menu fade-up m-0" aria-labelledby="pagesDropdown">
                    <li><a href="writeUs.php" class="dropdown-item">WRITE US</a></li>
                    <li><a href="faq.php" class="dropdown-item">FAQ</a></li>
                </ul>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    MY ACCOUNT
                </a>
                <ul class="dropdown-menu fade-up m-0" aria-labelledby="accountDropdown">
                    <li><a href="profile.php" class="dropdown-item">MY PROFILE</a></li>
                    <li><a href="logout.php" class="dropdown-item">LOGOUT</a></li>
                    <li><a href="myorder.php" class="dropdown-item">MY ORDERS</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<main>
    <h1 class="tech-builder-link">
        <a href="/Szak/pc_builder/">
            <span class="tech-text">🎮 BUILD YOUR ULTIMATE PC</span>
            <span class="glow">CLICK TO START</span>
        </a>
    </h1>

    <div class="search-section">
        <div class="search-box">
            <input id="searchInput" type="text" placeholder="Search for products...">
            <button id="searchButton"><i class="fa fa-search"></i></button>
            <ul id="suggestions" class="suggestions-list"></ul>
        </div>
    </div>

    <br>
    <div id="notice-products" style="background-color: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeeba; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;">
        ⚠️ Important: Only select our products 🛒 and make sure to read the user guide 📖 before ordering. This ensures proper use and helps us serve you better 💻. Thank you 🙏!
    </div>

    <section id="headphones" class="category-header">
        <div class="category-icon">🎧</div>
        <h1 class="category-title">HEADPHONES</h1>
        <div class="category-underline"></div>
        <p class="category-desc">Immerse yourself in superior sound...</p>
    </section>

    <section id="logitech" class="category-header">
        <h1 class="category-title">Logitech section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitech.com/content/dam/gaming/en/products/pro-x/pro-headset-gallery-1.png" alt="Logitech X PRO Wired Headset">
                </div>
                <div class="contentBx">
                    <h2>Logitech PRO X</h2>
                    <a href="products/logitechProX.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_pro_x">
                <input type="hidden" name="product_name" value="Logitech PRO X Wired Headset">
                <input type="hidden" name="product_image" value="https://resource.logitech.com/content/dam/gaming/en/products/pro-x/pro-headset-gallery-1.png">
                <input type="hidden" name="product_link" value="products/logitechProX.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_pro_x', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/a20-x-pdp/gallery/a20x-3qtr-front-with-receiver-black-gallery-1-new.png" alt="Logitech Astro A20 X">
                </div>
                <div class="contentBx">
                    <h2>Logitech Astro A20 X</h2>
                    <a href="products/logitechAstro.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_astro_a20">
                <input type="hidden" name="product_name" value="Logitech Astro A20 X">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/a20-x-pdp/gallery/a20x-3qtr-front-with-receiver-black-gallery-1-new.png">
                <input type="hidden" name="product_link" value="products/logitechAstro.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_astro_a20', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/g733/gallery/g733-black-gallery-3.png" alt="Logitech Pro G733">
                </div>
                <div class="contentBx">
                    <h2>Logitech PRO Pro G733</h2>
                    <a href="products/logitechG733.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_g733">
                <input type="hidden" name="product_name" value="Logitech PRO Pro G733">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/g733/gallery/g733-black-gallery-3.png">
                <input type="hidden" name="product_link" value="products/logitechG733.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_g733', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/audio/g735-wireless-headset/gallery/2025/g735-3qtr-front-left-angle-gallery-1.png?v=1" alt="Logitech Pro G735">
                </div>
                <div class="contentBx">
                    <h2>Logitech PRO Pro G735</h2>
                    <a href="products/logitechG735.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_g735">
                <input type="hidden" name="product_name" value="Logitech PRO Pro G735">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/audio/g735-wireless-headset/gallery/2025/g735-3qtr-front-left-angle-gallery-1.png?v=1">
                <input type="hidden" name="product_link" value="products/logitechG735.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_g735', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-2-lightspeed/gallery/gallery-1-pro-x-2-lightspeed-gaming-headset-black.png" alt="Logitech X PRO 2 Wired Headset">
                </div>
                <div class="contentBx">
                    <h2>Logitech PRO X 2</h2>
                    <a href="products/logitechProX2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_pro_x2">
                <input type="hidden" name="product_name" value="Logitech PRO X 2">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-2-lightspeed/gallery/gallery-1-pro-x-2-lightspeed-gaming-headset-black.png">
                <input type="hidden" name="product_link" value="products/logitechProX2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_pro_x2', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="asus" class="category-header">
        <h1 class="category-title">ASUS section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://dlcdnwebimgs.asus.com/gain/F223AA9E-DC85-421B-990A-EA07C27D19E5/w717/h525/fwebp" alt="ROG Delta S Wireless">
                </div>
                <div class="contentBx">
                    <h2>ASUS ROG Delta S</h2>
                    <a href="products/asusrogdelta.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="asus_rog_delta_s">
                <input type="hidden" name="product_name" value="ASUS ROG Delta S Wireless">
                <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/F223AA9E-DC85-421B-990A-EA07C27D19E5/w717/h525/fwebp">
                <input type="hidden" name="product_link" value="products/asusrogdelta.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('asus_rog_delta_s', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://dlcdnwebimgs.asus.com/gain/46BDC9FB-449D-4549-BADE-1E5A4EACA111/w717/h525/fwebp" alt="ROG Delta II">
                </div>
                <div class="contentBx">
                    <h2>ASUS ROG Delta II</h2>
                    <a href="products/asusrogdelta2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="asus_rog_delta_ii">
                <input type="hidden" name="product_name" value="ASUS ROG Delta II">
                <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/46BDC9FB-449D-4549-BADE-1E5A4EACA111/w717/h525/fwebp">
                <input type="hidden" name="product_link" value="products/asusrogdelta2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('asus_rog_delta_ii', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://dlcdnwebimgs.asus.com/gain/B43F05B8-6236-4225-8FB5-107B414D6104/w717/h525/fwebp" alt="ROG Strix Go Core Moonlight White">
                </div>
                <div class="contentBx">
                    <h2>ASUS ROG Strix</h2>
                    <a href="products/asusrogstrixgocore.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="asus_rog_strix_go_core">
                <input type="hidden" name="product_name" value="ASUS ROG Strix Go Core">
                <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/B43F05B8-6236-4225-8FB5-107B414D6104/w717/h525/fwebp">
                <input type="hidden" name="product_link" value="products/asusrogstrixgocore.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('asus_rog_strix_go_core', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://dlcdnwebimgs.asus.com/gain/0B6F7E0D-DE7D-48BC-BBB1-75F35E2D5CC9/w717/h525/fwebp" alt="ROG Fusion II 500">
                </div>
                <div class="contentBx">
                    <h2>ASUS ROG Fusion II</h2>
                    <a href="products/asusrogfushion.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="asus_rog_fusion_ii_500">
                <input type="hidden" name="product_name" value="ASUS ROG Fusion II 500">
                <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/0B6F7E0D-DE7D-48BC-BBB1-75F35E2D5CC9/w717/h525/fwebp">
                <input type="hidden" name="product_link" value="products/asusrogfushion.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('asus_rog_fusion_ii_500', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://dlcdnwebimgs.asus.com/gain/2FAA2882-5564-4A2A-80DA-C76BA236E933/w717/h525/fwebp" alt="ROG STRIX GO CORE">
                </div>
                <div class="contentBx">
                    <h2>ASUS ROG STRIX</h2>
                    <a href="products/asusstrix.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="asus_rog_strix">
                <input type="hidden" name="product_name" value="ASUS ROG STRIX">
                <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/2FAA2882-5564-4A2A-80DA-C76BA236E933/w717/h525/fwebp">
                <input type="hidden" name="product_link" value="products/asusstrix.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('asus_rog_strix', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="keyboard" class="category-header">
        <div class="category-icon">⌨️</div>
        <h1 class="category-title">KEYBOARD</h1>
        <div class="category-underline"></div>
        <p class="category-desc">
            Experience precision, comfort, and control at your fingertips. Explore our keyboards crafted for smooth typing, exceptional durability, and responsive performance—ideal for work, gaming, or everyday use.
        </p>
    </section>

    <section id="white-shark" class="category-header">
        <h1 class="category-title">White Shark section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9fa1972f-906a-4ac6-bd7c-d0ddca758916/conversions/EXCALIBUR-B-%281%29-thumb.png" alt="EXCALIBUR Black">
                </div>
                <div class="contentBx">
                    <h2>EXCALIBUR Black</h2>
                    <a href="products/keyboard1.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="white_shark_excalibur_black">
                <input type="hidden" name="product_name" value="EXCALIBUR Black">
                <input type="hidden" name="product_image" value="https://smit-electronic.s3.eu-central-1.amazonaws.com/9fa1972f-906a-4ac6-bd7c-d0ddca758916/conversions/EXCALIBUR-B-%281%29-thumb.png">
                <input type="hidden" name="product_link" value="products/keyboard1.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('white_shark_excalibur_black', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9f7f98c4-9f22-41a6-9c4f-d681bb832271/conversions/Wakizashi-2-bijela-HR-Red.sw-%281%29-thumb.png" alt="WAKIZASHI 2 HR White">
                </div>
                <div class="contentBx">
                    <h2>WAKIZASHI 2 HR White</h2>
                    <a href="products/keyboard2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="white_shark_wakizashi_2_hr_white">
                <input type="hidden" name="product_name" value="WAKIZASHI 2 HR White">
                <input type="hidden" name="product_image" value="https://smit-electronic.s3.eu-central-1.amazonaws.com/9f7f98c4-9f22-41a6-9c4f-d681bb832271/conversions/Wakizashi-2-bijela-HR-Red.sw-%281%29-thumb.png">
                <input type="hidden" name="product_link" value="products/keyboard2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('white_shark_wakizashi_2_hr_white', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9ddf293b-9d73-40db-8f04-dc867477c675/conversions/NAGAMAKI-W-US-RED.SW-%281%29-thumb.png" alt="NAGAMAKI WHITE US">
                </div>
                <div class="contentBx">
                    <h2>NAGAMAKI WHITE US</h2>
                    <a href="products/keyboard3.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="white_shark_nagamaki_white_us">
                <input type="hidden" name="product_name" value="NAGAMAKI WHITE US">
                <input type="hidden" name="product_image" value="https://smit-electronic.s3.eu-central-1.amazonaws.com/9ddf293b-9d73-40db-8f04-dc867477c675/conversions/NAGAMAKI-W-US-RED.SW-%281%29-thumb.png">
                <input type="hidden" name="product_link" value="products/keyboard3.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('white_shark_nagamaki_white_us', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e495f5d-a455-41c5-8485-a489fc9d4a0b/conversions/Wakizashiv2-US-Gray-Black-Red.sw-%281%29-thumb.png" alt="WAKIZASHI 2 US Gray/Black">
                </div>
                <div class="contentBx">
                    <h2>WAKIZASHI 2 US</h2>
                    <a href="products/keyboard4.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="white_shark_wakizashi_2_us_gray_black">
                <input type="hidden" name="product_name" value="WAKIZASHI 2 US Gray/Black">
                <input type="hidden" name="product_image" value="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e495f5d-a455-41c5-8485-a489fc9d4a0b/conversions/Wakizashiv2-US-Gray-Black-Red.sw-%281%29-thumb.png">
                <input type="hidden" name="product_link" value="products/keyboard4.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('white_shark_wakizashi_2_us_gray_black', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e3fc2de-9013-4019-bb62-75e75684d856/conversions/SHINOBI-2-CZSK-White-Red-Switch-%281%29-thumb.png" alt="SHINOBI 2 White CZ/SK">
                </div>
                <div class="contentBx">
                    <h2>SHINOBI 2 White CZ/SK</h2>
                    <a href="products/keyboard5.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="white_shark_shinobi_2_white_cz_sk">
                <input type="hidden" name="product_name" value="SHINOBI 2 White CZ/SK">
                <input type="hidden" name="product_image" value="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e3fc2de-9013-4019-bb62-75e75684d856/conversions/SHINOBI-2-CZSK-White-Red-Switch-%281%29-thumb.png">
                <input type="hidden" name="product_link" value="products/keyboard5.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('white_shark_shinobi_2_white_cz_sk', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="reddragon" class="category-header">
        <h1 class="category-title">reddragon section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://redragonshop.com/cdn/shop/files/RedragonANTONIUMK745PROKeyboard_2.png?v=1761211643&width=713" alt="ANTONIUM K745 PRO">
                </div>
                <div class="contentBx">
                    <h2>ANTONIUM K745 PRO</h2>
                    <a href="products/keyboard6.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="reddragon_antonium_k745_pro">
                <input type="hidden" name="product_name" value="ANTONIUM K745 PRO">
                <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonANTONIUMK745PROKeyboard_2.png?v=1761211643&width=713">
                <input type="hidden" name="product_link" value="products/keyboard6.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('reddragon_antonium_k745_pro', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://redragonshop.com/cdn/shop/files/RedragonFIZZK61760RapidTriggerMagneticSwitchGamingKeyboard_1_1.png?v=1762463308&width=713" alt="FIZZ K617 (Magnetic Hall Effect Keyboard)">
                </div>
                <div class="contentBx">
                    <h2>FIZZ K617</h2>
                    <a href="products/keyboard7.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="reddragon_fizz_k617">
                <input type="hidden" name="product_name" value="FIZZ K617">
                <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonFIZZK61760RapidTriggerMagneticSwitchGamingKeyboard_1_1.png?v=1762463308&width=713">
                <input type="hidden" name="product_link" value="products/keyboard7.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('reddragon_fizz_k617', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://redragonshop.com/cdn/shop/files/RedragonEISAK686HERapidTriggerGamingKeyboard_1.png?v=1760435423&width=713" alt="EISA K686 HE Rapid Trigger Gaming Keyboard">
                </div>
                <div class="contentBx">
                    <h2>EISA K686 HE Rapid</h2>
                    <a href="products/keyboard8.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="reddragon_eisa_k686_he">
                <input type="hidden" name="product_name" value="EISA K686 HE Rapid Trigger">
                <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonEISAK686HERapidTriggerGamingKeyboard_1.png?v=1760435423&width=713">
                <input type="hidden" name="product_link" value="products/keyboard8.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('reddragon_eisa_k686_he', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://redragonshop.com/cdn/shop/files/RedragonEISAK686PRO98KeysWirelessGasketRGBGamingKeyboard_1.png?v=1762463457&width=713" alt="EISA K686 PRO SE Anime Keyboard">
                </div>
                <div class="contentBx">
                    <h2>EISA K686 PRO SE</h2>
                    <a href="products/keyboard9.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="reddragon_eisa_k686_pro_se">
                <input type="hidden" name="product_name" value="EISA K686 PRO SE">
                <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonEISAK686PRO98KeysWirelessGasketRGBGamingKeyboard_1.png?v=1762463457&width=713">
                <input type="hidden" name="product_link" value="products/keyboard9.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('reddragon_eisa_k686_pro_se', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://redragonshop.com/cdn/shop/files/RedragonARTEMISK719PROGraffitiKeyboard_1.png?v=1762848339&width=713" alt="ARTEMIS K719 PRO Graffiti Keyboard">
                </div>
                <div class="contentBx">
                    <h2>ARTEMIS K719 PRO</h2>
                    <a href="products/keyboard10.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="reddragon_artemis_k719_pro">
                <input type="hidden" name="product_name" value="ARTEMIS K719 PRO">
                <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonARTEMISK719PROGraffitiKeyboard_1.png?v=1762848339&width=713">
                <input type="hidden" name="product_link" value="products/keyboard10.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('reddragon_artemis_k719_pro', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="monitor" class="category-header">
        <div class="category-icon">🖥️</div>
        <h1 class="category-title">MONITORS</h1>
        <div class="category-underline"></div>
        <p class="category-desc">
            Discover stunning clarity, vibrant colors, and smooth performance. Explore our monitors designed for work, gaming, and everyday use—delivering an immersive visual experience for every task.
        </p>
    </section>

    <section id="samsung" class="category-header">
        <h1 class="category-title">Samsung section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fg510euxen/gallery/hu-odyssey-g5-g51f-ls32fg510euxen-548700337?$Q90_1920_1280_F_PNG$" alt="32 Odyssey G5 G51F QHD 180Hz gaming monitor">
                </div>
                <div class="contentBx">
                    <h2>Odyssey 180Hz</h2>
                    <a href="products/monitor1.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="samsung_odyssey_g5_180hz">
                <input type="hidden" name="product_name" value="Odyssey 180Hz gaming monitor">
                <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fg510euxen/gallery/hu-odyssey-g5-g51f-ls32fg510euxen-548700337?$Q90_1920_1280_F_PNG$">
                <input type="hidden" name="product_link" value="products/monitor1.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('samsung_odyssey_g5_180hz', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fm700uuxdu/gallery/hu-smart-m7-32m70f-black-ls32fm700uuxdu-547197500?$Q90_1920_1280_F_PNG$" alt="32 Vision AI Smart monitor M7 M70F 4K - LS32FM700UUXDU">
                </div>
                <div class="contentBx">
                    <h2>"32" Vision AI Smart</h2>
                    <a href="products/monitor2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="samsung_vision_ai_m7_32">
                <input type="hidden" name="product_name" value="32' Vision AI Smart monitor M7">
                <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fm700uuxdu/gallery/hu-smart-m7-32m70f-black-ls32fm700uuxdu-547197500?$Q90_1920_1280_F_PNG$">
                <input type="hidden" name="product_link" value="products/monitor2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('samsung_vision_ai_m7_32', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls27dg602suxen/gallery/hu-odyssey-oled-g6-g60sd-ls27dg602suxen-541135297?$Q90_1920_1280_F_PNG$" alt="27 Odyssey OLED G6 G60SD QHD 360 Hz gaming monitor - LS27DG602SUXEN">
                </div>
                <div class="contentBx">
                    <h2>"27" Odyssey OLED G6</h2>
                    <a href="products/monitor3.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="samsung_odyssey_oled_g6_27">
                <input type="hidden" name="product_name" value="27' Odyssey OLED G6">
                <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls27dg602suxen/gallery/hu-odyssey-oled-g6-g60sd-ls27dg602suxen-541135297?$Q90_1920_1280_F_PNG$">
                <input type="hidden" name="product_link" value="products/monitor3.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('samsung_odyssey_oled_g6_27', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls37d800uauxen/gallery/hu-viewfinity-s8-37s80ud-ls37d800uauxen-546091975?$Q90_1920_1280_F_PNG$" alt="37 ViewFinity S8 S80UD UHD monitor - LS37D800UAUXEN">
                </div>
                <div class="contentBx">
                    <h2>"37" ViewFinity</h2>
                    <a href="products/monitor4.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="samsung_viewfinity_s8_37">
                <input type="hidden" name="product_name" value="37' ViewFinity S8 S80UD">
                <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls37d800uauxen/gallery/hu-viewfinity-s8-37s80ud-ls37d800uauxen-546091975?$Q90_1920_1280_F_PNG$">
                <input type="hidden" name="product_link" value="products/monitor4.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('samsung_viewfinity_s8_37', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls55cg970nuxdu/gallery/hu-odyssey-ark-g97nc-ls55cg970nuxdu-538036806?$Q90_1920_1280_F_PNG$" alt="55 Odyssey Ark G9 G97NC UHD 165 Hz ívelt gaming monitor - LS55CG970NUXDU">
                </div>
                <div class="contentBx">
                    <h2>"55" Odyssey Ark</h2>
                    <a href="products/monitor5.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="samsung_odyssey_ark_g9_55">
                <input type="hidden" name="product_name" value="55' Odyssey Ark G9 G97NC">
                <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls55cg970nuxdu/gallery/hu-odyssey-ark-g9-g97nc-165-hz-ivelt-gaming-monitor-ls55cg970nuxdu-538036806?$Q90_1920_1280_F_PNG$">
                <input type="hidden" name="product_link" value="products/monitor5.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('samsung_odyssey_ark_g9_55', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="msi" class="category-header">
        <h1 class="category-title">MSI section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MPG/271QR-QD-OLED-X50/kv-pd.webp" alt="MPG 271QR QD-OLED X50">
                </div>
                <div class="contentBx">
                    <h2>MPG 271QR QD-OLED</h2>
                    <a href="products/monitor6.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="msi_mpg_271qr_qd_oled_x50">
                <input type="hidden" name="product_name" value="MPG 271QR QD-OLED X50">
                <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MPG/271QR-QD-OLED-X50/kv-pd.webp">
                <input type="hidden" name="product_link" value="products/monitor6.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('msi_mpg_271qr_qd_oled_x50', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/mag-274qpf-x32/kv-pd.png" alt="MAG 274QPF X32">
                </div>
                <div class="contentBx">
                    <h2>MAG 274QPF X32</h2>
                    <a href="products/monitor7.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="msi_mag_274qpf_x32">
                <input type="hidden" name="product_name" value="MAG 274QPF X32">
                <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/mag-274qpf-x32/kv-pd.png">
                <input type="hidden" name="product_link" value="products/monitor7.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('msi_mag_274qpf_x32', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-275QF-E20/msi-mag-275qf-e20-kv.png" alt="MAG 275QF E20">
                </div>
                <div class="contentBx">
                    <h2>MAG 275QF E20</h2>
                    <a href="products/monitor8.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="msi_mag_275qf_e20">
                <input type="hidden" name="product_name" value="MAG 275QF E20">
                <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-275QF-E20/msi-mag-275qf-e20-kv.png">
                <input type="hidden" name="product_link" value="products/monitor8.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('msi_mag_275qf_e20', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QPF-E20/kv/msi-mag-272qpf-e20-kv.png" alt="MAG 272QPF E20">
                </div>
                <div class="contentBx">
                    <h2>MAG 272QPF E20</h2>
                    <a href="products/monitor9.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="msi_mag_272qpf_e20">
                <input type="hidden" name="product_name" value="MAG 272QPF E20">
                <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QPF-E20/kv/msi-mag-272qpf-e20-kv.png">
                <input type="hidden" name="product_link" value="products/monitor9.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('msi_mag_272qpf_e20', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QP-QD-OLED-X24/mag-272qp-qd-oled-x24-kv.webp" alt="MAG 272QP QD-OLED X24">
                </div>
                <div class="contentBx">
                    <h2>MAG 272QP QD-OLED</h2>
                    <a href="products/monitor10.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="msi_mag_272qp_qd_oled_x24">
                <input type="hidden" name="product_name" value="MAG 272QP QD-OLED X24">
                <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QP-QD-OLED-X24/mag-272qp-qd-oled-x24-kv.webp">
                <input type="hidden" name="product_link" value="products/monitor10.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('msi_mag_272qp_qd_oled_x24', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="mouse" class="category-header">
        <div class="category-icon">🖱️</div>
        <h1 class="category-title">MOUSE</h1>
        <div class="category-underline"></div>
        <p class="category-desc">
            Discover precision, comfort, and responsive control. Explore our mice designed for work, gaming, and everyday use—delivering accuracy and comfort for every movement.
        </p>
    </section>

    <section id="razer-mouse" class="category-header">
        <h1 class="category-title">RAZER MOUSE SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://assets3.razerzone.com/7QeO9se0LbDhoHwFI-3juHOVEzA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4e%2Fh5e%2F9765618221086%2Fviper-v3-pro-white-500x500.png" alt="Razer Viper V3 Pro">
                </div>
                <div class="contentBx">
                    <h2>Razer Viper V3 Pro</h2>
                    <a href="products/mice1.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="razer_viper_v3_pro">
                <input type="hidden" name="product_name" value="Razer Viper V3 Pro">
                <input type="hidden" name="product_image" value="https://assets3.razerzone.com/7QeO9se0LbDhoHwFI-3juHOVEzA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4e%2Fh5e%2F9765618221086%2Fviper-v3-pro-white-500x500.png">
                <input type="hidden" name="product_link" value="products/mice1.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('razer_viper_v3_pro', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://assets3.razerzone.com/9FvPlvujlrxafg7AbznLiEuAncE=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh01%2Fhf3%2F9926511951902%2Fdeathadder-v4-pro-black-500x500.png" alt="Razer DeathAdder V4 Pro">
                </div>
                <div class="contentBx">
                    <h2>Razer DeathAdder</h2>
                    <a href="products/mice2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="razer_deathadder_v4_pro">
                <input type="hidden" name="product_name" value="Razer DeathAdder V4 Pro">
                <input type="hidden" name="product_image" value="https://assets3.razerzone.com/9FvPlvujlrxafg7AbznLiEuAncE=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh01%2Fhf3%2F9926511951902%2Fdeathadder-v4-pro-black-500x500.png">
                <input type="hidden" name="product_link" value="products/mice2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('razer_deathadder_v4_pro', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://assets3.razerzone.com/y265A8on-spu30uzfYMFCzGGBpU=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fhb2%2Fhb9%2F9529652379678%2Fnaga-v2-pro-2-500x500.png" alt="Razer Naga V2 Pro">
                </div>
                <div class="contentBx">
                    <h2>Razer Naga V2 Pro</h2>
                    <a href="products/mice3.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="razer_naga_v2_pro">
                <input type="hidden" name="product_name" value="Razer Naga V2 Pro">
                <input type="hidden" name="product_image" value="https://assets3.razerzone.com/y265A8on-spu30uzfYMFCzGGBpU=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fhb2%2Fhb9%2F9529652379678%2Fnaga-v2-pro-2-500x500.png">
                <input type="hidden" name="product_link" value="products/mice3.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('razer_naga_v2_pro', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://assets3.razerzone.com/yVd7fP8Z4ibH0AxLPvpk16aelJA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh09%2Fhba%2F9529652346910%2Fnaga-left-handed-2-500x500.png" alt="Razer Naga Left-Handed Edition">
                </div>
                <div class="contentBx">
                    <h2>Razer Naga Left-Handed</h2>
                    <a href="products/mice4.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="razer_naga_left_handed">
                <input type="hidden" name="product_name" value="Razer Naga Left-Handed Edition">
                <input type="hidden" name="product_image" value="https://assets3.razerzone.com/yVd7fP8Z4ibH0AxLPvpk16aelJA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh09%2Fhba%2F9529652346910%2Fnaga-left-handed-2-500x500.png">
                <input type="hidden" name="product_link" value="products/mice4.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('razer_naga_left_handed', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://assets3.razerzone.com/QuWXycAZ9HfgKP_6waksItWG5Vc=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4a%2Fh79%2F9899953684510%2Fpro-click-v2-vertical-black-500x500.png" alt="Razer Pro Click V2 Vertical">
                </div>
                <div class="contentBx">
                    <h2>Razer Pro Click V2</h2>
                    <a href="products/mice5.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="razer_pro_click_v2_vertical">
                <input type="hidden" name="product_name" value="Razer Pro Click V2 Vertical">
                <input type="hidden" name="product_image" value="https://assets3.razerzone.com/QuWXycAZ9HfgKP_6waksItWG5Vc=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4a%2Fh79%2F9899953684510%2Fpro-click-v2-vertical-black-500x500.png">
                <input type="hidden" name="product_link" value="products/mice5.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('razer_pro_click_v2_vertical', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="logitech-mouse" class="category-header">
        <h1 class="category-title">LOGITECH MOUSE SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2/cyan-2025/gallery/pro-x-superlight-2-cyan-top-angle-gallery-1.png?v=1" alt="PRO X SUPERLIGHT 2">
                </div>
                <div class="contentBx">
                    <h2>PRO X SUPERLIGHT</h2>
                    <a href="products/mice6.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_pro_x_superlight_2">
                <input type="hidden" name="product_name" value="PRO X SUPERLIGHT 2">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2/cyan-2025/gallery/pro-x-superlight-2-cyan-top-angle-gallery-1.png?v=1">
                <input type="hidden" name="product_link" value="products/mice6.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_pro_x_superlight_2', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2c-pdp/gallery/pro-x-superlight-2c-mouse-top-angle-black-gallery-1.png?v=1" alt="PRO X SUPERLIGHT 2C">
                </div>
                <div class="contentBx">
                    <h2>PRO X SUPERLIGHT</h2>
                    <a href="products/mice7.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_pro_x_superlight_2c">
                <input type="hidden" name="product_name" value="PRO X SUPERLIGHT 2C">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2c-pdp/gallery/pro-x-superlight-2c-mouse-top-angle-black-gallery-1.png?v=1">
                <input type="hidden" name="product_link" value="products/mice7.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_pro_x_superlight_2c', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x2-superstrike-pdp/gallery/pro-x2-superstrike-top-angle-gallery-1.png?v=1" alt="PRO X2 SUPERSTRIKE">
                </div>
                <div class="contentBx">
                    <h2>PRO X2 SUPER</h2>
                    <a href="products/mice8.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_pro_x2_superstrike">
                <input type="hidden" name="product_name" value="PRO X2 SUPERSTRIKE">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x2-superstrike-pdp/gallery/pro-x2-superstrike-top-angle-gallery-1.png?v=1">
                <input type="hidden" name="product_link" value="products/mice8.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_pro_x2_superstrike', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-plus/gallery/g502x-plus-gallery-2-black.png?v=1" alt="G502 X PLUS">
                </div>
                <div class="contentBx">
                    <h2>G502 X PLUS</h2>
                    <a href="products/mice9.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_g502_x_plus">
                <input type="hidden" name="product_name" value="G502 X PLUS">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-plus/gallery/g502x-plus-gallery-2-black.png?v=1">
                <input type="hidden" name="product_link" value="products/mice9.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_g502_x_plus', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-lightspeed/gallery/g502-x-lightspeed-mouse-top-angle-white-gallery-1.png?v=1" alt="G502 X LIGHTSPEED">
                </div>
                <div class="contentBx">
                    <h2>G502 X LIGHTSPEED</h2>
                    <a href="products/mice10.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="logitech_g502_x_lightspeed">
                <input type="hidden" name="product_name" value="G502 X LIGHTSPEED">
                <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-lightspeed/gallery/g502-x-lightspeed-mouse-top-angle-white-gallery-1.png?v=1">
                <input type="hidden" name="product_link" value="products/mice10.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('logitech_g502_x_lightspeed', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="graphics-card" class="category-header">
        <div class="category-icon">🖥️</div>
        <h1 class="category-title">GRAPHICS CARDS</h1>
        <div class="category-underline"></div>
        <p class="category-desc">
            Experience stunning visuals and powerful performance with next-generation graphics cards. 
            From ultra-smooth gaming to professional content creation, our GPUs deliver exceptional speed,
            realism, and efficiency for every task.
        </p>
    </section>

    <section id="nvidia" class="category-header">
        <h1 class="category-title">NVIDIA GRAPHICS CARDS SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://foramax.hu/image/cache/catalog/product/VGA_PALIT_NVIDIA_RTX5050_Dual_8GB_GDDR6_-_NE65050019P1-GB2070D-i769814-600x600.png" alt="NVIDIA RTX 5050 DUAL 8GB">
                </div>
                <div class="contentBx">
                    <h2>NVIDIA RTX 5050 DUAL</h2>
                    <a href="products/nvidia1.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="nvidia_rtx_5050_dual_8gb">
                <input type="hidden" name="product_name" value="NVIDIA RTX 5050 DUAL 8GB">
                <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/VGA_PALIT_NVIDIA_RTX5050_Dual_8GB_GDDR6_-_NE65050019P1-GB2070D-i769814-600x600.png">
                <input type="hidden" name="product_link" value="products/nvidia1.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('nvidia_rtx_5050_dual_8gb', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%203050%20OC%20Low%20Profile%206G-06-600x600.png" alt="nVIDIA RTX 3050 6GB DDR6">
                </div>
                <div class="contentBx">
                    <h2>NVIDIA RTX 3050 6GB</h2>
                    <a href="products/nvidia2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="nvidia_rtx_3050_6gb">
                <input type="hidden" name="product_name" value="NVIDIA RTX 3050 6GB DDR6">
                <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%203050%20OC%20Low%20Profile%206G-06-600x600.png">
                <input type="hidden" name="product_link" value="products/nvidia2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('nvidia_rtx_3050_6gb', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://foramax.hu/image/cache/catalog/product/rtx5060ti_1-600x600.png" alt=" NVIDIA RTX 5060 Ti DUAL">
                </div>
                <div class="contentBx">
                    <h2>NVIDIA RTX 5060 Ti</h2>
                    <a href="products/nvidia3.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="nvidia_rtx_5060_ti_dual">
                <input type="hidden" name="product_name" value="NVIDIA RTX 5060 Ti DUAL">
                <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/rtx5060ti_1-600x600.png">
                <input type="hidden" name="product_link" value="products/nvidia3.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('nvidia_rtx_5060_ti_dual', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205050%20D6%208G-01-600x600.png" alt="NVIDIA RTX 5050 8GB">
                </div>
                <div class="contentBx">
                    <h2>NVIDIA RTX 5050 8GB</h2>
                    <a href="products/nvidia4.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="nvidia_rtx_5050_8gb">
                <input type="hidden" name="product_name" value="NVIDIA RTX 5050 8GB">
                <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205050%20D6%208G-01-600x600.png">
                <input type="hidden" name="product_link" value="products/nvidia4.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('nvidia_rtx_5050_8gb', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205070%20Ti%20GAMING%20OC%2016G-01-600x600.png" alt="NVIDIA RTX 5070 Ti">
                </div>
                <div class="contentBx">
                    <h2>NVIDIA RTX 5070 Ti</h2>
                    <a href="products/nvidia5.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="nvidia_rtx_5070_ti">
                <input type="hidden" name="product_name" value="NVIDIA RTX 5070 Ti">
                <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205070%20Ti%20GAMING%20OC%2016G-01-600x600.png">
                <input type="hidden" name="product_link" value="products/nvidia5.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('nvidia_rtx_5070_ti', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="amd" class="category-header">
        <h1 class="category-title">AMD GRAPHICS CARDS SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_20GB_DDR6_OC-i1445676.png" alt="AMD RX 7900 XT - BLACK">
                </div>
                <div class="contentBx">
                    <h2>RX 7900 XT - BLACK</h2>
                    <a href="products/amd1.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_rx_7900_xt_black">
                <input type="hidden" name="product_name" value="AMD RX 7900 XT - BLACK">
                <input type="hidden" name="product_image" value="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_20GB_DDR6_OC-i1445676.png">
                <input type="hidden" name="product_link" value="products/amd1.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_rx_7900_xt_black', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_WHITE_20GB_DDR6_OC-i1445669.png" alt="AMD RX 7900 XT - WHITE">
                </div>
                <div class="contentBx">
                    <h2>RX 7900 XT - WHITE</h2>
                    <a href="products/amd2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_rx_7900_xt_white">
                <input type="hidden" name="product_name" value="AMD RX 7900 XT - WHITE">
                <input type="hidden" name="product_image" value="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_WHITE_20GB_DDR6_OC-i1445669.png">
                <input type="hidden" name="product_link" value="products/amd2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_rx_7900_xt_white', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9060_XT_16GB_DDR6_OC-i1625663.png" alt="AMD RX 9060 XT 16GB">
                </div>
                <div class="contentBx">
                    <h2>AMD RX 9060 XT</h2>
                    <a href="products/amd3.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_rx_9060_xt_16gb">
                <input type="hidden" name="product_name" value="AMD RX 9060 XT 16GB">
                <input type="hidden" name="product_image" value="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9060_XT_16GB_DDR6_OC-i1625663.png">
                <input type="hidden" name="product_link" value="products/amd3.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_rx_9060_xt_16gb', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9070_XT_16GB-i1534401.png" alt="AMD RX 9070 XT 16GB">
                </div>
                <div class="contentBx">
                    <h2>AMD RX 9070 XT</h2>
                    <a href="products/amd4.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_rx_9070_xt_16gb">
                <input type="hidden" name="product_name" value="AMD RX 9070 XT 16GB">
                <input type="hidden" name="product_image" value="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9070_XT_16GB-i1534401.png">
                <input type="hidden" name="product_link" value="products/amd4.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_rx_9070_xt_16gb', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_6800XT_PHANTOM_GAMING_16GB_DDR6_OC-i1445767.png" alt="AMD RX 6800 XT PHANTOM">
                </div>
                <div class="contentBx">
                    <h2>AMD RX 6800 XT</h2>
                    <a href="products/amd5.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_rx_6800_xt_phantom">
                <input type="hidden" name="product_name" value="AMD RX 6800 XT PHANTOM">
                <input type="hidden" name="product_image" value="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_6800XT_PHANTOM_GAMING_16GB_DDR6_OC-i1445767.png">
                <input type="hidden" name="product_link" value="products/amd5.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_rx_6800_xt_phantom', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="processor" class="category-header">
        <div class="category-icon">🧠</div>
        <h1 class="category-title">PROCESSORS</h1>
        <div class="category-underline"></div>
        <p class="category-desc">
            Experience lightning-fast performance and reliable computing power with next-generation processors. 
            Whether for gaming, multitasking, or professional work, our CPUs deliver exceptional speed, 
            efficiency, and stability for every task.
        </p>
    </section>

    <section id="intel" class="category-header">
        <h1 class="category-title">Intel SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png" alt="Intel Core i7-14700">
                </div>
                <div class="contentBx">
                    <h2>Intel Core i7-14700</h2>
                    <a href="products/intel1.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="intel_core_i7_14700">
                <input type="hidden" name="product_name" value="Intel Core i7-14700">
                <input type="hidden" name="product_image" value="https://pngimg.com/uploads/cpu/cpu_PNG46.png">
                <input type="hidden" name="product_link" value="products/intel1.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('intel_core_i7_14700', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png" alt="Intel Core i5-14400">
                </div>
                <div class="contentBx">
                    <h2>Intel Core i5-14400</h2>
                    <a href="products/intel2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="intel_core_i5_14400">
                <input type="hidden" name="product_name" value="Intel Core i5-14400">
                <input type="hidden" name="product_image" value="https://pngimg.com/uploads/cpu/cpu_PNG46.png">
                <input type="hidden" name="product_link" value="products/intel2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('intel_core_i5_14400', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png" alt="Intel Core i5-14400F">
                </div>
                <div class="contentBx">
                    <h2>Intel Core i5-14400F</h2>
                    <a href="products/intel3.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="intel_core_i5_14400f">
                <input type="hidden" name="product_name" value="Intel Core i5-14400F">
                <input type="hidden" name="product_image" value="https://pngimg.com/uploads/cpu/cpu_PNG46.png">
                <input type="hidden" name="product_link" value="products/intel3.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('intel_core_i5_14400f', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png" alt="Intel Core i5-12400">
                </div>
                <div class="contentBx">
                    <h2>Intel Core i5-12400</h2>
                    <a href="products/intel4.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="intel_core_i5_12400">
                <input type="hidden" name="product_name" value="Intel Core i5-12400">
                <input type="hidden" name="product_image" value="https://pngimg.com/uploads/cpu/cpu_PNG46.png">
                <input type="hidden" name="product_link" value="products/intel4.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('intel_core_i5_12400', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png" alt="Intel Core i3-12100">
                </div>
                <div class="contentBx">
                    <h2>Intel Core i3-12100</h2>
                    <a href="products/intel5.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="intel_core_i3_12100">
                <input type="hidden" name="product_name" value="Intel Core i3-12100">
                <input type="hidden" name="product_image" value="https://pngimg.com/uploads/cpu/cpu_PNG46.png">
                <input type="hidden" name="product_link" value="products/intel5.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('intel_core_i3_12100', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>

    <section id="amd-processor" class="category-header">
        <h1 class="category-title">AMD PROCESSOR SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/320w/products/166/468/Ryzen_9_9000X3D__86102.1741368250.png?c=1" alt="AMD Ryzen™ 9 9950X3D">
                </div>
                <div class="contentBx">
                    <h2>AMD Ryzen™ 9 9950X3D</h2>
                    <a href="products/amdproc1.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_ryzen_9_9950x3d">
                <input type="hidden" name="product_name" value="AMD Ryzen™ 9 9950X3D">
                <input type="hidden" name="product_image" value="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/320w/products/166/468/Ryzen_9_9000X3D__86102.1741368250.png?c=1">
                <input type="hidden" name="product_link" value="products/amdproc1.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_ryzen_9_9950x3d', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/165/470/Ryzen_9_9000X3D__03822.1741368290.png?c=1" alt="AMD Ryzen™ 9 9900X3D">
                </div>
                <div class="contentBx">
                    <h2>AMD Ryzen™ 9 9900X3D</h2>
                    <a href="products/amdproc2.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_ryzen_9_9900x3d">
                <input type="hidden" name="product_name" value="AMD Ryzen™ 9 9900X3D">
                <input type="hidden" name="product_image" value="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/165/470/Ryzen_9_9000X3D__03822.1741368290.png?c=1">
                <input type="hidden" name="product_link" value="products/amdproc2.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_ryzen_9_9900x3d', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/163/465/Ryzen_7_9800X3D__03299.1730824088.png?c=1" alt="AMD Ryzen™ 7 9800X3D">
                </div>
                <div class="contentBx">
                    <h2>AMD Ryzen™ 7 9800X3D</h2>
                    <a href="products/amdproc3.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_ryzen_7_9800x3d">
                <input type="hidden" name="product_name" value="AMD Ryzen™ 7 9800X3D">
                <input type="hidden" name="product_image" value="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/163/465/Ryzen_7_9800X3D__03299.1730824088.png?c=1">
                <input type="hidden" name="product_link" value="products/amdproc3.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_ryzen_7_9800x3d', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/158/461/2733716-ryzen-9-9000-series_1200x1200__68606.1721680468.png?c=1" alt="AMD Ryzen™ 9 9950X">
                </div>
                <div class="contentBx">
                    <h2>AMD Ryzen™ 9 9950X</h2>
                    <a href="products/amdproc4.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_ryzen_9_9950x">
                <input type="hidden" name="product_name" value="AMD Ryzen™ 9 9950X">
                <input type="hidden" name="product_image" value="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/158/461/2733716-ryzen-9-9000-series_1200x1200__68606.1721680468.png?c=1">
                <input type="hidden" name="product_link" value="products/amdproc4.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_ryzen_9_9950x', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>

        <div class="product-item">
            <div class="card">
                <div class="imgBx">
                    <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/160/457/2733716-ryzen-7-9000-series_1200x1200__84837.1721680415.png?c=1" alt="AMD Ryzen™ 7 9700X">
                </div>
                <div class="contentBx">
                    <h2>AMD Ryzen™ 7 9700X</h2>
                    <a href="products/amdproc5.php">Buy Now</a>
                </div>
            </div>
            <form method="post" class="favorite-form">
                <input type="hidden" name="toggle_favorite" value="1">
                <input type="hidden" name="product_id" value="amd_ryzen_7_9700x">
                <input type="hidden" name="product_name" value="AMD Ryzen™ 7 9700X">
                <input type="hidden" name="product_image" value="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/160/457/2733716-ryzen-7-9000-series_1200x1200__84837.1721680415.png?c=1">
                <input type="hidden" name="product_link" value="products/amdproc5.php">
                <button type="submit" class="favorite-btn <?php echo (isset($_SESSION['favorites']) && in_array('amd_ryzen_7_9700x', $_SESSION['favorites'])) ? 'active' : ''; ?>">
                    <i class="fa fa-heart"></i> Make It Favourite
                </button>
            </form>
        </div>
    </div>
</main>

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

<div class="cart-button-container">
    <div class="cart-wrapper">
        <a href="myorder.php" class="cart-btn">
            <i class="fa fa-shopping-cart"></i>
            <?php if (!empty($_SESSION['order'])): ?>
                <span class="cart-badge">
                    <?= count($_SESSION['order']) ?>
                </span>
            <?php endif; ?>
            <span class="cart-text">My Cart</span>
        </a>
        <?php if (!empty($_SESSION['order'])): ?>
            <div class="cart-preview">
                <strong>Cart items</strong>
                <?php foreach ($_SESSION['order'] as $item): ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="Product">
                        <div class="cart-item-info">
                            <div class="cart-item-name">
                                <?= htmlspecialchars($item['name']) ?>
                            </div>
                            <div class="cart-item-price">
                                $<?= htmlspecialchars($item['price']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($message): ?>
<div id="notification" class="notification success" data-message="<?php echo htmlspecialchars($message); ?>">
    <div class="notification-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="notification-content">
        <strong><?php echo htmlspecialchars($message); ?></strong>
    </div>
</div>
<?php endif; ?>

</body>
<script src="javas.js"></script>
<script src="products.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
<script src="chat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Értesítés megjelenítése (ha van)
    const notification = document.getElementById('notification');
    
    if (notification) {
        const message = notification.getAttribute('data-message');
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 400);
        }, 3000);
    }
    
    // Kedvencek gomb kezelése
    const favoriteForms = document.querySelectorAll('.favorite-form');
    
    favoriteForms.forEach(form => {
        const button = form.querySelector('.favorite-btn');
        
        form.addEventListener('submit', function(e) {
            //Megakadályozzuk az oldal újratöltését
            e.preventDefault();
            
            // Elküldjük az űrlapot 
            const formData = new FormData(this);
            const productName = this.querySelector('input[name="product_name"]').value;
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Sikeres küldés
                const isActive = button.classList.contains('active');
                let message = '';
                
                if (isActive) {
                    message = `"${productName}" removed from favorites!`;
                    button.classList.remove('active');
                } else {
                    message = `"${productName}" added to favorites!`;
                    button.classList.add('active');
                }
                
                // Értesítés megjelenítése
                showNotification(message, isActive ? 'error' : 'success');            
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred! Please try again.', 'error');
            });
        });
    });
    
    // Értesítés megjelenítjük
    function showNotification(message, type) {
        // Ha már van értesítés, töröljük
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
            </div>
            <div class="notification-content">
                <strong>${message}</strong>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 400);
        }, 3000);
    }
});
</script>
</html>
