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
    <title>ASUS ROG Strix Go Core</title>
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
            <p class="sub">ASUS ROG Strix Go Core</p>
            <p class="price">$79</p>
          </div>
          <div class="image">
            <img src="https://dlcdnwebimgs.asus.com/gain/2FAA2882-5564-4A2A-80DA-C76BA236E933/w717/h525/fwebp" alt="ASUS ROG Strix">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>ASUS ROG Strix Go Core</b> is a wired gaming headset built for cross-platform play — offering immersive sound, comfortable fit and lightweight portability for extended sessions.</p>
            <br>
            <p><ul>
                <li><b>Driver size:</b> 40 mm ASUS Essence drivers for detailed and immersive audio.</li>
                <li><b>Microphone type:</b> Detachable unidirectional boom mic, certified by Discord and TeamSpeak for clear team communication.</li>
                <li><b>Connectivity:</b> Wired 3.5 mm jack — compatible with PC, Mac, PS5, Xbox One, Nintendo Switch and mobile devices.</li>
                <li><b>Impedance:</b> 32 Ω.</li>
                <li><b>Weight:</b> Approx. 252 g.</li>
                <li><b>Virtual surround:</b> Supports Windows Sonic virtual 7.1 surround sound.</li>
              </ul></p>
          </div>
          <span class="stock"><i class="fa fa-pen"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-half-o"></i></li>
              <li><i class="fa fa-star-o"></i></li>
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
            <input type="hidden" name="product_name" value="ASUS ROG Strix">
            <input type="hidden" name="product_price" value="79">
            <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/2FAA2882-5564-4A2A-80DA-C76BA236E933/w717/h525/fwebp">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
