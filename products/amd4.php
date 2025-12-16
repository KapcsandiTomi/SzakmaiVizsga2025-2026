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
    <title>AMD Radeon RX 9070 XT 16GB</title>
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
            <h1>Graphics Card</h1>
            <p class="sub">AMD Radeon RX 9070 XT 16GB GDDR6</p>
            <p class="price">$1,199</p>
          </div>

          <div class="image">
            <img src="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9070_XT_16GB-i1534401.png"
                 alt="AMD RX 9070 XT 16GB">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>
              The <strong>AMD Radeon RX 9070 XT 16GB</strong> is designed for extreme performance and next-gen gaming.
              Built on the latest AMD architecture, it delivers outstanding 4K gaming, high refresh rates,
              and exceptional power efficiency for demanding workloads.
            </p>
            <br>
            <ul>
              <li><b>Memory:</b> 16GB GDDR6</li>
              <li><b>Architecture:</b> AMD RDNA™ next-generation</li>
              <li><b>Cooling:</b> Advanced multi-fan cooling system</li>
              <li><b>Gaming:</b> Optimized for 4K Ultra & high-refresh 1440p</li>
              <li><b>Ports:</b> HDMI 2.1, DisplayPort 2.1</li>
              <li><b>Use case:</b> Enthusiast gaming, streaming, professional content creation</li>
              <li><b>Design:</b> Premium GIGABYTE performance design</li>
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
            <span>(10 reviews)</span>
          </div>
        </div>
      </div>

      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Kapcsándi tamás</h3>
        </div>

        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="AMD Radeon RX 9070 XT 16GB">
            <input type="hidden" name="product_price" value="1199">
            <input type="hidden" name="product_image" value="https://www.chs.hu/GIGABYTE_Videokartya_PCI-Ex16x_AMD_RX_9070_XT_16GB-i1534401.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
</main>

<script src="js.js"></script>
</body>
</html>
