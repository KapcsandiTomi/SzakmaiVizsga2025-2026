<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logitech G733 LIGHTSPEED</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="letoles.jpg" type="image/png">
</head>
<body>
<main>
    <div class="card">
      <div class="card__title">
        <div class="icon">
          <a href="../products.php"><i class="fa fa-arrow-left"></i></a>
        </div>
        <h3>New product</h3>
      </div>
      <div class="card__body">
        <div class="half">
          <div class="featured_text">
            <h1>Headphones</h1>
            <p class="sub">Logitech Pro G733</p>
            <p class="price">$90</p>
          </div>
          <div class="image">
            <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/g733/gallery/g733-black-gallery-3.png" alt="Logitech Pro G733" alt="Logitech Pro G733">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <strong>Logitech G733 LIGHTSPEED</strong> is a wireless RGB gaming headset built for comfort, style and long gaming sessions.</p>
            <br>
            <p><ul>
              <li><b>Drivers:</b> 40 mm PRO‑G drivers, designed to deliver precise audio across games and media.</li>
              <li><b>Frequency Response:</b> 20 Hz–20 kHz, covering the full range of human hearing.</li>
              <li><b>Impedance:</b> 39 Ω (passive) and up to 5 kΩ (active) – allowing for effective driver control when powered.</li>
              <li><b>Weight:</b> Approximately 278 g – very lightweight for extended use.</li>
              <li><b>Battery Life:</b> Up to ~29 hours (with RGB off) and ~20 hours with lighting on.</li>
              <li><b>Microphone:</b> Detachable boom mic, cardioid pattern, frequency response ~100 Hz‑10 kHz.</li>
              <li><b>Connectivity:</b> 2.4 GHz LIGHTSPEED wireless (USB‑A dongle), up to ~20 m range.</li>
            </ul></p>
          </div>
          <span class="stock"><i class="fa fa-pen"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
            </ul>
            <span>(0 reviews)</span>
          </div>
        </div>
      </div>
      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Kapcsándi Tamás</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="Logitech G733 LIGHTSPEED">
            <input type="hidden" name="product_price" value="90">
            <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/g733/gallery/g733-black-gallery-3.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>