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
    <title>55" Samsung Odyssey Ark G9 G97NC</title>
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
            <h1>Monitor</h1>
            <p class="sub">55" Odyssey Ark G9 G97NC UHD 165Hz (Curved)</p>
            <p class="price">$1499</p>
          </div>

          <div class="image">
            <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls55cg970nuxdu/gallery/hu-odyssey-ark-g97nc-ls55cg970nuxdu-538036806?$Q90_1920_1280_F_PNG$"
                 alt="55 Odyssey Ark G9 G97NC UHD 165Hz curved gaming monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Samsung "55" Odyssey Ark G9 G97NC</strong> is a flagship-class curved gaming monitor delivering a massive, immersive viewing experience with UHD resolution, smooth 165 Hz refresh rate, and cutting-edge design — perfect for gaming, multimedia and productivity.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 55 inches</li>
              <li><b>Resolution:</b> 4K UHD (3840×2160) for ultra-sharp visuals</li>
              <li><b>Curved design:</b> Deep curvature for cinematic immersion</li>
              <li><b>Refresh rate:</b> 165 Hz for fluid gameplay and smooth motion</li>
              <li><b>HDR support:</b> Advanced HDR for vibrant contrast and color depth</li>
              <li><b>Panel type:</b> VA panel optimized for large-format displays</li>
              <li><b>Connectivity:</b> Multiple inputs for gaming consoles, PC or media streaming devices</li>
              <li><b>Use-case:</b> Ideal for immersive gaming, movies, as well as productivity or multitasking on a big screen</li>
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
            <span>(3 reviews)</span>
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
            <input type="hidden" name="product_name" value="Samsung 55 Odyssey Ark G9 G97NC">
            <input type="hidden" name="product_price" value="1500">
            <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls55cg970nuxdu/gallery/hu-odyssey-ark-g97nc-ls55cg970nuxdu-538036806?$Q90_1920_1280_F_PNG$">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>

