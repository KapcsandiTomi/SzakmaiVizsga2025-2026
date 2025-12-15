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
    <title>Logitech PRO X2 SUPERSTRIKE</title>
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
            <h1>Mouse</h1>
            <p class="sub">Logitech PRO X2 SUPERSTRIKE</p>
            <p class="price">$139</p>
          </div>
          <div class="image">
            <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x2-superstrike-pdp/gallery/pro-x2-superstrike-top-angle-gallery-1.png?v=1" alt="Logitech PRO X2 SUPERSTRIKE">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Logitech PRO X2 SUPERSTRIKE</b> is a premium gaming mouse engineered for high performance, low latency and ergonomic handling — ideal for competitive gaming or intensive use. With its top‑tier sensor, lightweight build and responsive switches, it ensures precise tracking and swift reactions during intense gameplay.</p>
            <br>
            <p><ul>
                <li><b>Sensor:</b> High‑precision optical sensor (configurable DPI)</li>
                <li><b>Switches:</b> Durable gaming‑grade switches</li>
                <li><b>Connectivity:</b> Wireless / USB‑C (depending on version)</li>
                <li><b>Weight:</b> Lightweight design optimized for agility</li>
                <li><b>Compatibility:</b> PC, Mac, supported platforms</li>
                <li><b>DPI / Tracking:</b> High accuracy, adjustable tracking settings</li>
                </ul></p>
          </div>
          <span class="stock"><i class="fa fa-pen"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-o"></i></li>
            </ul>
            <span>(0 reviews)</span>
          </div>
        </div>
      </div>
      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Tamás Kapcsándi</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="Logitech PRO X2 SUPERSTRIKE">
            <input type="hidden" name="product_price" value="139">
            <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x2-superstrike-pdp/gallery/pro-x2-superstrike-top-angle-gallery-1.png?v=1">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
