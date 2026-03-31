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
    <title>Logitec G Pro X 2</title>
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
            <h1>Headphones</h1>
            <p class="sub">Logitech G PRO X 2</p>
            <p class="price">$150</p>
          </div>
          <div class="image">
            <img src="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-2-lightspeed/gallery/gallery-1-pro-x-2-lightspeed-gaming-headset-black.png" alt="Logitech Pro X 2">
          </div>
        </div>
        <div class="half">
          <div class="description">
            <p>The <b>Logitech G PRO X 2 LIGHTSPEED</b> is a premium wireless gaming headset built for competitive gamers and content creators seeking top‑tier audio performance and comfort.</p>
            <br>
            <p><ul>
              <li><b>Driver size:</b> 50 mm graphene‑coated drivers for enhanced clarity and detail across the full audible range.</li>
              <li><b>Microphone type:</b> Detachable cardioid boom mic with a frequency response of ~100 Hz–10 kHz, optimized for team communication and streaming.</li>
              <li><b>Connectivity:</b> Supports LIGHTSPEED 2.4 GHz wireless, Bluetooth, and wired 3.5 mm — enabling flexible use across PC, console, and mobile.</li>
              <li><b>Weight:</b> Approx. 320 g (without cable)</li>
              <li><b>Frequency response:</b> 20 Hz–20 kHz, offering a broad and accurate sound footprint.</li>
              <li><b>Impedance:</b> 38 Ω, tuned for efficient power handling and compatibility in wired and wireless modes. </li>
              <li><b>Battery life:</b> Up to 50 hours of wireless use on a single charge, supporting long gaming sessions without interruption.</li>
              <li><b>Build & comfort:</b> Lightweight (~345 g) with high‑quality materials (aluminum fork, steel headband, memory‑foam ear pads) designed for extended wear.</li>
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
            <span>(10 reviews)</span>
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
            <input type="hidden" name="product_name" value="Logitech G PRO X 2 LIGHTSPEED">
            <input type="hidden" name="product_price" value="150">
            <input type="hidden" name="product_image" value="https://resource.logitechg.com/w_692,c_lpad,ar_4:3,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-2-lightspeed/gallery/gallery-1-pro-x-2-lightspeed-gaming-headset-black.png">
            <button type="submit">Order now</button>
          </form>
        </div>
      </div>
    </div>
  </main>
    
</body>
<script src="js.js"></script>
</html>
