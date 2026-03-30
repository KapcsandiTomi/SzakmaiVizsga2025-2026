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
    <title>Razer Pro Click V2 Vertical</title>
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
            <h1>Mouse</h1>
            <p class="sub">Razer Pro Click V2 Vertical</p>
            <p class="price">$79</p>
          </div>
          <div class="image">
            <img src="https://assets3.razerzone.com/QuWXycAZ9HfgKP_6waksItWG5Vc=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4a%2Fh79%2F9899953684510%2Fpro-click-v2-vertical-black-500x500.png" alt="Razer Pro Click V2 Vertical">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Razer Pro Click V2 Vertical</b> is an ergonomic vertical mouse designed to reduce wrist strain and improve comfort during long work or creative sessions. Its vertical orientation encourages a natural hand posture while still offering precise control and reliable tracking.</p>
            <br>
            <p><ul>
                <li><b>Sensor:</b> High-precision optical sensor (configurable DPI)</li>
                <li><b>Switches:</b> Durable switches for long-term use</li>
                <li><b>Connectivity:</b> Wired / USB-C or Wireless (as per configuration)</li>
                <li><b>Design:</b> Ergonomic vertical housing for reduced wrist strain</li>
                <li><b>Compatibility:</b> PC, Mac, general office and productivity use</li>
                </ul></p>
          </div>
          <span class="stock"><i class="fa fa-pen"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-o"></i></li>
              <li><i class="fa fa-star-o"></i></li>
            </ul>
            <span>(0 reviews)</span>
          </div>
        </div>
      </div>
      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Tamás Kapcsándi</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="Razer Pro Click V2 Vertical">
            <input type="hidden" name="product_price" value="79">
            <input type="hidden" name="product_image" value="https://assets3.razerzone.com/QuWXycAZ9HfgKP_6waksItWG5Vc=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4a%2Fh79%2F9899953684510%2Fpro-click-v2-vertical-black-500x500.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>

