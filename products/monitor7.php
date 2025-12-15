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
    <title>MSI MAG 274QPF</title>
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
            <p class="sub">MSI MAG 274QPF</p>
            <p class="price">$329</p>
          </div>

          <div class="image">
            <img src="https://storage-asset.msi.com/global/picture/image/feature/monitor/mag-274qpf-x32/kv-pd.png"
                 alt="MSI MAG 274QPF Monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>MSI MAG 274QPF</strong> is a versatile QHD monitor designed for gamers and power users, delivering a balanced mix of performance, clarity and responsiveness — ideal for gaming, work, and everyday use.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 27 inches</li>
              <li><b>Resolution:</b> QHD (2560×1440) for sharp visuals and detailed images</li>
              <li><b>Panel type:</b> IPS or VA for good color and viewing angles</li>
              <li><b>Refresh rate:</b> 165 Hz for smooth motion in games</li>
              <li><b>Response time:</b> Fast response time for gaming and media</li>
              <li><b>Color & Image Quality:</b> Balanced performance suitable for gaming, design, and general use</li>
            </ul>
          </div>

          <span class="stock"><i class="fa fa-pen"></i> In stock</span>

          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class fa-star"></i></li>
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
            <input type="hidden" name="product_name" value="MSI MAG 274QPF">
            <input type="hidden" name="product_price" value="330">
            <input type="hidden" name="product_image" value="https://storage-asset.msi.com/global/picture/image/feature/monitor/mag-274qpf-x32/kv-pd.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>
