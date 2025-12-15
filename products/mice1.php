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
    <title>Razer Viper V3 Pro</title>
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
            <h1>Mouse</h1>
            <p class="sub">Razer Viper V3 Pro</p>
            <p class="price">$159</p>
          </div>
          <div class="image">
            <img src="https://assets3.razerzone.com/7QeO9se0LbDhoHwFI-3juHOVEzA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4e%2Fh5e%2F9765618221086%2Fviper-v3-pro-white-500x500.png" alt="Razer Viper V3 Pro">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Razer Viper V3 Pro</b> is a lightweight, high-performance wireless gaming mouse engineered for serious competitive players. Featuring Razer's next-gen Focus Pro 35K Optical Sensor and HyperPolling Wireless technology, it delivers unmatched accuracy, speed, and responsiveness.</p>
            <br>
            <p><ul>
                <li><b>Sensor:</b> Razer Focus Pro 35K Optical</li>
                <li><b>Switches:</b> Gen-3 Optical Mouse Switches</li>
                <li><b>Connectivity:</b> Razer HyperSpeed Wireless / USB-C</li>
                <li><b>Battery Life:</b> Up to 95 hours</li>
                <li><b>Weight:</b> 54 g</li>
                <li><b>DPI Range:</b> Up to 35,000 DPI</li>
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
            <span>(4 reviews)</span>
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
            <input type="hidden" name="product_name" value="Razer Viper V3 Pro">
            <input type="hidden" name="product_price" value="159">
            <input type="hidden" name="product_image" value="https://assets3.razerzone.com/7QeO9se0LbDhoHwFI-3juHOVEzA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh4e%2Fh5e%2F9765618221086%2Fviper-v3-pro-white-500x500.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
