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
    <title>Logitec G Pro X</title>
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
            <p class="sub">ASUS ROG Delta S Wireless</p>
            <p class="price">$50</p>
          </div>
          <div class="image">
            <img src="https://dlcdnwebimgs.asus.com/gain/F223AA9E-DC85-421B-990A-EA07C27D19E5/w717/h525/fwebp" alt="ROG Delta S Wireless">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>ASUS ROG Delta S Wireless</b> delivers top-tier audio performance and comfort for gamers who demand flexibility and freedom from cables. Equipped with dual wireless connectivity and AI-enhanced microphones, this headset ensures crystal-clear communication and immersive sound across multiple platforms.</p>
            <br>
            <p><ul>
                <li><b>Driver size:</b> 50 mm ASUS Essence drivers</li>
                <li><b>Microphone type:</b> AI Beamforming with AI Noise-Cancelation (hidden dual microphones)</li>
                <li><b>Connectivity:</b> 2.4 GHz RF wireless / Bluetooth 5.0</li>
                <li><b>Battery life:</b> Up to 25 hours (USB-C fast charging: 15 min = 3 hours)</li>
                <li><b>Compatibility:</b> PC, Mac, PlayStation 5, Nintendo Switch, Mobile</li>
                <li><b>Weight:</b> Approx. 318 g</li>
                </ul></p>
          </div>
          <span class="stock"><i class="fa fa-pen"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-o"></i></li>
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
            <input type="hidden" name="product_name" value="ASUS ROG Delta S Wireless">
            <input type="hidden" name="product_price" value="50">
            <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/F223AA9E-DC85-421B-990A-EA07C27D19E5/w717/h525/fwebp">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>