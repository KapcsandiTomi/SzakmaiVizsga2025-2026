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
    <title>AMD Ryzen™ 7 9800X3D</title>
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
            <h1>Processor</h1>
            <p class="sub">AMD Ryzen™ 7 9800X3D</p>
            <p class="price">$599</p>
          </div>
          <div class="image">
            <img src="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/163/465/Ryzen_7_9800X3D__03299.1730824088.png?c=1" alt="AMD Ryzen™ 7 9800X3D">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>AMD Ryzen™ 7 9800X3D</b> delivers high-performance computing for gaming and creative workloads. With 3D V-Cache technology, it maximizes cache for faster processing and smoother multitasking across demanding applications.</p>
            <br>
            <p><ul>
                <li><b>Cores / Threads:</b> 8 / 16</li>
                <li><b>Base / Boost Clock:</b> 3.3 GHz / 4.7 GHz</li>
                <li><b>Cache:</b> 96 MB (L2 + L3 with 3D V-Cache)</li>
                <li><b>Socket:</b> AM4</li>
                <li><b>Integrated Graphics:</b> None</li>
                <li><b>TDP:</b> 105 W</li>
                </ul></p>
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
            <span>(21 reviews)</span>
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
            <input type="hidden" name="product_name" value="AMD Ryzen™ 7 9800X3D">
            <input type="hidden" name="product_price" value="599">
            <input type="hidden" name="product_image" value="https://cdn11.bigcommerce.com/s-3wzspkde5p/images/stencil/500x659/products/163/465/Ryzen_7_9800X3D__03299.1730824088.png?c=1">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>

