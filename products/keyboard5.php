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
    <title>White Shark SHINOBI 2 White CZ/SK</title>
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
            <h1>Keyboard</h1>
            <p class="sub">White Shark SHINOBI 2 White CZ/SK</p>
            <p class="price">$45</p>
          </div>
          <div class="image">
            <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e3fc2de-9013-4019-bb62-75e75684d856/conversions/SHINOBI-2-CZSK-White-Red-Switch-%281%29-thumb.png" alt="SHINOBI 2 White CZ/SK">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <strong>White Shark SHINOBI 2 White CZ/SK</strong> is a compact mechanical gaming keyboard with high-performance red switches. 
            Its responsive keys, customizable RGB lighting, and durable design make it ideal for fast-paced gaming and everyday use.</p>
            <br>
            <ul>
              <li><b>Switch type:</b> Mechanical Red switches</li>
              <li><b>Layout:</b> CZ/SK</li>
              <li><b>Backlight:</b> RGB with multiple lighting modes</li>
              <li><b>Connection:</b> USB 2.0 wired</li>
              <li><b>Anti-ghosting:</b> 26-key anti-ghosting</li>
              <li><b>Build:</b> Compact, durable design</li>
              <li><b>Cable length:</b> 1.5 m braided cable</li>
            </ul>
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
            <span>(2 reviews)</span>
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
            <input type="hidden" name="product_name" value="White Shark SHINOBI 2 White CZ/SK">
            <input type="hidden" name="product_price" value="45">
            <input type="hidden" name="product_image" value="https://smit-electronic.s3.eu-central-1.amazonaws.com/9e3fc2de-9013-4019-bb62-75e75684d856/conversions/SHINOBI-2-CZSK-White-Red-Switch-%281%29-thumb.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
