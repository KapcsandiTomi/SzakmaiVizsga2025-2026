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
    <title>Logitec G Pro X</title>
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
            <h1>Headphones</h1>
            <p class="sub">ASUS ROG Delta 2</p>
            <p class="price">$70</p>
          </div>
          <div class="image">
            <img src="https://dlcdnwebimgs.asus.com/gain/46BDC9FB-449D-4549-BADE-1E5A4EACA111/w717/h525/fwebp" alt="AUSUS ROG Delta 2">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>ASUS ROG Delta II</b> offers a comprehensive feature‑set for serious gamers, combining high‑end audio, multi‑mode connectivity, and comfort for long sessions.</p>
            <br>
            <p><ul>
                <li><b>Driver size:</b> 50 mm titanium‑plated diaphragm drivers, tuned for rich detail and clear gaming sound.</li>
                <li><b>Microphone type:</b> Detachable boom mic with 10 mm diaphragm, unidirectional pattern, optimized for clear team communication.</li>
                <li><b>Connectivity:</b> Tri‑mode connection — 2.4 GHz low‑latency wireless, Bluetooth, and 3.5 mm wired analog.</li>
                <li><b>Battery life:</b> Up to ~110 hours in 2.4 GHz wireless mode (with RGB lighting off).</li>
                <li><b>Compatibility:</b> PC, Mac, PlayStation 5, Nintendo Switch, Mobile</li>
                <li><b>Weight:</b> Approx. 318 g</li>
                </ul></p>
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
          <h3>Hajas Máté and Kapcsándi Tamás</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="ASUS ROG Delta II">
            <input type="hidden" name="product_price" value="70">
            <input type="hidden" name="product_image" value="https://dlcdnwebimgs.asus.com/gain/46BDC9FB-449D-4549-BADE-1E5A4EACA111/w717/h525/fwebp">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>