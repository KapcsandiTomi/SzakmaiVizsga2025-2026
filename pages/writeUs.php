<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/../handler/maintenance_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Write Us</title>
    <link rel="stylesheet" href="../assets/css/writeUs.css">
    <link rel="stylesheet" href="../assets/css/user-navbar.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css"/>
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
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
$currentPage = 'writeUs.php';
include __DIR__ . '/includes/user-navbar.php';
?>
<br>

<main>
  <div class="contact-container">
    <h1>Write <span>Us</span></h1>
    <form action="https://api.web3forms.com/submit" method="POST" id="contactForm" class="contact-form">
  
  <input type="hidden" name="access_key" value="270a700b-95e4-4c00-92b5-b56c03fbdaa5">

  
  <input type="hidden" name="subject" value="You have a new report from your website!">
  <input type="hidden" name="from_name" value="AQUA MINI SHOP (AMS)">

 
  <input type="checkbox" name="botcheck" class="hidden" style="display:none;">

  
  <input type="text" name="name" placeholder="Full Name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="text" name="subject" placeholder="Subject" required>
  <textarea name="message" placeholder="Your Message" required></textarea>

  <button type="submit">Send Message</button>

  
  <div id="form-status" style="margin-top:10px; font-weight:500;"></div>
</form> 
  </div>
</main>



<br>
<br>
   <footer>
  <div class="container">
    <div class="footer-col">
      <h2>OUR MOTTO<div class="underline"><span></span></div></h2>
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
        <a href="https://x.com/tamas_kapc343"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
</footer>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/javas.js"></script>
<script src="../assets/js/writeus.js"></script>
<script src="../assets/js/chat.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
</html>


