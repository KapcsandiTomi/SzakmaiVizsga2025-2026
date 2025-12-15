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
    <title>27" Samsung Odyssey OLED G6 G60SD</title>
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
            <p class="sub">27" Odyssey OLED G6 G60SD QHD 360Hz</p>
            <p class="price">$899</p>
          </div>

          <div class="image">
            <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls27dg602suxen/gallery/hu-odyssey-oled-g6-g60sd-ls27dg602suxen-541135297?$Q90_1920_1280_F_PNG$"
                 alt="27 Odyssey OLED G6 G60SD QHD 360Hz gaming monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Samsung 27 Odyssey OLED G6 G60SD</strong> is a cutting-edge QHD gaming monitor designed for elite performance, featuring ultra-fast response times, exceptional OLED picture quality, and an incredible 360Hz refresh rate.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 27 inches</li>
              <li><b>Panel type:</b> OLED — perfect blacks & infinite contrast</li>
              <li><b>Resolution:</b> QHD 2560×1440</li>
              <li><b>Refresh rate:</b> 360Hz for top-tier competitive gameplay</li>
              <li><b>Response time:</b> 0.03ms (GtG) lightning-fast pixel transitions</li>
              <li><b>Color:</b> Vibrant HDR performance with exceptional color accuracy</li>
              <li><b>Sync technology:</b> AMD FreeSync Premium Pro</li>
              <li><b>Design:</b> Sleek gamer-focused minimal bezel design</li>
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
            <span>(7 reviews)</span>
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
            <input type="hidden" name="product_name" value="Samsung 27 Odyssey OLED G6 G60SD">
            <input type="hidden" name="product_price" value="900">
            <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls27dg602suxen/gallery/hu-odyssey-oled-g6-g60sd-ls27dg602suxen-541135297?$Q90_1920_1280_F_PNG$">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>
