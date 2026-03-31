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
    <title>NVIDIA RTX 3050 6GB Low Profile</title>
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
            <p class="sub">NVIDIA RTX 3050 6GB Low Profile</p>
            <p class="price">$249</p>
          </div>

          <div class="image">
            <img src="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%203050%20OC%20Low%20Profile%206G-06-600x600.png"
                 alt="nVIDIA RTX 3050 6GB DDR6">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>
              The <strong>NVIDIA RTX 3050 6GB Low Profile</strong> is designed for gamers and creators looking for 
              an affordable yet powerful graphics card. Equipped with cutting-edge Turing architecture, it offers 
              excellent performance for 1080p gaming and light content creation tasks.
            </p>
            <br>
            <ul>
              <li><b>Memory:</b> 6GB GDDR6</li>
              <li><b>Architecture:</b> NVIDIA Turing – Ray Tracing & DLSS support</li>
              <li><b>Cooling:</b> Efficient low-profile cooling system for small form factor builds</li>
              <li><b>Gaming:</b> Ideal for 1080p high settings and some 1440p gaming</li>
              <li><b>Ports:</b> HDMI & DisplayPort</li>
              <li><b>Use case:</b> Gaming, multimedia, and light content creation</li>
              <li><b>Design:</b> Low profile design for compact PCs or HTPCs</li>
            </ul>
          </div>

          <span class="stock"><i class="fa fa-check"></i> In stock</span>

          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-o"></i></li>
              <li><i class="fa fa-star-o"></i></li>
            </ul>
            <span>(8 reviews)</span>
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
            <input type="hidden" name="product_name" value="NVIDIA RTX 3050 6GB Low Profile">
            <input type="hidden" name="product_price" value="249">
            <input type="hidden" name="product_image" value="https://foramax.hu/image/cache/catalog/product/GeForce%20RTX%E2%84%A2%203050%20OC%20Low%20Profile%206G-06-600x600.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
</main>

<script src="js.js"></script>
</body>
</html>

