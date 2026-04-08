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
    <title>Aqua Mini Shop - FAQ</title>
    <link rel="stylesheet" href="../assets/css/faq.css">
    <link rel="stylesheet" href="../assets/css/user-navbar.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
$currentPage = 'faq.php';
include __DIR__ . '/includes/user-navbar.php';
?>
<br>

<section class="faq-header">
  <h1>Do You Have Questions?</h1>
  <h2>We have answers (well, most of the times!)</h2>
  <p>
    Below you’ll find answers to the most common questions you may have on our products & services. 
    Also, feel free to check out our 
    <a href="https://www.facebook.com/groups/780808788334912">Facebook</a>,<a href="https://www.instagram.com/aqua.mini.shop/">Instagram</a>,<a href="https://x.com/tamas_kapc343">X</a>
    If you still can’t find the answer you’re looking for, just 
    <a href="writeUs.php">Write US!</a>
  </p>
  <div class="faq-image">
    <img src="../img/faq.jpg" alt="FAQ">
  </div>
  </section>
  <section class="faq-section">
    <h1 class="faq-title">Frequently Asked Questions</h1>
    <div class="faq"> 
      <h2 class="faq-title-h2">General Shopping & Products</h2>

      <div class="faq-item">
        <button class="faq-question">
          What payment methods do you accept?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>We accept all major credit and debit cards, like Mastercard, and other secure payment options shown at checkout.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          How do I place an order?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Simply browse our products, add your favorites to the cart, and follow the checkout steps to complete your purchase.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          Can I change or cancel my order after placing it?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Orders can only be changed or canceled within a short period. Please call +36 70 645 1793 right away.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          Do you offer discounts, promotions, or gift cards?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Yes! We regularly run promotions and offer gift cards. Subscribe to our newsletter to stay updated.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          Are your products authentic/guaranteed?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Absolutely. All our products are 100% authentic and covered by our quality guarantee.</p>
        </div>
      </div>


      <h2 class="faq-title-h2">Shipping & Delivery</h2>

      <div class="faq-item">
        <button class="faq-question">
          What are your shipping options and costs?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>We offer standard and express shipping. Costs depend on your location and will be shown at checkout.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          How long does delivery take?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Standard shipping usually takes 3–7 business days, while express delivery is 1–3 business days.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          Do you ship internationally?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Yes, we ship worldwide. International shipping rates and times vary by country.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          What should I do if my package hasn’t arrived?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>If your order hasn’t arrived within the estimated time, please contact our support team with your order number.</p>
        </div>
      </div>


      <h2 class="faq-title-h2">Returns & Exchanges</h2>

      <div class="faq-item">
        <button class="faq-question">
          What is your return policy?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>We accept returns within 30 days of delivery as long as items are unused and in original packaging.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          How do I return or exchange an item?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Simply contact us for return instructions, then send your item back to our warehouse.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          Who pays for return shipping?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>If the return is due to our error (wrong item, damaged product), we cover return shipping. Otherwise, the buyer is responsible.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          How long does it take to process a refund?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Refunds are usually processed within 5–10 business days after we receive your return.</p>
        </div>
      </div>



      <h2 class="faq-title-h2">Account & Security</h2>

      <div class="faq-item">
        <button class="faq-question">
          Do I need an account to shop?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Yes you need account for buy anything.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          Is my payment information secure?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Yes, we use SSL encryption and trusted payment gateways to keep your information safe.</p>
        </div>
      </div>



      <h2 class="faq-title-h2">Customer Support</h2>

      <div class="faq-item">
        <button class="faq-question">
          How can I contact customer service?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>You can reach us by email at kapcsandi.tomi@gmail.com or through our contact form.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          What are your customer support hours?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Our team is available Monday–Saturday, 9 AM–7 PM (local time).</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">
          Do you offer live chat or phone support?
          <span class="icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Yes, we provide live chat on our website and phone support during business hours.</p>
        </div>
      </div>

    </div>
  </section>
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
<script src="../assets/js/javas.js"></script>
<script src="../assets/js/faq.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/chat.js"></script>
</html>

