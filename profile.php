<?php
// ============================================
// MUNKAMENET KEZDÉS ÉS KONFIGURÁCIÓ
// ============================================
session_start(); 
require_once 'config.php'; 

// ============================================
// BEJELENTKEZÉSI ÁLLAPOT ELLENŐRZÉSE
// ============================================
// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve (email alapján)
if (!isset($_SESSION['email'])) {
    header('Location: index.php'); 
    exit(); 
}

// ============================================
// FELHASZNÁLÓI ADATOK BETÖLTÉSE
// ============================================
// A munkamenetből kinyerjük a felhasználó email címét
$email = $_SESSION['email'];

// Felhasználó adatainak lekérdezése az adatbázisból
$stmt = $conn->prepare("SELECT name, email, profile_pic FROM `4` WHERE email = ?");
$stmt->bind_param("s", $email); // "s" = string 
$stmt->execute(); // SQL lekérdezés végrehajtása
$result = $stmt->get_result(); // Eredmény halmaz lekérése
$user = $result->fetch_assoc(); // Eredmény sor asszociatív tömbbé --> indexelt tömbb

// ============================================
// VÁLTOZÓK INICIALIZÁLÁSA
// ============================================
// Üzenetek tárolására az űrlap műveletek eredményéhez

$update_success = ''; // Sikeres művelet üzenet
$update_error = '';   // Sikertelen művelet hibaüzenet

// ============================================
// JELSZÓ MEGVÁLTÓ MŰVELET FELDOLGOZÁSA
// ============================================
if (isset($_POST['change_password'])) {
    // Űrlap mezők értékeinek kinyerése
    $currentPassword = $_POST['current_password'] ?? '';  // Jelenlegi jelszó
    $newPassword = $_POST['new_password'] ?? '';          // Új jelszó
    $confirmPassword = $_POST['confirm_password'] ?? '';  // Új jelszó megerősítése

    // ============================================
    // JELENLEGI JELSZÓ ELLENŐRZÉSE
    // ============================================
    // Aktuális felhasználó titkosított jelszavának lekérdezése
    $stmt = $conn->prepare("SELECT password FROM `4` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashedPassword); // Eredmény tárolása változóba
    $stmt->fetch(); 
    $stmt->close(); 
    
    if (!password_verify($currentPassword, $hashedPassword)) {
        $update_error = "The actual password is wrong"; // Hibás jelenlegi jelszó
    } 
    // ============================================
    // ÚJ JELSZAVAK EGYEZÉSÉNEK ELLENŐRZÉSE
    // ============================================
    elseif ($newPassword !== $confirmPassword) {
        $update_error = "The new passwords are not match"; 
    } 
    // ============================================
    // JELSZÓ MEGVÁLTÓ MŰVELET
    // ============================================
    else {
        // Új jelszó titkosítása
        $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Titkosított jelszó frissítése az adatbázisban
        $stmt = $conn->prepare("UPDATE `4` SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $newHashed, $email);
        $stmt->execute();
        $stmt->close();
        
        $update_success = "Password successfully have been changed"; // Sikeres üzenet
    }
}

// ============================================
// PROFILKÉP MÓDOSÍTÓ MŰVELET FELDOLGOZÁSA
// ============================================
if (isset($_POST['change_picture']) && isset($_FILES['profile_pic'])) {
    // Feltöltési könyvtár beállítása
    $targetDir = "uploads/";
    
    // Ha a könyvtár nem létezik, létrehozzuk
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true); // 0755 = olvasási/írási jogosultságok --> ez egy kód
    }

    // Fájlnév előkészítése
    $fileName = basename($_FILES["profile_pic"]["name"]); // Eredeti fájlnév
    $targetFile = $targetDir . time() . "_" . $fileName; // Egyedi fájlnév idővel
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); // Fájl kiterjesztése

    // ============================================
    // FÁJLTÍPUS ELLENŐRZÉSE
    // ============================================
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // Engedélyezett képformátumok
    
    if (!in_array($imageFileType, $allowedTypes)) {
        $update_error = "Only just JPG, JPEG, PNG and GIF files allowed."; // Nem engedélyezett formátum
    } 
    // ============================================
    // FÁJL FELTÖLTÉSE ÉS ADATBÁZIS FRISSÍTÉSE
    // ============================================
    else {
        // Fájl feltöltése a szerverre
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
            // Profilkép elérési út frissítése az adatbázisban
            $stmt = $conn->prepare("UPDATE `4` SET profile_pic = ? WHERE email = ?");
            $stmt->bind_param("ss", $targetFile, $email);
            $stmt->execute();
            $stmt->close();
            
            $update_success = "You successfully uploaded your profile picture!"; // Sikeres üzenet
            $user['profile_pic'] = $targetFile; // Felhasználói adatok frissítése a munkamenetben
        } else {
            $update_error = "There was a problem when you are uploading you picture!"; // Feltöltési hiba
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua Mini Shop - My Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
          <a href="#" class="nav-link dropdown-toggle" id="pagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            CONTACT
          </a>
          <ul class="dropdown-menu fade-up m-0" aria-labelledby="pagesDropdown">
            <li><a href="writeUs.php" class="dropdown-item">WRITE US</a></li>
            <li><a href="faq.php" class="dropdown-item">FAQ</a></li>
          </ul>
        </div>


        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" id="pagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            MY ACCOUNT
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


<div class="profile-container">
    <h2>MY PROFILE</h2>

    <?php if ($update_success) echo "<p class='success-message'>$update_success</p>"; ?>
    <?php if ($update_error) echo "<p class='error-message'>$update_error</p>"; ?>

    <div class="profile-info text-center">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p>
            <strong>Profile picture:</strong><br>
            <?php if ($user['profile_pic']) : ?>
                <img class="profile-pic" src="<?php echo $user['profile_pic']; ?>" alt="Profile picure">
            <?php else: ?>
                No uploaded picture
            <?php endif; ?>
        </p>
    </div>

    
    <form method="post" enctype="multipart/form-data" class="mb-3">
        <input type="file" name="profile_pic" class="form-control mb-2" required>
        <button type="submit" name="change_picture" class="btn btn-primary w-100">Change my profile picture</button>
    </form>

    
    <div class="text-center my-3">
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#passwordCollapse" aria-expanded="false">
            Change my password
        </button>
    </div>

    <div class="collapse" id="passwordCollapse">
        <div class="card card-body p-3">
            <form method="post">
                <input type="password" name="current_password" placeholder="Old password" class="form-control mb-2" required>
                <input type="password" name="new_password" placeholder="New password" class="form-control mb-2" required>
                <input type="password" name="confirm_password" placeholder="Again your password" class="form-control mb-2" required>
                <button type="submit" name="change_password" class="btn btn-primary w-100">Change my password!</button>
            </form>
        </div>
    </div>
</div>

<footer>
  <div class="container">
    <div class="footer-col">
      <h2>MOTO<div class="underline"><span></span></div></h2>
      <p class="footer-para">Innovation starts here – with machines designed for creators and professionals who demand more. Whether you’re editing, rendering, or building the future, our systems are ready to keep up with your vision.</p>
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
</body>
<script src="javas.js"></script>
<script src="faq.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
<script src="chat.js"></script>
</html>
