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
  <title>Redragon EISA K686 PRO SE</title>
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
          <p class="sub">Redragon EISA K686 PRO SE (Anime)</p>
          <p class="price">$62.99</p> 
        </div>
        <div class="image">
          <img src="https://redragonshop.com/cdn/shop/files/RedragonEISAK686PRO98KeysWirelessGasketRGBGamingKeyboard_1.png?v=1762463457&width=713" alt="Redragon EISA K686 PRO SE Keyboard">
        </div>
      </div>
      <div class="half">
        <div class="description">
          <p>The <strong>Redragon EISA K686 PRO SE</strong> is a stylish 98‑key wireless gaming keyboard featuring a gasket-mounted design for soft, cushioned typing feedback. With hi‑fi linear switches, tri‑mode connectivity, and a 3000 mAh battery, it's thoughtfully built for both performance and aesthetics.</p>
          <br />
          <ul>
            <li><b>Switches:</b> Custom hi‑fi linear, lubed</li>
            <li><b>Key layout:</b> 98 keys (compact full-size)</li>
            <li><b>Connectivity:</b> USB‑C wired, Bluetooth 3.0/5.0, 2.4 GHz wireless</li>
            <li><b>Dampening:</b> 5-layer sound absorbing (foams + gasket)</li>
            <li><b>Battery:</b> 3000 mAh rechargeable</li>
            <li><b>Keycaps:</b> 5-side dye‑sub PBT with anime-themed Eisa design</li>
            <li><b>Control:</b> One knob for media and brightness control</li>
            <li><b>Hot-swap:</b> 3/5-pin compatible hot-swap sockets</li>
            <li><b>Software:</b> Redragon Pro software for RGB, macros, layers</li>
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
          <span>(~177 reviews on official site) :contentReference[oaicite:18]{index=18}</span>
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
            <input type="hidden" name="product_name" value="Redragon EISA K686 PRO SE">
            <input type="hidden" name="product_price" value="63">
            <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonEISAK686PRO98KeysWirelessGasketRGBGamingKeyboard_1.png?v=1762463457&width=713">
            <button type="submit">Order now</button>
          </form>
        </div>
    </div>
  </div>
</main>
<script src="js.js"></script>
</body>
</html>

