<?php
if (!isset($currentPage) || !is_string($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
}

if (!function_exists('amsNavActiveClass')) {
    function amsNavActiveClass(array $pages, string $currentPage): string
    {
        return in_array($currentPage, $pages, true) ? ' active' : '';
    }
}

$contactActive = amsNavActiveClass(['writeUs.php', 'faq.php'], $currentPage);
$accountActive = amsNavActiveClass(['profile.php', 'myorder.php'], $currentPage);
?>

<nav class="ams-user-navbar navbar navbar-expand-lg sticky-top">
    <div class="container-fluid ams-nav-shell">
        <a href="fooldal.php" class="ams-brand" aria-label="Aqua Mini Shop Home">
            <span class="ams-brand-main">AQUA</span>
            <span class="ams-brand-sub">MINI SHOP</span>
        </a>

        <?php if (isset($_SESSION['is_admin']) && (int) $_SESSION['is_admin'] === 1): ?>
            <a href="/Szakmai/admin/index.php" class="ams-admin-link">
                <i class="fas fa-cog" aria-hidden="true"></i>
                <span>Admin</span>
            </a>
        <?php endif; ?>

        <button
            class="navbar-toggler ams-menu-toggle"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#amsNavbarCollapse"
            aria-controls="amsNavbarCollapse"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="amsNavbarCollapse">
            <ul class="navbar-nav ms-auto ams-main-links">
                <li class="nav-item">
                    <a class="nav-link<?php echo amsNavActiveClass(['fooldal.php'], $currentPage); ?>" href="fooldal.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo amsNavActiveClass(['about.php'], $currentPage); ?>" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo amsNavActiveClass(['products.php'], $currentPage); ?>" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo amsNavActiveClass(['rateus.php'], $currentPage); ?>" href="rateus.php">Rate Us</a>
                </li>

                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle<?php echo $contactActive; ?>"
                        href="#"
                        id="amsContactDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        Contact
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="amsContactDropdown">
                        <li><a class="dropdown-item<?php echo amsNavActiveClass(['writeUs.php'], $currentPage); ?>" href="writeUs.php">Write Us</a></li>
                        <li><a class="dropdown-item<?php echo amsNavActiveClass(['faq.php'], $currentPage); ?>" href="faq.php">FAQ</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle<?php echo $accountActive; ?>"
                        href="#"
                        id="amsAccountDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        My Account
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="amsAccountDropdown">
                        <li><a class="dropdown-item<?php echo amsNavActiveClass(['profile.php'], $currentPage); ?>" href="profile.php">My Profile</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        <li><a class="dropdown-item<?php echo amsNavActiveClass(['myorder.php'], $currentPage); ?>" href="myorder.php">My Orders</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
