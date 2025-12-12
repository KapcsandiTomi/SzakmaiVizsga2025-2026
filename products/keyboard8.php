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
  <title>Redragon EISA K686 HE</title>
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
            <p class="sub">Redragon EISA K686 HE Rapid Trigger</p>
            <p class="price">$62.99</p>
          </div>
          <div class="image">
            <img src="https://redragonshop.com/cdn/shop/files/RedragonEISAK686HERapidTriggerGamingKeyboard_1.png?v=1760435423&width=713" alt="Redragon EISA K686 HE">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <strong>Redragon EISA K686 HE</strong> offers cutting-edge magnetic UltraMag switches and Rapid Trigger technology to let you fine-tune the actuation point between 0.1 mm and 3.4 mm. With 8 000 Hz hyper-polling and a compact 98-key layout plus a media knob, it's designed for gamers who demand precision and responsiveness.</p>
            <br />
            <ul>
              <li><b>Switch type:</b> UltraMag 100% POM magnetic switches</li>
              <li><b>Actuation (Rapid Trigger):</b> 0.1 – 3.4 mm (software-adjustable)</li>
              <li><b>Polling rate:</b> 8 000 Hz</li>
              <li><b>Keys:</b> 98 (compact full-size) + media knob</li>
              <li><b>Dampening:</b> 2‑layer noise dampening</li>
              <li><b>Keycaps:</b> Round PBT</li>
              <li><b>Connection:</b> USB‑C wired</li>
              <li><b>Extras:</b> PRCS Tech (Snap Tap & SOCD)</li>
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
            <span>(~8 reviews)</span>
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
            <input type="hidden" name="product_name" value="Redragon EISA K686 HE Rapid Trigger">
            <input type="hidden" name="product_price" value="63">
            <input type="hidden" name="product_image" value="https://redragonshop.com/cdn/shop/files/RedragonEISAK686HERapidTriggerGamingKeyboard_1.png?v=1760435423&width=713">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
  <script src="js.js"></script>
</body>
</html>
