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
  <title>Redragon ANTONIUM K745 PRO</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
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
            <p class="sub">Redragon ANTONIUM K745 PRO</p>
            <p class="price">$64.99</p>
          </div>
          <div class="image">
            <img src="https://redragonshop.com/cdn/shop/files/RedragonANTONIUMK745PROKeyboard_2.png?v=1761211643&width=713" alt="Redragon ANTONIUM K745 PRO">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Redragon ANTONIUM K745 PRO</strong> is a full‑size mechanical keyboard with a gasket-mounted design and smooth linear Mint Mambo switches. It offers tri-mode connectivity (USB‑C, Bluetooth 3.0/5.0, 2.4 GHz), hot‑swappable sockets, and a rich sound profile thanks to its five‑layer damping system.</p>
            <br />
            <ul>
              <li><b>Switch type:</b> Mint Mambo (linear)</li>
              <li><b>Mount:</b> Gasket mount</li>
              <li><b>Sound dampening:</b> 5-layer foam</li>
              <li><b>Keys:</b> 108 (full-size) + 4 shortcut keys</li>
              <li><b>Connectivity:</b> USB‑C wired, Bluetooth 3.0/5.0, 2.4 GHz wireless</li>
              <li><b>Lighting:</b> RGB, south-facing LEDs</li>
              <li><b>Extras:</b> Hot-swappable (3/5 pin), Pro software, adjustable feet</li>
            </ul>
          </div>
          <span class="stock"><i class="fa fa-check"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-half-o"></i></li>
              <li><i class="fa fa-star-o"></i></li>
            </ul>
            <span>(55 reviews)</span>
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
            <input type="hidden" name="product_name" value="Redragon ANTONIUM K745 PRO">
            <input type="hidden" name="product_price" value="65">
            <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonANTONIUMK745PROKeyboard_2.png?v=1761211643&width=713">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="js.js"></script>
</body>
</html>
