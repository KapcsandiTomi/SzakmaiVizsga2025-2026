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
    <title>Aqua Mini Shop - Main</title>
    <link rel="stylesheet" href="fooldal.css.">
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
        <a href="rateus.php" class="nav-item nav-link">RATE US</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" id="contactDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            CONTACT <i class="fa-solid fa-caret-down"></i>
          </a>
          <ul class="dropdown-menu fade-up m-0" aria-labelledby="pagesDropdown">
            <li><a href="writeUs.php" class="dropdown-item">WRITE US</a></li>
            <li><a href="faq.php" class="dropdown-item">FAQ</a></li>
          </ul>
        </div>


        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            MY ACCOUNT <i class="fa-solid fa-caret-down"></i>
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

<?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
    <div class = "admin-page">
      <b><a href="admin/admin.php">⚙️ Admin Panel</a></b>
    </div>
<?php endif; ?>

<main>
  <div class="hero">
    <div class="hero-text">
      <h1>Give Your Gaming<br>A New Style!</h1>
      <p>Machines are built to extend our hands, but innovations make them extend our possibilities.</p>
      <a href="products.php" class="btn">Explore Now →</a>
    </div>
    <img src="aqau.jpg" alt="PC" style="border-radius: 100px;">
  </div>

  <div class="categories">
    <div class="small-container">
      <div class="row">
        <div class="col-3"><img src="1.jpg" alt="PC"></div>
        <div class="col-3"><img src="2.jpg" alt="PC"></div>
        <div class="col-3"><img src="3.avif" alt="PC"></div>
      </div>
    </div>

    <div class="small-container">
      <h2 class="title">Featured Products</h2>
      <div class="row">
        <div class="col-4">
          <img src="bil.avif" alt="Billentyűzet">
          <h4>Spirit of Gamer ELITE K70 RGB</h4>
          <div class="rating">
            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>
          </div>
          <p>50.00$</p>
        </div>
        <div class="col-4">
          <img src="videokartya.png" alt="Videókártya">
          <h4>RTX 5070 TI BY NVIDIA</h4>
          <div class="rating">
            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
          </div>
          <p>250.00$</p>
        </div>
        <div class="col-4">
          <img src="monitor.avif" alt="Monitor">
          <h4>Oddesey 244 HZ MONITOR</h4>
          <div class="rating">
            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
          </div>
          <p>60.00$</p>
        </div>
      </div>
    </div>
  </div>

  <div class="offer">
    <div class="small-container">
      <div class="row">
        <div class="col-2"><img src="Png.png" alt="Különleges" class="offer-img"></div>
        <div class="col-2">
          <p>Exclusively Available on MINISTORE!</p>
          <h1>GeForce RTX™ 5090 GAMING OC 32G</h1>  
          <small>Powered by the NVIDIA Blackwell architecture and DLSS 4</small><br>
          <small>Powered by GeForce RTX™ 5090</small><br>
          <small>Integrated with 32GB GDDR7 512bit memory interface</small><br>
          <small>WINDFORCE cooling system</small><br>
          <small>RGB Halo</small><br>
          <small>Dual BIOS (Performance / Silent)</small><br>
          <small>Reinforced structure</small><br>
          <a href="products.php" class="btn">Buy now!</a>
        </div>
      </div>
    </div>
  </div>

  <div class="testimontial">
    <div class="small-container">
      <div class="row">
        <div class="col-3">
          <i class="fa fa-quote-left"></i>
          <p>I recently upgraded to the GeForce RTX™ 5090 GAMING OC 32G, and I couldn’t be happier...</p>
          <div class="rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>
          <img src="user1.jpg"><h3>Ali-Hissam Raed</h3>
        </div>
        <div class="col-3">
          <i class="fa fa-quote-left"></i>
          <p>This graphics card exceeded my expectations in every possible way...</p>
          <div class="rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></div>
          <img src="user1.jpg"><h3>Big-Mike Moore</h3>
        </div>
        <div class="col-3">
          <i class="fa fa-quote-left"></i>
          <p>The GeForce RTX™ 5090 is simply a beast. From stunning visuals to unmatched performance...</p>
          <div class="rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i></div>
          <img src="user1.jpg"><h3>Shaun Dutche</h3>
        </div>
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
<script src="chat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
</html>