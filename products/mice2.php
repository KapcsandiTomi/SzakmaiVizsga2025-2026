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
    <title>Razer DeathAdder V4 Pro</title>
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
            <h1>Mouse</h1>
            <p class="sub">Razer DeathAdder V4 Pro</p>
            <p class="price">$129</p>
          </div>
          <div class="image">
            <img src="https://assets3.razerzone.com/9FvPlvujlrxafg7AbznLiEuAncE=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh01%2Fhf3%2F9926511951902%2Fdeathadder-v4-pro-black-500x500.png" alt="Razer DeathAdder V4 Pro">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Razer DeathAdder V4 Pro</b> is a high-performance gaming mouse built for precision, comfort, and endurance. Featuring a Razer Focus Pro optical sensor and durable switches, it delivers accurate tracking and responsive clicks — ideal for competitive gaming and everyday use alike.</p>
            <br>
            <p><ul>
                <li><b>Sensor:</b> Razer Focus Pro Optical Sensor</li>
                <li><b>Switches:</b> Durable gaming-grade switches</li>
                <li><b>Connectivity:</b> Wired (USB-C)</li>
                <li><b>Compatibility:</b> PC, Mac, Console (where supported)</li>
                <li><b>Weight:</b> ~80–90 g (depending on configuration)</li>
                <li><b>DPI Range:</b> High-precision sensor (configurable in software)</li>
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
          <h3>Tamás Kapcsándi</h3>
        </div>
        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="Razer DeathAdder V4 Pro">
            <input type="hidden" name="product_price" value="129">
            <input type="hidden" name="product_image" value="https://assets3.razerzone.com/9FvPlvujlrxafg7AbznLiEuAncE=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh01%2Fhf3%2F9926511951902%2Fdeathadder-v4-pro-black-500x500.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
