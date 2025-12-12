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
    <title>Logitech G502 X LIGHTSPEED</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
            <p class="sub">Logitech G502 X LIGHTSPEED</p>
            <p class="price">$119</p>
          </div>
          <div class="image">
            <img src="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-lightspeed/gallery/g502-x-lightspeed-mouse-top-angle-white-gallery-1.png?v=1" alt="Logitech G502 X LIGHTSPEED">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Logitech G502 X LIGHTSPEED</b> is a high‑performance wireless gaming mouse combining the classic customizable shape of the G502 series with modern low‑latency wireless technology. It’s designed for gamers who want reliable responsiveness, ergonomic comfort, and versatile button configuration across genres.</p>
            <br>
            <p><ul>
                <li><b>Sensor:</b> High‑precision optical sensor (configurable DPI)</li>
                <li><b>Switches:</b> Durable gaming‑grade switches</li>
                <li><b>Connectivity:</b> LIGHTSPEED Wireless / USB‑C</li>
                <li><b>Features:</b> Customizable buttons, adjustable DPI, ergonomic shape</li>
                <li><b>Compatibility:</b> PC, Mac, supported platforms</li>
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
            <input type="hidden" name="product_name" value="Logitech G502 X LIGHTSPEED">
            <input type="hidden" name="product_price" value="119">
            <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_386,ar_1.0,c_limit,f_auto,q_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/g502x-lightspeed/gallery/g502-x-lightspeed-mouse-top-angle-white-gallery-1.png?v=1">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
