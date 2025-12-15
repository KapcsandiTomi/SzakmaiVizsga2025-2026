<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - Meet Our Team</title>
    <link rel="stylesheet" href="about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="letoles.jpg" type="image/png">
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
          <small>+36 70 645 1793</small>
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
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" id="contactDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            CONTACT <i class="fa-solid fa-caret-down"></i>
          </a>
          <ul class="dropdown-menu fade-up m-0" aria-labelledby="pagesDropdown">
            <li><a href="writeUs.php" class="dropdown-item">WRITE US</a></li>
            <li><a href="faq.php" class="dropdown-item">FAQ</a></li>
          </ul>
        </div>


        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            MY ACCOUNT <i class="fa-solid fa-caret-down"></i>
          </a>
          <ul class="dropdown-menu fade-up m-0" aria-labelledby="pagesDropdown">
            <li><a href="profile.php" class="dropdown-item">MY PROFILE</a></li>
            <li><a href="logout.php" class="dropdown-item">LOGOUT</a></li>
            <li><a href="myorder.php" class="dropdown-item">MY ORDERS</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>


<section class="team-section">

    <h1 class="section-title">Meet Our Team</h1>
    <p class="section-subtitle">Creative Minds – Modern Developers – Future Builders</p>

    <div class="team-container">
        <div class="team-card">
            <div class="team-image">
                <img src="KapcsiTomi.JPEG" alt="Kapcsándi Tamás">
            </div>
            <div class="team-content">
                <h3>Kapcsándi Tamás</h3>
                <p class="role">Full-Stack Developer • Designer • Backend Developer</p>

                <p class="short-text">
                    <b><span style="font-size: 1.6rem;">Tomi</span></b>, is a <span id="age"></span>-year-old developer, is passionate about creating elegant and user-friendly web experiences.
                    He focuses mainly on <b>front-end development</b>, crafting responsive and visually appealing interfaces using modern web technologies.
                    Beyond design, he also explores <b>back-end development</b>, giving him a broader understanding of how <b>front-end and server-side logic work together.</b>
                </p>

                <div class="hidden-text">
                    <p>
                        <h3><span>My Story</span></h3>
                        <p>Tomi was born in <b>Isaszeg</b>, a small town, and ever since I was little, I have been fascinated by computers and programming. I have always been curious about <b>how games are made and how software works</b>. One of my favorite games as a child was <b>Minecraft</b>, because it allowed me to be <b>creative and experiment with building and exploring.</b></p>
                    </p>
                </div>

                <button class="read-more-btn">Read More</button>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <span class="contact-text">+36 70 645 1793</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <span class="contact-text">kapcsandi.tomi@gmail.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fab fa-instagram contact-icon"></i>
                        <a href="https://www.instagram.com/kapcsandi.tomi/" class="contact-link" target="_blank">@kapcsandi.tomi</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="team-card">
            <div class="team-image">
                <img src="HajasMate.jpg" alt="Hajas Máté">
            </div>
            <div class="team-content">
                <h3>Hajas Máté</h3>
                <p class="role">Frontend Developer • UI/UX Designer</p>

                <p class="short-text">
                    <b><span style="font-size: 1.6rem;">Máté</span></b> is a <b><span id="age2"></span>-year-old frontend developer and designer</b> with a strong passion for creating modern, user-friendly digital experiences. He combines <b>technical skill with a keen eye for design,</b> focusing on clean interfaces and smooth interactions.
                </p>

                <div class="hidden-text">
                    <p>
                        <h3><span>My Story</span></h3>
                        <p>From a young age, Máté knew he wanted to work in technology and design. His curiosity about how websites and digital products are made inspired him to start learning early, shaping his path toward becoming a frontend developer and designer.</p>
                    </p>
                </div>

                <button class="read-more-btn">Read More</button>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <span class="contact-text">+36 30 123 4567</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <span class="contact-text">hajasm778@gmail.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fab fa-instagram contact-icon"></i>
                        <a href="https://www.instagram.com/hajasm778/" class="contact-link" target="_blank">@hajasm778</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="team-card">
            <div class="team-image">
                <img src="HrustinszkiMarci.jpg" alt="Hrusztinszki Márton">
            </div>
            <div class="team-content">
                <h3>Hrusztinszki Márton</h3>
                <p class="role">Searching Manager • Frontend Developer</p>

                <p class="short-text">
                    <b><span style="font-size: 1.6rem;">Márton</span></b> is a <span id="age3"></span> year old dedicated Searching Manager and Front-end Developer who combines analytical thinking with creative problem-solving. He focuses on <b>building efficient, visually</b> appealing, and <b>user-centered digital solutions</b> that deliver real results.
                </p>

                <div class="hidden-text">
                    <p>
                        <h3><span>My Story</span></h3>
                        <p>From a young age, Márton showed a strong interest in technology and digital innovation. His passion for exploring how things work online led him to pursue a career in web development and management, where he continues to grow his skills and creativity.<p>
                    </p>
                </div>

                <button class="read-more-btn">Read More</button>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone-alt contact-icon"></i>
                        <span class="contact-text">+36 30 987 6543</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <span class="contact-text">hrustinszkimarci@gmail.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fab fa-instagram contact-icon"></i>
                        <a href="https://www.instagram.com/hrmarcyy/" class="contact-link" target="_blank">@hrmarcyy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
      <p class="email">kapcsandi.tomi@gmail.com</p>
      <p class="phone">+36 70 645 1793</p>
    </div>
    <div class="footer-col">
      <h3>Menu<div class="underline"><span></span></div></h3>
      <ul>
        <li><a href="fooldal.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="about.php">About us</a></li>
        <li><a href="faq.php">FAQ</a></li>
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

<script src="javas.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="chat.js"></script>
<script src="about.js"></script>
</body>
</html>