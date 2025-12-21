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
    <title>ASUS ROG Fusion II 500</title>
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
            <p class="sub">ASUS ROG Fusion II 500</p>
            <p class="price">$179</p>
          </div>
          <div class="image">
            <img src="https://dlcdnwebimgs.asus.com/gain/0B6F7E0D-DE7D-48BC-BBB1-75F35E2D5CC9/w717/h525/fwebp" alt="ASUS ROG Fusion II 500">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>ASUS ROG Fusion II 500</b> is a premium wired gaming headset offering hi‑res audio with ESS Quad‑DAC, virtual 7.1 surround sound, AI‑noise cancelling microphones and versatile connectivity for PC, consoles and mobile.</p>
            <br>
            <p><ul>
                <li><b>Driver size:</b> 50 mm ASUS Essence drivers.</li>
                <li><b>Microphone type:</b> Hidden AI Beamforming microphones with AI noise‑cancelling.</li>
                <li><b>Connectivity:</b> USB‑C / USB‑A / 3.5 mm wired — compatible with PC, Mac, PS5, Nintendo Switch, Xbox and mobile.</li>
                <li><b>Virtual surround sound:</b> 7.1 channel virtual surround sound powered by ESS 9280 Quad DAC.</li>
                <li><b>Weight:</b> Approx. 310 g.</li>
                <li><b>Frequency response / Impedance:</b> 20 Hz‑40 000 Hz / 32 Ω.</li>
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
          <h3>Hajas Máté and Hrustinszki Márton</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="ASUS ROG Fusion II 500">
            <input type="hidden" name="product_price" value="179">
            <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/0B6F7E0D-DE7D-48BC-BBB1-75F35E2D5CC9/w717/h525/fwebp">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
