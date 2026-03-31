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
    <title>White Shark NAGAMAKI WHITE US</title>
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
            <h1>Keyboard</h1>
            <p class="sub">White Shark NAGAMAKI WHITE US</p>
            <p class="price">$34</p>
          </div>
          <div class="image">
            <img src="https://smit-electronic.s3.eu-central-1.amazonaws.com/9ddf293b-9d73-40db-8f04-dc867477c675/conversions/NAGAMAKI-W-US-RED.SW-%281%29-thumb.png" alt="NAGAMAKI WHITE US">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <strong>White Shark NAGAMAKI WHITE US</strong> is a stylish gaming keyboard designed for smooth, reliable typing and vibrant RGB lighting. 
            Its spill-resistant construction, responsive membrane keys, and durable build make it ideal for gaming, office use, or everyday work.</p>
            <br>
            <ul>
              <li><b>Key type:</b> Membrane keys</li>
              <li><b>Layout:</b> US layout</li>
              <li><b>Backlight:</b> RGB with 3 lighting modes</li>
              <li><b>Connection:</b> USB 2.0 wired</li>
              <li><b>Anti-ghosting:</b> 19-key anti-ghosting</li>
              <li><b>Features:</b> Spill-resistant design</li>
              <li><b>Cable length:</b> 1.5 m durable cable</li>
            </ul>
          </div>
          <span class="stock"><i class="fa fa-pen"></i> In stock</span>
          <div class="reviews">
            <ul class="stars">
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star"></i></li>
              <li><i class="fa fa-star-half-o"></i></li>
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
            <input type="hidden" name="product_name" value="White Shark NAGAMAKI WHITE US">
            <input type="hidden" name="product_price" value="34">
            <input type="hidden" name="product_image" value="https://smit-electronic.s3.eu-central-1.amazonaws.com/9ddf293b-9d73-40db-8f04-dc867477c675/conversions/NAGAMAKI-W-US-RED.SW-%281%29-thumb.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>

