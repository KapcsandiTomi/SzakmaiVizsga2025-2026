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
  <title>Redragon ARTEMIS K719 PRO Graffiti</title>
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
            <p class="sub">Redragon ARTEMIS K719 PRO Graffiti</p>
            <p class="price">$49.99</p>
          </div>
          <div class="image">
            <img src="https://redragonshop.com/cdn/shop/files/RedragonARTEMISK719PROGraffitiKeyboard_1.png?v=1762848339&width=713" alt="Redragon ARTEMIS K719 PRO Graffiti">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Redragon ARTEMIS K719 PRO Graffiti</strong> fuses bold street‑art style with performance, delivering a tenkeyless mechanical keyboard with smooth linear switches, full RGB lighting, and a sturdy aluminium top plate. Perfect for gamers and creatives alike.</p>
            <br />
            <ul>
              <li><b>Switch type:</b> Linear mechanical switches</li>
              <li><b>Layout:</b> 87-key TKL</li>
              <li><b>Connection:</b> USB‑C wired</li>
              <li><b>Anti-ghosting:</b> N-key rollover</li>
              <li><b>Build:</b> Aluminium top plate</li>
              <li><b>Lighting:</b> Full RGB with multiple effects</li>
              <li><b>Extras:</b> Media controls via function layer</li>
              <li><b>Cable:</b> Detachable braided USB‑C</li>
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
            <span>(~12 reviews)</span>
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
            <input type="hidden" name="product_name" value="Redragon ARTEMIS K719 PRO Graffiti">
            <input type="hidden" name="product_price" value="50">
            <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonARTEMISK719PROGraffitiKeyboard_1.png?v=1762848339&width=713">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
  <script src="js.js"></script>
</body>
</html>

