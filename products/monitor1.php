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
    <title>32&quot; Samsung Odyssey G5 G51F</title>
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
            <h1>Monitor</h1>
            <p class="sub">32 Odyssey G5 G51F QHD 180Hz</p>
            <p class="price">$350</p>
          </div>

          <div class="image">
            <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fg510euxen/gallery/hu-odyssey-g5-g51f-ls32fg510euxen-548700337?$Q90_1920_1280_F_PNG$" 
                 alt="32 Odyssey G5 G51F QHD 180Hz gaming monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Samsung 32 Odyssey G5 G51F</strong> is a high-performance QHD gaming monitor built for smooth gameplay, 
            immersive visuals, and competitive-level responsiveness.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 32 inches</li>
              <li><b>Resolution:</b> QHD 2560×1440 for stunning detail</li>
              <li><b>Refresh rate:</b> Ultra-fast 180Hz for fluid motion</li>
              <li><b>Response time:</b> 1ms (MPRT) for blur-free gaming</li>
              <li><b>Panel type:</b> VA panel with high contrast</li>
              <li><b>HDR:</b> HDR10 support for enhanced color and brightness</li>
              <li><b>Adaptive sync:</b> AMD FreeSync Premium</li>
              <li><b>Design:</b> Minimalistic dark design ideal for gaming setups</li>
            </ul>
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
            <span>(14 reviews)</span>
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
            <input type="hidden" name="product_name" value="Odyssey G5 G51F QHD 180Hz">
            <input type="hidden" name="product_price" value="350">
            <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fg510euxen/gallery/hu-odyssey-g5-g51f-ls32fg510euxen-548700337?$Q90_1920_1280_F_PNG$">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>
