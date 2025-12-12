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
    <title>37" Samsung ViewFinity S8 S80UD UHD Monitor</title>
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
            <h1>Monitor</h1>
            <p class="sub">37" ViewFinity S8 S80UD UHD</p>
            <p class="price">$699</p>
          </div>

          <div class="image">
            <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls37d800uauxen/gallery/hu-viewfinity-s8-37s80ud-ls37d800uauxen-546091975?$Q90_1920_1280_F_PNG$" 
                 alt="37 ViewFinity S8 S80UD UHD monitor">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Samsung "37" ViewFinity S8 S80UD</strong> is a premium UHD monitor combining large-format display, high resolution, and versatile performance — ideal for creative work, productivity, and immersive media consumption.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 37 inches</li>
              <li><b>Resolution:</b> 4K UHD (3840×2160) for razor-sharp detail</li>
              <li><b>Panel type:</b> VA panel — balanced contrast and color depth</li>
              <li><b>Color & HDR:</b> High dynamic range and wide color gamut for accurate colors</li>
              <li><b>Connectivity:</b> HDMI, DisplayPort, USB-C, USB hub — flexible setup options</li>
              <li><b>Multi-tasking:</b> Large screen real estate ideal for multitasking, content creation or office work</li>
              <li><b>Design:</b> Elegant, minimalistic design suited for professional workspaces or modern desks</li>
            </ul>
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
            <span>(5 reviews)</span>
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
            <input type="hidden" name="product_name" value="Samsung 37 ViewFinity S8 S80UD">
            <input type="hidden" name="product_price" value="700">
            <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls37d800uauxen/gallery/hu-viewfinity-s8-37s80ud-ls37d800uauxen-546091975?$Q90_1920_1280_F_PNG$">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>
