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
    <title>32" Samsung Smart Monitor M7 M70F 4K</title>
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
            <h1>Monitor</h1>
            <p class="sub">"32" Vision AI Smart Monitor M7 M70F 4K</p>
            <p class="price">$399</p>
          </div>

          <div class="image">
            <img src="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fm700uuxdu/gallery/hu-smart-m7-32m70f-black-ls32fm700uuxdu-547197500?$Q90_1920_1280_F_PNG$" 
                 alt="32 Smart Monitor M7 M70F 4K">
          </div>
        </div>

        <div class="half">
          <div class="description">
            <p>The <strong>Samsung "32" Vision AI Smart Monitor M7 M70F 4K</strong> combines sharp 4K resolution with smart features — ideal for work, entertainment and productivity all in one display.</p>
            <br>
            <ul>
              <li><b>Screen size:</b> 32 inches</li>
              <li><b>Resolution:</b> 4K UHD (3840×2160) for crisp, detailed visuals</li>
              <li><b>Smart features:</b> Built-in streaming apps, remote desktop support, and smart functionality without a PC</li>
              <li><b>HDR support:</b> HDR10+ for vibrant colors and contrast</li>
              <li><b>Connectivity:</b> HDMI, USB-C, and wireless options for flexible use</li>
              <li><b>Design:</b> Sleek modern look with minimal bezel — ideal for any workspace or living room</li>
              <li><b>Multitasking:</b> Picture-in-Picture and Picture-by-Picture support for efficient workflows</li>
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
            <span>(0 reviews)</span>
          </div>
        </div>
      </div>

      <div class="card__footer">
        <div class="recommend">
          <p>Recommended by</p>
          <h3>Kapcsándi Tamás</h3>
        </div>

        <div class="action">
          <form action="../add_to_cart.php" method="POST">
            <input type="hidden" name="product_name" value="Vision AI Smart Monitor M7 M70F 4K">
            <input type="hidden" name="product_price" value="400">
            <input type="hidden" name="product_image" value="https://images.samsung.com/is/image/samsung/p6pim/hu/ls32fm700uuxdu/gallery/hu-smart-m7-32m70f-black-ls32fm700uuxdu-547197500?$Q90_1920_1280_F_PNG$">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>

</body>
<script src="js.js"></script>
</html>

