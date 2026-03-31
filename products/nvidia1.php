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
    <title>NVIDIA RTX 5050 Dual 8GB</title>
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
            <h1>Graphics Card</h1>
            <p class="sub">NVIDIA RTX 5050 Dual 8GB GDDR6</p>
            <p class="price">$499</p>
          </div>

          <div class="image">
            <img src="https://foramax.hu/image/cache/catalog/product/VGA_PALIT_NVIDIA_RTX5050_Dual_8GB_GDDR6_-_NE65050019P1-GB2070D-i769814-600x600.png"
                 alt="NVIDIA RTX 5050 Dual 8GB graphics card">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>
              The <strong>NVIDIA RTX 5050 Dual 8GB</strong> delivers powerful next-generation performance
              for modern gaming and creative workloads. With ray tracing support and AI-enhanced graphics,
              it offers smooth gameplay and stunning visuals.
            </p>
            <br>
            <ul>
              <li><b>Memory:</b> 8GB GDDR6</li>
              <li><b>Architecture:</b> NVIDIA RTX – Ray Tracing & DLSS support</li>
              <li><b>Cooling:</b> Dual-fan cooling system for stable performance</li>
              <li><b>Gaming:</b> Ideal for 1080p & 1440p high settings</li>
              <li><b>Ports:</b> HDMI & DisplayPort support</li>
              <li><b>Use case:</b> Gaming, streaming, content creation</li>
              <li><b>Design:</b> Compact dual-slot design fits most PC builds</li>
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
            <span>(12 reviews)</span>
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
            <input type="hidden" name="product_name" value="NVIDIA RTX 5050 Dual 8GB">
            <input type="hidden" name="product_price" value="499">
            <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/VGA_PALIT_NVIDIA_RTX5050_Dual_8GB_GDDR6_-_NE65050019P1-GB2070D-i769814-600x600.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
</main>

<script src="js.js"></script>
</body>
</html>

