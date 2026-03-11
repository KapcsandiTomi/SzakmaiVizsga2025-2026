<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// Adatbázis kapcsolat
include 'config.php';
require_once 'handler/ratedata.php';

// RateData modell létrehozása
$rateModel = new RateData($conn);

// Vélemények lekérése
$reviews = $rateModel->getReviewsForHomepage(3);
$stats = $rateModel->getReviewStats();
$total_reviews = $stats['total_reviews'];
$avg_rating = $stats['avg_rating'];
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Main</title>
    <link rel="stylesheet" href="fooldal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="letoles.jpg" type="image/png">
    <style>
        .testimonials-container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }
        
        .testimonials-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .testimonials-header h2 {
            font-size: 2.8rem;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
            background: linear-gradient(135deg, #51dbf3ff 0%, #4b95a2ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .testimonials-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .testimonials-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            min-width: 180px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 8rem;
            color: rgba(102, 126, 234, 0.1);
            font-family: Georgia, serif;
            line-height: 1;
        }
        
        .quote-icon {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
            margin-bottom: 25px;
            font-style: italic;
            position: relative;
            z-index: 1;
        }
        
        .testimonial-rating {
            color: #ffc107;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #07c6edff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .author-info h4 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .author-info p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .testimonial-date {
            font-size: 0.85rem;
            color: #888;
            margin-top: 5px;
        }
        
        .view-all-reviews {
            text-align: center;
            margin-top: 50px;
        }
        
        .btn-view-all {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #667eea 0%, #0fd3ffff 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .btn-view-all:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .no-testimonials {
            text-align: center;
            padding: 60px 40px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f7ff 100%);
            border-radius: 20px;
            border: 2px dashed #667eea;
        }
        
        .no-testimonials i {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-testimonials h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        
        .no-testimonials p {
            color: #666;
            margin-bottom: 25px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        @media (max-width: 768px) {
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            
            .testimonials-stats {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
            
            .stat-item {
                width: 100%;
                max-width: 250px;
            }
            
            .testimonial-card {
                padding: 25px;
            }
        }
        
        @media (max-width: 480px) {
            .testimonials-header h2 {
                font-size: 2.2rem;
            }
            
            .testimonial-card {
                padding: 20px;
            }
            
            .author-avatar {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid bg-light p-0">
    <div class="row gx-0 d-none d-lg-flex">
      <div class="col-lg-7 px-5 text-start">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-map-marker-alt text-primary me-2"></small>
          <small>Gardonyi Road, Isaszeg, Hungary</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center py-3">
          <small class="far fa-clock text-primary me-2"></small>
          <small>Mon - Sat: 09:00 AM - 07:00 PM</small>
        </div>
      </div>
      <div class="col-lg-5 px-5 text-end">
        <div class="h-100 d-inline-flex align-items-center py-3 me-4">
          <small class="fa fa-phone-alt text-primary me-2"></small>
          <small>+36 70 123 4567</small>
        </div>
        <div class="h-100 d-inline-flex align-items-center">
          <a class="btn btn-sm-square bg-white text-primary me-1" href="https://www.facebook.com/groups/780808788334912"><i class="fab fa-facebook-f"></i></a>
          <a class="btn btn-sm-square bg-white text-primary me-1" href="https://x.com/tamas_kapc343"><i class="fab fa-twitter"></i></a>
          <a class="btn btn-sm-square bg-white text-primary me-0" href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>

  <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
    <a href="fooldal.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <div class="logo-4">
            <span class="aqua">AQUA</span>
            <span class="mini-shop">MINI SHOP</span>
        </div>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="fooldal.php" class="nav-item nav-link active">Home</a>
        <a href="about.php" class="nav-item nav-link">About US</a>
        <a href="products.php" class="nav-item nav-link">Products</a>
        <a href="rateus.php" class="nav-item nav-link">RATE US</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" id="contactDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            CONTACT
          </a>
          <ul class="dropdown-menu fade-up m-0" aria-labelledby="contactDropdown">
            <li><a href="writeUs.php" class="dropdown-item">WRITE US</a></li>
            <li><a href="faq.php" class="dropdown-item">FAQ</a></li>
          </ul>
        </div>

        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            MY ACCOUNT
          </a>
          <ul class="dropdown-menu fade-up m-0" aria-labelledby="accountDropdown">
            <li><a href="profile.php" class="dropdown-item">MY PROFILE</a></li>
            <li><a href="logout.php" class="dropdown-item">LOGOUT</a></li>
            <li><a href="myorder.php" class="dropdown-item">MY ORDERS</a></li>
          </ul>
        </div>
      </div>
    </div>
</nav>

<?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
    <div class = "admin-page">
      <b><a href="admin/index.php">⚙️ Admin Panel</a></b>
    </div>
<?php endif; ?>

<main>
  <div class="hero">
    <div class="hero-text">
      <h1>Give Your Gaming<br>A New Style!</h1>
      <p>Machines are built to extend our hands, but innovations make them extend our possibilities.</p>
      <a href="products.php" class="btn">Explore Now →</a>
    </div>
    <img src="img/aqau.jpg" alt="PC" style="border-radius: 100px;">
  </div>

  <div class="categories">
    <div class="small-container">
      <div class="row">
        <div class="col-3"><img src="img/1.jpg" alt="PC"></div>
        <div class="col-3"><img src="img/2.jpg" alt="PC"></div>
        <div class="col-3"><img src="img/3.avif" alt="PC"></div>
      </div>
    </div>

    <div class="small-container">
      <h2 class="title">Featured Products</h2>
      <div class="row">
        <div class="col-4">
          <img src="img/bil.avif" alt="Billentyűzet">
          <h4>Spirit of Gamer ELITE K70 RGB</h4>
          <div class="rating">
            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>
          </div>
          <p>50.00$</p>
        </div>
        <div class="col-4">
          <img src="img/videokartya.png" alt="Videókártya">
          <h4>RTX 5070 TI BY NVIDIA</h4>
          <div class="rating">
            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
          </div>
          <p>250.00$</p>
        </div>
        <div class="col-4">
          <img src="img/monitor.avif" alt="Monitor">
          <h4>Oddesey 244 HZ MONITOR</h4>
          <div class="rating">
            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
          </div>
          <p>60.00$</p>
        </div>
      </div>
    </div>
  </div>

  <div class="offer">
    <div class="small-container">
      <div class="row">
        <div class="col-2"><img src="img/Png.png" alt="Különleges" class="offer-img"></div>
        <div class="col-2">
          <p>Exclusively Available on MINISTORE!</p>
          <h1>GeForce RTX™ 5090 GAMING OC 32G</h1>  
          <small>Powered by the NVIDIA Blackwell architecture and DLSS 4</small><br>
          <small>Powered by GeForce RTX™ 5090</small><br>
          <small>Integrated with 32GB GDDR7 512bit memory interface</small><br>
          <small>WINDFORCE cooling system</small><br>
          <small>RGB Halo</small><br>
          <small>Dual BIOS (Performance / Silent)</small><br>
          <small>Reinforced structure</small><br>
          <a href="products.php" class="btn">Buy now!</a>
        </div>
      </div>
    </div>
  </div>

  <!-- RATEUS vélemények megjelenítése -->
  <div class="testimonials-container">
    <div class="testimonials-header">
      <h2>What Our Customers Say</h2>
      <p>Real feedback from our valued customers about their shopping experience</p>
    </div>
    
    <!-- Statisztikák -->
    <?php if($total_reviews > 0): ?>
    <div class="testimonials-stats">
      <div class="stat-item">
        <div class="stat-number"><?php echo $total_reviews; ?></div>
        <div class="stat-label">Total Reviews</div>
      </div>
      <div class="stat-item">
        <div class="stat-number"><?php echo $avg_rating; ?>/5</div>
        <div class="stat-label">Average Rating</div>
      </div>
    </div>
    <?php endif; ?>
    
    <!-- Vélemények -->
    <?php if(count($reviews) > 0): ?>
    <div class="testimonials-grid">
      <?php foreach($reviews as $review): 
          $initial = strtoupper(substr($review['user_name'], 0, 1));
          $rating_stars = str_repeat('<i class="fas fa-star"></i>', $review['rating']) . 
                         str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']);
          $formatted_date = date('F j, Y', strtotime($review['created_at']));
      ?>
      <div class="testimonial-card">
        <div class="quote-icon">
          <i class="fas fa-quote-left"></i>
        </div>
        
        <p class="testimonial-text">
          <?php echo htmlspecialchars($review['comment']); ?>
        </p>
        
        <div class="testimonial-rating">
          <?php echo $rating_stars; ?>
        </div>
        
        <div class="testimonial-author">
          <div class="author-avatar">
            <?php echo $initial; ?>
          </div>
          <div class="author-info">
            <h4><?php echo htmlspecialchars($review['user_name']); ?></h4>
            <p class="testimonial-date"><?php echo $formatted_date; ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <div class="view-all-reviews">
      <a href="rateus.php" class="btn-view-all">
        <i class="fas fa-comments"></i> View All Reviews
      </a>
    </div>
    <?php else: ?>
    <div class="no-testimonials">
      <i class="fas fa-comments"></i>
      <h3>No Reviews Yet</h3>
      <p>Be the first to share your shopping experience! Your feedback helps us improve.</p>
      <a href="rateus.php" class="btn-view-all">
        <i class="fas fa-pen-alt"></i> Write First Review
      </a>
    </div>
    <?php endif; ?>
  </div>
</main>

<footer>
  <div class="container">
    <div class="footer-col">
      <h2>MOTO<div class="underline"><span></span></div></h2>
      <p class="footer-para">Innovation starts here – with machines designed for creators and professionals who demand more. Whether you're editing, rendering, or building the future, our systems are ready to keep up with your vision.</p>
    </div>
    <div class="footer-col">
      <h3 class="text-office">
        Office<div class="underline"><span></span></div>
      </h3>
      <p>Street No 8</p><p>Gárdonyi Géza</p><p>Isaszeg, 2117, Hungary</p>
      <p class="email">aquaminishop@gmail.com</p>
      <p class="phone">+36 70 123 4567</p>
    </div>
    <div class="footer-col">
      <h3>Menu<div class="underline"><span></span></div></h3>
      <ul>
        <li><a href="fooldal.php">Home</a></li><li><a href="products.php">Products</a></li><li><a href="about.php">About us</a></li><li><a href="faq.php">FAQ</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h2>FOLLOW US<div class="underline"><span></span></div></h2>
      <div class="social-icons">
        <a href="https://www.facebook.com/groups/780808788334912"><i class="fab fa-facebook-f"></i></a>
        <a href="https://x.com/tamas_kapc343"><i class="fab fa-twitter"></i></a>
        <a href="https://www.instagram.com/aqua.mini.shop/"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    if(testimonialCards.length > 1) {
        let currentIndex = 0;
        
        setInterval(() => {
            testimonialCards.forEach(card => card.style.display = 'none');
            
            currentIndex = (currentIndex + 1) % testimonialCards.length;
            testimonialCards[currentIndex].style.display = 'block';
            
        }, 80000);
    }
});
</script>

<script src="javas.js"></script>
<script src="chat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
</body>
</html>
