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
    <title>Razer Naga Left-Handed Edition</title>
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
            <p class="sub">Razer Naga Left-Handed Edition</p>
            <p class="price">$149</p>
          </div>
          <div class="image">
            <img src="https://assets3.razerzone.com/yVd7fP8Z4ibH0AxLPvpk16aelJA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh09%2Fhba%2F9529652346910%2Fnaga-left-handed-2-500x500.png" alt="Razer Naga Left-Handed Edition">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Razer Naga Left-Handed Edition</b> is a specially designed gaming mouse for left-handed users, combining ergonomic comfort and high-performance tracking. With customizable side-buttons and a precision sensor, it offers intuitive control whether you play MMOs, MOBAs or work with productivity apps.</p>
            <br>
            <p><ul>
                <li><b>Sensor:</b> Razer Focus Pro Optical Sensor</li>
                <li><b>Switches:</b> Durable gaming-grade switches</li>
                <li><b>Connectivity:</b> Wired / USB-C</li>
                <li><b>Modularity:</b> Ergonomic design optimized for left-hand use, customizable side-buttons</li>
                <li><b>Compatibility:</b> PC, Mac, Console (where supported)</li>
                <li><b>DPI Range:</b> High precision adjustable DPI</li>
                </ul></p>
          </div>
          <span class="stock"><i class="fa fa-pen"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class are="fa fa-star"></i></li>
              <li><i class are="fa fa-star"></i></li>
              <li><i class are="fa fa-star"></i></li>
              <li><i class are="fa fa-star-o"></i></li>
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
            <input type="hidden" name="product_name" value="Razer Naga Left-Handed Edition">
            <input type="hidden" name="product_price" value="149">
            <input type="hidden" name="product_image" value="https://assets3.razerzone.com/yVd7fP8Z4ibH0AxLPvpk16aelJA=/300x300/https%3A%2F%2Fmedias-p1.phoenix.razer.com%2Fsys-master-phoenix-images-container%2Fh09%2Fhba%2F9529652346910%2Fnaga-left-handed-2-500x500.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>

