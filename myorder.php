<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
//Megnézi hogy létezik egyáltalán a kosár és ha törölni akarják akkor kitörli a teljes kosarat a menetből, majd ráfrissit az oldalra
if (isset($_POST['clear_order'])) {
    unset($_SESSION['order']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$order_list = $_SESSION['order'] ?? [];

//Teljes ár kiszámitása, szamlalo függvénnyel
$totalPrice = 0;
foreach ($order_list as $order) {
    $totalPrice += $order['price'];
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - My Orders</title>
    <link rel="stylesheet" href="faq.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="letoles.jpg" type="image/png">

    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .header { 
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: #fff; 
            padding: 30px; 
            border-radius: 10px; 
            text-align: center; 
            margin-bottom: 30px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header h1 { margin: 0; font-size: 2rem; letter-spacing: 1px; }
        .order-box { background: #fff; padding: 15px; border-radius: 10px; margin-bottom: 15px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .order-box img { width: 120px; height: auto; margin-right: 20px; border-radius: 8px; }
        .order-box h2 { margin: 0; font-size: 1.2rem; }
        .order-box p { margin: 0; font-weight: 500; }
        .total { font-weight: bold; font-size: 1.5rem; margin-top: 20px; text-align: right; }
        .btn-clear, .btn-checkout { margin-top: 10px; }
        .btn-checkout { float: right; }
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
          <small>+36 70 645 1793</small>
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
          <a href="#" class="nav-link dropdown-toggle" id="pagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            MY ACCOUNT
          </a>
          <ul class="dropdown-menu fade-up m-0" aria-labelledby="pagesDropdown">
            <li><a href="profile.php" class="dropdown-item">MY PROFILE</a></li>
            <li><a href="logout.php" class="dropdown-item">LOGOUT</a></li>
            <li><a href="myorder.php" class="dropdown-item">MY ORDERS</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
<br>

<div id="notice" style="background-color: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeeba; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;">
    ⚠️ Please note: If you log out from your profile or close the browser and return later, you will need to reselect the items you want to order 🛒. This helps us reduce server load 💻. Thank you for your understanding 🙏!
</div>

<div class="header">
        <h1>My Orders</h1>
    </div
<div class="container">

    <?php if (empty($order_list)): ?>
    <div style="background-color: #d1ecf1; color: #0c5460; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;">
        🛒 You haven't ordered anything yet! Browse our products to add items to your cart ✨
    </div>
<?php else: ?>
    <?php foreach ($order_list as $order): ?>
        <div class="order-box">
            <img src="<?= htmlspecialchars($order['image']) ?>" alt="<?= htmlspecialchars($order['name']) ?>">
            <div>
                <h2><?= htmlspecialchars($order['name']) ?></h2>
                <p><strong>Price:</strong> $<?= number_format($order['price'], 2) ?></p>
            </div>
        </div>
    <?php endforeach; ?>

    <p class="total">Total price: $<?= number_format($totalPrice, 2) ?></p>

    <form method="post">
        <button type="submit" name="clear_order" class="btn btn-danger btn-clear">Delete ALL</button>
        <a href="checkout.php" class="btn btn-success btn-checkout">Checkout / Payment</a>
    </form>
<?php endif; ?>
</div>
<br>
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
      <p class="email">kapcsandi.tomi@gmail.com</p>
      <p class="phone">+36 70 645 1793</p>
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
</body>
<script src="javas.js"></script>
<script src="faq.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
<script src="chat.js"></script>
<script src="myorder.js"></script>
</html>
