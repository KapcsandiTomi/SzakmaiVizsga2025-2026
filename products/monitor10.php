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
    <title>MSI MAG 272QP QD-OLED X24</title>
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
            <h1>Monitor</h1>
            <p class="sub">MSI MAG 272QP QD-OLED X24</p>
            <p class="price">$699</p>
          </div>

          <div class="image">
            <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QP-QD-OLED-X24/mag-272qp-qd-oled-x24-kv.webp"
                 alt="MSI MAG 272QP QD-OLED X24 monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>MSI MAG 272QP QD-OLED X24</strong> is a 27" QD-OLED monitor designed for high-end gaming and creative work. It delivers exceptional color accuracy, ultra-fast response times, and smooth refresh rates.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 27 inches</li>
              <li><b>Panel type:</b> QD-OLED</li>
              <li><b>Resolution:</b> QHD (2560×1440)</li>
              <li><b>Refresh rate:</b> 165 Hz</li>
              <li><b>Response time:</b> 0.03 ms GtG</li>
              <li><b>Color performance:</b> Wide color gamut, HDR support, vivid and deep colors</li>
              <li><b>Connectivity:</b> DisplayPort 1.4, HDMI 2.0, USB hub</li>
              <li><b>Use-case:</b> Gaming, content creation, multimedia</li>
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
            <span>(0 reviews)</span>
          </div>
        </div>
      </div>

      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Hrustinszki Márton </h3>
        </div>

        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="MSI MAG 272QP QD-OLED X24">
            <input type="hidden" name="product_price" value="670">
            <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QP-QD-OLED-X24/mag-272qp-qd-oled-x24-kv.webp">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
</body>
<script src="js.js"></script>
</html>
