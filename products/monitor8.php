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
    <title>MSI MAG 275QF E20</title>
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
            <p class="sub">MSI MAG 275QF E20 27" QHD 200Hz</p>
            <p class="price">$329</p>
          </div>

          <div class="image">
            <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-275QF-E20/msi-mag-275qf-e20-kv.png"
                 alt="MSI MAG 275QF E20 monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>MSI MAG 275QF E20</strong> is a fast, responsive 27‑inch gaming monitor offering smooth performance and sharp QHD resolution — ideal for competitive gaming, general use and immersive multimedia.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 27 inches</li>
              <li><b>Panel type:</b> Rapid IPS — flat panel with good color and viewing angles</li>
              <li><b>Resolution:</b> 2560 × 1440 (WQHD)</li>
              <li><b>Refresh rate:</b> up to 200 Hz via DisplayPort (144 Hz via HDMI)</li>
              <li><b>Response time:</b> 0.5 ms (GtG) for minimal motion blur</li>
              <li><b>Adaptive sync / FreeSync:</b> Adaptive‑Sync supported — tear‑free gameplay </li>
              <li><b>Color & Brightness:</b> 300 nits typical brightness, ~101% sRGB gamut, 1.07 B colors (8‑bit + FRC) </li>
              <li><b>Connectivity:</b> 2× HDMI 2.0b, 1× DisplayPort 1.4, 1× 3.5 mm audio out</li>
              <li><b>Ergonomics & Design:</b> Tilt adjustable (-5° … +20°), anti‑glare coating, 178° viewing angles, VESA 100×100‑mm mountable</li>
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
          <h3>Kapcsándi Tamás</h3>
        </div>

        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="MSI MAG 275QF E20">
            <input type="hidden" name="product_price" value="330">
            <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-275QF-E20/msi-mag-275qf-e20-kv.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>
