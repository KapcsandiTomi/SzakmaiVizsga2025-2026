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
    <title>MSI MPG 271QR QD-OLED</title>
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
            <p class="sub">MSI MPG 271QR QD-OLED</p>
            <p class="price">$499</p>
          </div>

          <div class="image">
            <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MPG/271QR-QD-OLED-X50/kv-pd.webp"
                 alt="MSI MPG 271QR QD-OLED Monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>MSI MPG 271QR QD-OLED</strong> is a high-end QD-OLED gaming monitor offering exceptional color, contrast, and smooth performance — ideal for competitive gaming, creative work, and immersive media consumption.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 27 inches</li>
              <li><b>Panel type:</b> QD-OLED — rich colors and deep blacks</li>
              <li><b>Resolution:</b> QHD (2560×1440) for sharp detail</li>
              <li><b>Refresh rate:</b> 165 Hz for smooth motion (or specifikáld, ha eltér)</li>
              <li><b>Response time:</b> Ultra fast OLED response for minimal motion blur</li>
              <li><b>Color performance:</b> Excellent contrast, wide color gamut ideal for gaming & content creation</li>
              <li><b>Use-case:</b> Gaming, graphic design, video editing, general desktop use</li>
            </ul>
          </div>

          <span class="stock"><i class="fa fa-pen"></i> In stock</span>

          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class arefa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-o"></i></li>
            </ul>
            <span>(0 reviews)</span>
          </div>
        </div>
      </div>

      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Kapcsándi Tamás</h3>
        </div>

        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="MSI MPG 271QR QD-OLED">
            <input type="hidden" name="product_price" value="500">
            <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MPG/271QR-QD-OLED-X50/kv-pd.webp">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>
