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
    <title>Logitech Astro A20 X</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
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
            <p class="sub">Logitech Astro A20 X</p>
            <p class="price">$120</p>
          </div>
          <div class="image">
            <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/a20-x-pdp/gallery/a20x-3qtr-front-with-receiver-black-gallery-1-new.png" alt="Logitech Astro A20 X">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The Logitech Astro A20 X is a multi-platform wireless (with optional wired) gaming headset designed for gamers, featuring a lightweight design and modern audio and microphone capabilities.</p>
            <br>
            <p><ul>
              <li><b>Speakers:</b> 40 mm PRO‑G (bio‑cellulose diaphragm) drivers.</li>
              <li><b>Frequency Response:</b> 20 Hz–20 kHz.</li>
              <li><b>Weight:</b> ~290 g.</li>
              <li><b>Dual Device Connection (“PlaySync”):</b> Allows switching between two devices simultaneously.</li>
              <li><b>Battery Life:</b> ~40 hours with RGB lighting, up to ~90 hours without lighting.</li>
              <li><b>Microphone:</b> Detachable, 48 kHz sample rate, LED indicator for mute status.</li>
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
            <span>(1 reviews)</span>
          </div>
        </div>
      </div>
      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Hajas Máté</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="Logitech Astro A20 X">
            <input type="hidden" name="product_price" value="120">
            <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/a20-x-pdp/gallery/a20x-3qtr-front-with-receiver-black-gallery-1-new.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
