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
    <title>Intel Core i5-14400F</title>
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
            <h1>Processor</h1>
            <p class="sub">Intel Core i5-14400F</p>
            <p class="price">$260</p>
          </div>
          <div class="image">
            <img src="https://pngimg.com/uploads/cpu/cpu_PNG46.png" alt="Intel Core i5-14400F">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Intel Core i5-14400F</b> delivers strong performance for gaming and general computing without integrated graphics. Ideal for systems using dedicated GPUs, it offers excellent multitasking capabilities and efficient power consumption.</p>
            <br>
            <p><ul>
                <li><b>Cores / Threads:</b> 10 / 16</li>
                <li><b>Base / Boost Clock:</b> 3.0 GHz / 4.5 GHz</li>
                <li><b>Cache:</b> 18 MB Intel Smart Cache</li>
                <li><b>Socket:</b> LGA 1700</li>
                <li><b>Integrated Graphics:</b> None</li>
                <li><b>TDP:</b> 65 W</li>
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
            <input type="hidden" name="product_name" value="Intel Core i5-14400F">
            <input type="hidden" name="product_price" value="260">
            <input type="hidden" name="product_image" value="https://pngimg.com/uploads/cpu/cpu_PNG46.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
