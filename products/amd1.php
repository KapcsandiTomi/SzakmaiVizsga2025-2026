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
    <title>AMD Radeon RX 7900 XT Phantom Gaming</title>
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
            <p class="sub">AMD Radeon RX 7900 XT Phantom Gaming 20GB</p>
            <p class="price">$949</p>
          </div>

          <div class="image">
            <img src="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_20GB_DDR6_OC-i1445676.png"
                 alt="AMD RX 7900 XT Phantom Gaming 20GB">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>
              The <strong>AMD Radeon RX 7900 XT Phantom Gaming</strong> delivers extreme performance powered by 
              AMD RDNA™ 3 architecture. With massive 20GB GDDR6 memory, it is built for 4K gaming, high refresh 
              rates, and demanding creative workloads.
            </p>
            <br>
            <ul>
              <li><b>Memory:</b> 20GB GDDR6</li>
              <li><b>Architecture:</b> AMD RDNA™ 3</li>
              <li><b>Cooling:</b> Phantom Gaming triple-fan cooling system</li>
              <li><b>Gaming:</b> Excellent for 1440p Ultra & 4K High/Ultra</li>
              <li><b>Ports:</b> HDMI 2.1, DisplayPort 2.1</li>
              <li><b>Use case:</b> High-end gaming, streaming, content creation</li>
              <li><b>Design:</b> Aggressive Phantom Gaming black design</li>
            </ul>
          </div>

          <span class="stock"><i class="fa fa-check"></i> In stock</span>

          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-o"></i></li>
            </ul>
            <span>(18 reviews)</span>
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
            <input type="hidden" name="product_name" value="AMD Radeon RX 7900 XT Phantom Gaming 20GB">
            <input type="hidden" name="product_price" value="949">
            <input type="hidden" name="product_image" value="https://www.chs.hu/ASROCK_Videokartya_PCI-Ex16x_AMD_RX_7900XT_PHANTOM_GAMING_20GB_DDR6_OC-i1445676.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
</main>

<script src="js.js"></script>
</body>
</html>

