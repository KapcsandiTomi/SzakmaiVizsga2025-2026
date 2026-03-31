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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Redragon FIZZ K617</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
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
            <h1>Keyboard</h1>
            <p class="sub">Redragon FIZZ K617 (Magnetic)</p>
            <p class="price">$64.99</p>
          </div>
          <div class="image">
            <img src="https://redragonshop.com/cdn/shop/files/RedragonFIZZK61760RapidTriggerMagneticSwitchGamingKeyboard_1_1.png?v=1762463308&width=713" alt="Redragon FIZZ K617 Magnetic">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Redragon FIZZ K617</strong> features Hall-effect magnetic switches combined with Rapid Trigger technology to offer unmatched input precision and speed. The actuation point can be adjusted from 0.1 mm up to ~4 mm via software, making it an excellent choice for competitive gaming.</p>
            <br />
            <ul>
              <li><b>Polling rate:</b> 8 000 Hz</li>
              <li><b>Switch type:</b> Magnetic (Hall-effect)</li>
              <li><b>Actuation point:</b> 0.1 – ~4 mm (software adjustable)</li>
              <li><b>Layout:</b> 60 % (61 keys)</li>
              <li><b>Backlight:</b> RGB, 20 preset modes</li>
              <li><b>Connectivity:</b> Wired via detachable USB‑C cable</li>
              <li><b>Extras:</b> Rapid Trigger, 2 actions per key (nyomásérzékenység alapján)</li>
            </ul>
          </div>
          <span class="stock"><i class="fa fa-check"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-empty"></i></li>
              <li><i class="fa fa-star-empty"></i></li>
            </ul>
            <span>(~5 reviews)</span>
          </div>
        </div>
      </div>

      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Hrustinszki Márton</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="Redragon FIZZ K617 (Magnetic)">
            <input type="hidden" name="product_price" value="65">
            <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonFIZZK61760RapidTriggerMagneticSwitchGamingKeyboard_1_1.png?v=1762463308&width=713">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="js.js"></script>
</body>
</html>

