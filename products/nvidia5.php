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
    <title>NVIDIA RTX 5070 Ti GAMING OC 16GB</title>
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
            <p class="sub">NVIDIA RTX 5070 Ti GAMING OC 16GB</p>
            <p class="price">$899</p>
          </div>

          <div class="image">
            <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205070%20Ti%20GAMING%20OC%2016G-01-600x600.png"
                 alt="NVIDIA RTX 5070 Ti GAMING OC 16GB">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>
              The <strong>NVIDIA RTX 5070 Ti GAMING OC 16GB</strong> is built for high-end gaming and advanced 
              creative workloads. With factory overclocking, next-generation ray tracing, and massive VRAM, 
              it delivers ultra-smooth performance at 1440p and 4K resolutions.
            </p>
            <br>
            <ul>
              <li><b>Memory:</b> 16GB GDDR6</li>
              <li><b>Architecture:</b> NVIDIA RTX – Ray Tracing & DLSS</li>
              <li><b>Cooling:</b> Advanced triple-fan GAMING OC cooling system</li>
              <li><b>Gaming:</b> Perfect for 1440p Ultra & 4K High settings</li>
              <li><b>Ports:</b> HDMI 2.1, DisplayPort 1.4a</li>
              <li><b>Use case:</b> High-end gaming, streaming, 3D & video rendering</li>
              <li><b>Design:</b> Premium RGB GAMING OC design</li>
            </ul>
          </div>

          <span class="stock"><i class="fa fa-check"></i> In stock</span>

          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
            </ul>
            <span>(21 reviews)</span>
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
            <input type="hidden" name="product_name" value="NVIDIA RTX 5070 Ti GAMING OC 16GB">
            <input type="hidden" name="product_price" value="899">
            <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%205070%20Ti%20GAMING%20OC%2016G-01-600x600.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
</main>

<script src="js.js"></script>
</body>
</html>

