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
    <title>NVIDIA RTX 5060 Ti DUAL</title>
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
            <h1>Graphics Card</h1>
            <p class="sub">NVIDIA RTX 5060 Ti DUAL</p>
            <p class="price">$799</p>
          </div>

          <div class="image">
            <img src="https://foramax.hu/image/cache/catalog/product/rtx5060ti_1-600x600.png" 
                 alt="NVIDIA RTX 5060 Ti DUAL">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>
              The <strong>NVIDIA RTX 5060 Ti DUAL</strong> is a high-performance graphics card designed for serious gamers 
              and content creators. With exceptional ray tracing capabilities and enhanced AI features, it offers 
              immersive gameplay and efficient rendering for creative workloads.
            </p>
            <br>
            <ul>
              <li><b>Memory:</b> 12GB GDDR6</li>
              <li><b>Architecture:</b> NVIDIA Ampere – Ray Tracing & DLSS support</li>
              <li><b>Cooling:</b> Dual-fan cooling system for optimal thermal management</li>
              <li><b>Gaming:</b> Ideal for 1440p and 4K gaming at high settings</li>
              <li><b>Ports:</b> HDMI 2.1, DisplayPort 1.4</li>
              <li><b>Use case:</b> High-end gaming, 3D rendering, and video production</li>
              <li><b>Design:</b> Dual-slot design for most gaming PCs</li>
            </ul>
          </div>

          <span class="stock"><i class="fa fa-check"></i> In stock</span>

          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-o"></i></li>
            </ul>
            <span>(15 reviews)</span>
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
            <input type="hidden" name="product_name" value="NVIDIA RTX 5060 Ti DUAL">
            <input type="hidden" name="product_price" value="799">
            <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/rtx5060ti_1-600x600.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
</main>

<script src="js.js"></script>
</body>
</html>
