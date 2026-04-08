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
$adminReturnTo = '/Szakmai/pages/' . ltrim($currentPage, '/');
$adminModalAutoOpen = isset($_GET['admin_prompt']) || isset($_GET['admin_error']);
$adminErrorMessage = null;

if (isset($_GET['admin_error'])) {
    $adminErrorMessage = $_GET['admin_error'] === 'denied'
        ? 'You do not have permission to open the admin panel.'
        : 'Wrong password. Access denied.';
}
?>

<nav class="ams-user-navbar navbar navbar-expand-lg sticky-top">
    <div class="container-fluid ams-nav-shell">
        <a href="fooldal.php" class="ams-brand" aria-label="Aqua Mini Shop Home">
            <span class="ams-brand-main">AQUA</span>
            <span class="ams-brand-sub">MINI SHOP</span>
        </a>

        <?php if (isset($_SESSION['is_admin']) && (int) $_SESSION['is_admin'] === 1): ?>
            <button type="button" class="ams-admin-link" data-admin-modal-open>
                <i class="fas fa-cog" aria-hidden="true"></i>
                <span>Admin</span>
            </button>
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

<?php if (isset($_SESSION['is_admin']) && (int) $_SESSION['is_admin'] === 1): ?>
    <div
        id="amsAdminPasswordModal"
        class="ams-admin-modal<?php echo $adminModalAutoOpen ? ' is-visible' : ''; ?>"
        aria-hidden="<?php echo $adminModalAutoOpen ? 'false' : 'true'; ?>"
        data-auto-open="<?php echo $adminModalAutoOpen ? 'true' : 'false'; ?>"
    >
        <div class="ams-admin-modal__card" role="dialog" aria-modal="true" aria-labelledby="amsAdminModalTitle">
            <button type="button" class="ams-admin-modal__close" data-admin-modal-close aria-label="Close">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>

            <div class="ams-admin-modal__icon">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
            </div>

            <h3 id="amsAdminModalTitle">Give the Password</h3>
            <p class="ams-admin-modal__text">Enter the admin password to continue.</p>

            <?php if ($adminErrorMessage !== null): ?>
                <div class="ams-admin-modal__error"><?php echo htmlspecialchars($adminErrorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form action="/Szakmai/handler/admin_access.php" method="POST" class="ams-admin-modal__form">
                <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($adminReturnTo, ENT_QUOTES, 'UTF-8'); ?>">
                <label for="adminPasswordInput" class="ams-admin-modal__label">Password</label>
                <input
                    type="password"
                    id="adminPasswordInput"
                    name="admin_password"
                    class="ams-admin-modal__input"
                    placeholder="Give the Password"
                    required
                    autocomplete="current-password"
                >
                <div class="ams-admin-modal__actions">
                    <button type="button" class="ams-admin-modal__secondary" data-admin-modal-close>Cancel</button>
                    <button type="submit" class="ams-admin-modal__primary">Enter</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('amsAdminPasswordModal');

        if (!modal) {
            return;
        }

        const passwordInput = modal.querySelector('#adminPasswordInput');
        const openModal = function () {
            modal.classList.add('is-visible');
            modal.setAttribute('aria-hidden', 'false');

            if (passwordInput) {
                window.setTimeout(function () {
                    passwordInput.focus();
                }, 80);
            }
        };

        const closeModal = function () {
            modal.classList.remove('is-visible');
            modal.setAttribute('aria-hidden', 'true');
        };

        document.querySelectorAll('[data-admin-modal-open]').forEach(function (button) {
            button.addEventListener('click', openModal);
        });

        modal.querySelectorAll('[data-admin-modal-close]').forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('is-visible')) {
                closeModal();
            }
        });

        if (modal.dataset.autoOpen === 'true') {
            openModal();
        }
    });
    </script>
<?php endif; ?>
