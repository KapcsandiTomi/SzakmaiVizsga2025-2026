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
            <p class="sub">Logitech G PRO X</p>
            <p class="price">$100</p>
          </div>
          <div class="image">
            <img src="https://image.alza.cz/products/JL288g6a/JL288g6a.jpg?width=500&height=500" alt="">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <strong>Logitech G PRO X Wired Gaming Headset</strong> is a professional-grade headset designed for competitive gaming and content creation.
It features 50 mm PRO-G hybrid mesh drivers, providing clear and accurate sound reproduction with a frequency response of 20 Hz to 20 kHz and an impedance of around 32 Î©.</p>
            <br>
            <p><ul>
              <li><b>Driver size:</b> 50 mm PRO-G hybrid mesh</li>
              <li><b>Microphone type:</b> Detachable cardioid (6 mm)</li>
              <li><b>Connectivity:</b> 3.5 mm analog / USB DAC</li>
              <li><b>Weight:</b> Approx. 320 g (without cable)</li>
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
            <span>(1 reviews)</span>
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
            <input type="hidden" name="product_name" value="Logitech G PRO X Wired Gaming Headset">
            <input type="hidden" name="product_price" value="100">
            <input type="hidden" name="product_image" value="https://image.alza.cz/products/JL288g6a/JL288g6a.jpg?width=500&height=500">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
