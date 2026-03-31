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
    <title>MSI MAG 272QPF E20</title>
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
            <p class="sub">MSI 27&quot; MAG 272QPF E20 QHD 200 Hz</p>
            <p class="price">$349</p>
          </div>

          <div class="image">
            <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QPF-E20/kv/msi-mag-272qpf-e20-kv.png"
                 alt="MSI MAG 272QPF E20 monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>MSI 27 MAG 272QPF E20</strong> is a fast and responsive WQHD monitor with a Rapid IPS panel, built for gamers and enthusiasts needing smooth performance and sharp imagery.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 27 inches</li>
              <li><b>Panel type:</b> Rapid IPS</li>
              <li><b>Resolution:</b> 2560 × 1440 (WQHD) </li>
              <li><b>Refresh rate:</b> up to 200 Hz </li>
              <li><b>Response time:</b> 0.5 ms (GtG, min) </li>
              <li><b>Color / Gamut:</b> Wide‑gamut coverage (sRGB / DCI‑P3 / etc.) </li>
              <li><b>Brightness:</b> 300 nits (typical) </li>
              <li><b>Panel surface:</b> Anti‑glare / matte</li>
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
          <h3>Hajas Máté</h3>
        </div>

        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="MSI 27 MAG 272QPF E20">
            <input type="hidden" name="product_price" value="350">
            <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/MAG/MAG-272QPF-E20/kv/msi-mag-272qpf-e20-kv.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
</body>
<script src="js.js"></script>
</html>

