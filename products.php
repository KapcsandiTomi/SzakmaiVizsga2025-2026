<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
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
        <a href="pc_builder/index.php">
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
        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitech.com/content/dam/gaming/en/products/pro-x/pro-headset-gallery-1.png" alt="Logitech X PRO Wired Headset">
            </div>
            <div class="contentBx">
                <h2>Logitech PRO X Wired Headset</h2>
                <a href="products/logitechProX.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/a20-x-pdp/gallery/a20x-3qtr-front-with-receiver-black-gallery-1-new.png" alt="Logitech Astro A20 X">
            </div>
            <div class="contentBx">
                <h2>Logitech Astro A20 X</h2>
                <a href="products/logitechAstro.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/g733/gallery/g733-black-gallery-3.png" alt="Logitech Pro G733">
            </div>
            <div class="contentBx">
                <h2>Logitech PRO Pro G733</h2>
                <a href="products/logitechG733.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/audio/g735-wireless-headset/gallery/2025/g735-3qtr-front-left-angle-gallery-1.png?v=1" alt="Logitech Pro G735">
            </div>
            <div class="contentBx">
                <h2>Logitech PRO Pro G735</h2>
                <a href="products/logitechG735.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-2-lightspeed/gallery/gallery-1-pro-x-2-lightspeed-gaming-headset-black.png" alt="Logitech X PRO 2 Wired Headset">
            </div>
            <div class="contentBx">
                <h2>Logitech PRO X 2</h2>
                <a href="products/logitechProX2.php">Buy Now</a>
            </div>
        </div>
    </div>

    <section id="asus" class="category-header">
        <h1 class="category-title">ASUS section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="card">
            <div class="imgBx">
                <img src="https://dlcdnwebimgs.asus.com/gain/F223AA9E-DC85-421B-990A-EA07C27D19E5/w717/h525/fwebp" alt="ROG Delta S Wireless">
            </div>
            <div class="contentBx">
                <h2>ASUS ROG Delta S Wireless</h2>
                <a href="products/asusrogdelta.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://dlcdnwebimgs.asus.com/gain/46BDC9FB-449D-4549-BADE-1E5A4EACA111/w717/h525/fwebp" alt="ROG Delta II">
            </div>
            <div class="contentBx">
                <h2>ASUS ROG Delta II</h2>
                <a href="products/asusrogdelta2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://dlcdnwebimgs.asus.com/gain/B43F05B8-6236-4225-8FB5-107B414D6104/w717/h525/fwebp" alt="ROG Strix Go Core Moonlight White">
            </div>
            <div class="contentBx">
                <h2>ASUS ROG Strix Go Core</h2>
                <a href="products/asusrogstrixgocore.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://dlcdnwebimgs.asus.com/gain/0B6F7E0D-DE7D-48BC-BBB1-75F35E2D5CC9/w717/h525/fwebp" alt="ROG Fusion II 500">
            </div>
            <div class="contentBx">
                <h2>ASUS ROG Fusion II 500</h2>
                <a href="products/asusrogfushion.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://dlcdnwebimgs.asus.com/gain/2FAA2882-5564-4A2A-80DA-C76BA236E933/w717/h525/fwebp" alt="ROG STRIX GO CORE">
            </div>
            <div class="contentBx">
                <h2>ASUS ROG STRIX</h2>
                <a href="products/asusstrix.php">Buy Now</a>
            </div>
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
        <div class="card">
            <div class="imgBx">
                <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9fa1972f-906a-4ac6-bd7c-d0ddca758916/conversions/EXCALIBUR-B-%281%29-thumb.png" alt="EXCALIBUR Black">
            </div>
            <div class="contentBx">
                <h2>EXCALIBUR Black</h2>
                <a href="products/keyboard1.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9f7f98c4-9f22-41a6-9c4f-d681bb832271/conversions/Wakizashi-2-bijela-HR-Red.sw-%281%29-thumb.png" alt="WAKIZASHI 2 HR White">
            </div>
            <div class="contentBx">
                <h2>WAKIZASHI 2 HR White</h2>
                <a href="products/keyboard2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9ddf293b-9d73-40db-8f04-dc867477c675/conversions/NAGAMAKI-W-US-RED.SW-%281%29-thumb.png" alt="NAGAMAKI WHITE US">
            </div>
            <div class="contentBx">
                <h2>NAGAMAKI WHITE US</h2>
                <a href="products/keyboard3.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e495f5d-a455-41c5-8485-a489fc9d4a0b/conversions/Wakizashiv2-US-Gray-Black-Red.sw-%281%29-thumb.png" alt="WAKIZASHI 2 US Gray/Black">
            </div>
            <div class="contentBx">
                <h2>WAKIZASHI 2 US Gray/Black</h2>
                <a href="products/keyboard4.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e3fc2de-9013-4019-bb62-75e75684d856/conversions/SHINOBI-2-CZSK-White-Red-Switch-%281%29-thumb.png" alt="SHINOBI 2 White CZ/SK">
            </div>
            <div class="contentBx">
                <h2>SHINOBI 2 White CZ/SK</h2>
                <a href="products/keyboard5.php">Buy Now</a>
            </div>
        </div>
    </div>

    <section id="reddragon" class="category-header">
        <h1 class="category-title">reddragon section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="card">
            <div class="imgBx">
                <img src="https://redragonshop.com/cdn/shop/files/RedragonANTONIUMK745PROKeyboard_2.png?v=1761211643&width=713" alt="ANTONIUM K745 PRO">
            </div>
            <div class="contentBx">
                <h2>ANTONIUM K745 PROk</h2>
                <a href="products/keyboard6.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://redragonshop.com/cdn/shop/files/RedragonFIZZK61760RapidTriggerMagneticSwitchGamingKeyboard_1_1.png?v=1762463308&width=713" alt="FIZZ K617 (Magnetic Hall Effect Keyboard)">
            </div>
            <div class="contentBx">
                <h2>FIZZ K617</h2>
                <a href="products/keyboard7.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://redragonshop.com/cdn/shop/files/RedragonEISAK686HERapidTriggerGamingKeyboard_1.png?v=1760435423&width=713" alt="EISA K686 HE Rapid Trigger Gaming Keyboard">
            </div>
            <div class="contentBx">
                <h2>EISA K686 HE Rapid Trigger</h2>
                <a href="products/keyboard8.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://redragonshop.com/cdn/shop/files/RedragonEISAK686PRO98KeysWirelessGasketRGBGamingKeyboard_1.png?v=1762463457&width=713" alt="EISA K686 PRO SE Anime Keyboard">
            </div>
            <div class="contentBx">
                <h2>EISA K686 PRO SE</h2>
                <a href="products/keyboard9.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://redragonshop.com/cdn/shop/files/RedragonARTEMISK719PROGraffitiKeyboard_1.png?v=1762848339&width=713" alt="ARTEMIS K719 PRO Graffiti Keyboard">
            </div>
            <div class="contentBx">
                <h2>ARTEMIS K719 PRO</h2>
                <a href="products/keyboard10.php">Buy Now</a>
            </div>
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
        <div class="card">
            <div class="imgBx">
                <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fg510euxen/gallery/hu-odyssey-g5-g51f-ls32fg510euxen-548700337?$Q90_1920_1280_F_PNG$" alt="32 Odyssey G5 G51F QHD 180Hz gaming monitor">
            </div>
            <div class="contentBx">
                <h2>Odyssey 180Hz gaming monitor</h2>
                <a href="products/monitor1.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fm700uuxdu/gallery/hu-smart-m7-32m70f-black-ls32fm700uuxdu-547197500?$Q90_1920_1280_F_PNG$" alt="32 Vision AI Smart monitor M7 M70F 4K - LS32FM700UUXDU">
            </div>
            <div class="contentBx">
                <h2>32" Vision AI Smart monitor M7</h2>
                <a href="products/monitor2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls27dg602suxen/gallery/hu-odyssey-oled-g6-g60sd-ls27dg602suxen-541135297?$Q90_1920_1280_F_PNG$" alt="27 Odyssey OLED G6 G60SD QHD 360 Hz gaming monitor - LS27DG602SUXEN">
            </div>
            <div class="contentBx">
                <h2>27" Odyssey OLED G6</h2>
                <a href="products/monitor3.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls37d800uauxen/gallery/hu-viewfinity-s8-37s80ud-ls37d800uauxen-546091975?$Q90_1920_1280_F_PNG$" alt="37 ViewFinity S8 S80UD UHD monitor - LS37D800UAUXEN">
            </div>
            <div class="contentBx">
                <h2>37" ViewFinity S8 S80UD</h2>
                <a href="products/monitor4.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls55cg970nuxdu/gallery/hu-odyssey-ark-g97nc-ls55cg970nuxdu-538036806?$Q90_1920_1280_F_PNG$" alt="55 Odyssey Ark G9 G97NC UHD 165 Hz ívelt gaming monitor - LS55CG970NUXDU">
            </div>
            <div class="contentBx">
                <h2>55" Odyssey Ark G9 G97NC</h2>
                <a href="products/monitor5.php">Buy Now</a>
            </div>
        </div>
    </div>

    <section id="msi" class="category-header">
        <h1 class="category-title">MSI section</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="card">
            <div class="imgBx">
                <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MPG/271QR-QD-OLED-X50/kv-pd.webp" alt="MPG 271QR QD-OLED X50">
            </div>
            <div class="contentBx">
                <h2>MPG 271QR QD-OLED X50</h2>
                <a href="products/monitor6.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/mag-274qpf-x32/kv-pd.png" alt="MAG 274QPF X32">
            </div>
            <div class="contentBx">
                <h2>MAG 274QPF X32</h2>
                <a href="products/monitor7.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-275QF-E20/msi-mag-275qf-e20-kv.png" alt="MAG 275QF E20">
            </div>
            <div class="contentBx">
                <h2>MAG 275QF E20</h2>
                <a href="products/monitor8.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QPF-E20/kv/msi-mag-272qpf-e20-kv.png" alt="MAG 272QPF E20">
            </div>
            <div class="contentBx">
                <h2>MAG 272QPF E20</h2>
                <a href="products/monitor9.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QP-QD-OLED-X24/mag-272qp-qd-oled-x24-kv.webp" alt="MAG 272QP QD-OLED X24">
            </div>
            <div class="contentBx">
                <h2>MAG 272QP QD-OLED X24</h2>
                <a href="products/monitor10.php">Buy Now</a>
            </div>
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
        <div class="card">
            <div class="imgBx">
                <img src="https://assets3.razerzone.com/7QeO9se0LbDhoHwFI-3juHOVEzA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4e%2Fh5e%2F9765618221086%2Fviper-v3-pro-white-500x500.png" alt="Razer Viper V3 Pro">
            </div>
            <div class="contentBx">
                <h2>Razer Viper V3 Pro</h2>
                <a href="products/mice1.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://assets3.razerzone.com/9FvPlvujlrxafg7AbznLiEuAncE=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh01%2Fhf3%2F9926511951902%2Fdeathadder-v4-pro-black-500x500.png" alt="Razer DeathAdder V4 Pro">
            </div>
            <div class="contentBx">
                <h2>Razer DeathAdder V4 Pro</h2>
                <a href="products/mice2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://assets3.razerzone.com/y265A8on-spu30uzfYMFCzGGBpU=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fhb2%2Fhb9%2F9529652379678%2Fnaga-v2-pro-2-500x500.png" alt="Razer Naga V2 Pro">
            </div>
            <div class="contentBx">
                <h2>Razer Naga V2 Pro</h2>
                <a href="products/mice3.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://assets3.razerzone.com/yVd7fP8Z4ibH0AxLPvpk16aelJA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh09%2Fhba%2F9529652346910%2Fnaga-left-handed-2-500x500.png" alt="Razer Naga Left-Handed Edition">
            </div>
            <div class="contentBx">
                <h2>Razer Naga Left-Handed Edition</h2>
                <a href="products/mice4.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://assets3.razerzone.com/QuWXycAZ9HfgKP_6waksItWG5Vc=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4a%2Fh79%2F9899953684510%2Fpro-click-v2-vertical-black-500x500.png" alt="Razer Pro Click V2 Vertical">
            </div>
            <div class="contentBx">
                <h2>Razer Pro Click V2 Vertical</h2>
                <a href="products/mice5.php">Buy Now</a>
            </div>
        </div>
    </div>

    <section id="logitech-mouse" class="category-header">
        <h1 class="category-title">LOGITECH MOUSE SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2/cyan-2025/gallery/pro-x-superlight-2-cyan-top-angle-gallery-1.png?v=1" alt="PRO X SUPERLIGHT 2">
            </div>
            <div class="contentBx">
                <h2>PRO X SUPERLIGHT 2</h2>
                <a href="products/mice6.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight-2c-pdp/gallery/pro-x-superlight-2c-mouse-top-angle-black-gallery-1.png?v=1" alt="PRO X SUPERLIGHT 2C">
            </div>
            <div class="contentBx">
                <h2>PRO X SUPERLIGHT 2C</h2>
                <a href="products/mice7.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x2-superstrike-pdp/gallery/pro-x2-superstrike-top-angle-gallery-1.png?v=1" alt="PRO X2 SUPERSTRIKE">
            </div>
            <div class="contentBx">
                <h2>PRO X2 SUPERSTRIKE</h2>
                <a href="products/mice8.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-plus/gallery/g502x-plus-gallery-2-black.png?v=1" alt="G502 X PLUS">
            </div>
            <div class="contentBx">
                <h2>G502 X PLUS</h2>
                <a href="products/mice9.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-lightspeed/gallery/g502-x-lightspeed-mouse-top-angle-white-gallery-1.png?v=1" alt="G502 X LIGHTSPEED">
            </div>
            <div class="contentBx">
                <h2>G502 X LIGHTSPEED</h2>
                <a href="products/mice10.php">Buy Now</a>
            </div>
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
        <div class="card">
            <div class="imgBx">
                <img src="https://foramax.hu/image/cache/catalog/product/VGA_PALIT_NVIDIA_RTX5050_Dual_8GB_GDDR6_-_NE65050019P1-GB2070D-i769814-600x600.png" alt="NVIDIA RTX 5050 DUAL 8GB">
            </div>
            <div class="contentBx">
                <h2>NVIDIA RTX 5050 DUAL 8GB</h2>
                <a href="products/nvidia1.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%203050%20OC%20Low%20Profile%206G-06-600x600.png" alt="nVIDIA RTX 3050 6GB DDR6">
            </div>
            <div class="contentBx">
                <h2>NVIDIA RTX 3050 6GB DDR6</h2>
                <a href="products/nvidia2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://foramax.hu/image/cache/catalog/product/rtx5060ti_1-600x600.png" alt=" NVIDIA RTX 5060 Ti DUAL">
            </div>
            <div class="contentBx">
                <h2>NVIDIA RTX 5060 Ti DUAL</h2>
                <a href="products/nvidia3.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205050%20D6%208G-01-600x600.png" alt="NVIDIA RTX 5050 8GB">
            </div>
            <div class="contentBx">
                <h2>NVIDIA RTX 5050 8GB</h2>
                <a href="products/nvidia4.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205070%20Ti%20GAMING%20OC%2016G-01-600x600.png" alt="NVIDIA RTX 5070 Ti">
            </div>
            <div class="contentBx">
                <h2>NVIDIA RTX 5070 Ti</h2>
                <a href="products/nvidia5.php">Buy Now</a>
            </div>
        </div>
    </div>

    <section id="amd" class="category-header">
        <h1 class="category-title">AMD GRAPHICS CARDS SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="card">
            <div class="imgBx">
                <img src="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_20GB_DDR6_OC-i1445676.png" alt="AMD RX 7900 XT - BLACK">
            </div>
            <div class="contentBx">
                <h2>AMD RX 7900 XT - BLACK</h2>
                <a href="products/amd1.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_WHITE_20GB_DDR6_OC-i1445669.png" alt="AMD RX 7900 XT - WHITE">
            </div>
            <div class="contentBx">
                <h2>AMD RX 7900 XT - WHITE6</h2>
                <a href="products/amd2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9060_XT_16GB_DDR6_OC-i1625663.png" alt="AMD RX 9060 XT 16GB">
            </div>
            <div class="contentBx">
                <h2>AMD RX 9060 XT 16GB</h2>
                <a href="products/amd3.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9070_XT_16GB-i1534401.png" alt="AMD RX 9070 XT 16GB">
            </div>
            <div class="contentBx">
                <h2>AMD RX 9070 XT 16GB</h2>
                <a href="products/amd4.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_6800XT_PHANTOM_GAMING_16GB_DDR6_OC-i1445767.png" alt="AMD RX 6800 XT PHANTOM">
            </div>
            <div class="contentBx">
                <h2>AMD RX 6800 XT PHANTOM</h2>
                <a href="products/amd5.php">Buy Now</a>
            </div>
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
        <div class="card">
            <div class="imgBx">
                <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png" alt="Intel Core i7-14700">
            </div>
            <div class="contentBx">
                <h2>Intel Core i7-14700</h2>
                <a href="products/intel1.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png"" alt="Intel Core i5-14400">
            </div>
            <div class="contentBx">
                <h2>Intel Core i5-14400</h2>
                <a href="products/intel2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png"" alt="Intel Core i5-14400F">
            </div>
            <div class="contentBx">
                <h2>Intel Core i5-14400F</h2>
                <a href="products/intel3.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png"" alt="Intel Core i5-12400">
            </div>
            <div class="contentBx">
                <h2>Intel Core i5-12400</h2>
                <a href="products/intel4.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png"" alt="Intel Core i3-12100">
            </div>
            <div class="contentBx">
                <h2>Intel Core i3-12100</h2>
                <a href="products/intel5.php">Buy Now</a>
            </div>
        </div>
    </div>

    <section id="amd-processor" class="category-header">
        <h1 class="category-title">AMD PROCESSOR SECTION</h1>
        <div class="category-underline"></div>
    </section>

    <div class="products-container">
        <div class="card">
            <div class="imgBx">
                <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/320w/products/166/468/Ryzen_9_9000X3D__86102.1741368250.png?c=1" alt="AMD Ryzen™ 9 9950X3D">
            </div>
            <div class="contentBx">
                <h2>AMD Ryzen™ 9 9950X3D</h2>
                <a href="products/amdproc1.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/165/470/Ryzen_9_9000X3D__03822.1741368290.png?c=1"" alt="AMD Ryzen™ 9 9900X3D">
            </div>
            <div class="contentBx">
                <h2>AMD Ryzen™ 9 9900X3D</h2>
                <a href="products/amdproc2.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/163/465/Ryzen_7_9800X3D__03299.1730824088.png?c=1"" alt="AMD Ryzen™ 7 9800X3D">
            </div>
            <div class="contentBx">
                <h2>AMD Ryzen™ 7 9800X3D</h2>
                <a href="products/amdproc3.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/158/461/2733716-ryzen-9-9000-series_1200x1200__68606.1721680468.png?c=1"" alt="AMD Ryzen™ 9 9950X">
            </div>
            <div class="contentBx">
                <h2>AMD Ryzen™ 9 9950X</h2>
                <a href="products/amdproc4.php">Buy Now</a>
            </div>
        </div>

        <div class="card">
            <div class="imgBx">
                <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/160/457/2733716-ryzen-7-9000-series_1200x1200__84837.1721680415.png?c=1"" alt="AMD Ryzen™ 7 9700X">
            </div>
            <div class="contentBx">
                <h2>AMD Ryzen™ 7 9700X</h2>
                <a href="products/amdproc5.php">Buy Now</a>
            </div>
        </div>
    </div>
</main>

<footer>
  <div class="container">
    <div class="footer-col">
      <h2>MOTO<div class="underline"><span></span></div></h2>
      <p class="footer-para">Innovation starts here – with machines designed for creators and professionals who demand more. Whether you’re editing, rendering, or building the future, our systems are ready to keep up with your vision.</p>
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
        <a href="https://x.com/tamas_kapc343"><i class="fab fa-twitter"></i></a>
        <a href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
</footer>


<div class="cart-button-container">
    <div class="cart-wrapper">

        <!-- Kosár  -->
        <a href="myorder.php" class="cart-btn">
            <i class="fa fa-shopping-cart"></i>

            <?php if (!empty($_SESSION['order'])): ?>
                <span class="cart-badge">
                    <?= count($_SESSION['order']) ?>
                </span>
            <?php endif; ?>

            <span class="cart-text">My Cart</span>
        </a>

        <!-- Kosár adatai pl mit rendelt a felhasználó -->
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


</body>
<script src="javas.js"></script>
<script src="products.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
<script src="chat.js"></script>
</html>
