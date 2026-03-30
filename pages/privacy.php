<?php
//WEBOLDAL NEVE ÉS a DÁTUM
$siteName = "Aqua Mini Shop";
$lastUpdated = date("F j, Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Privacy Policy – <?= htmlspecialchars($siteName) ?></title>
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
    <style>
        body { font-family: cursive; background:#f6f9fc; color:#153041; margin:0; padding:20px; }
        .wrap { max-width:980px; margin:28px auto; background:#fff; padding:32px; border-radius:10px; box-shadow:0 8px 30px rgba(8,30,52,0.06); }
        header { display:flex; justify-content:space-between; align-items:center; }
        header h1 { margin:0; font-size:20px; }
        p.meta { color:#61798a; font-size:14px; margin:6px 0 18px; }
        h2 { color:#0b3b66; margin-top:24px; }
        ul { margin:8px 0 18px 20px; }
        footer { margin-top:28px; padding-top:18px; border-top:1px solid #eef4fb; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
        a.btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#eaf6ff; color:#0b3b66; text-decoration:none; border:1px solid #d9eefc; }
        .small { font-size:13px; color:#6b8093; }
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <h1>Privacy Policy</h1>
            <div class="small"><?= htmlspecialchars($siteName) ?></div>
        </header>

        <p class="meta"><strong>Last updated:</strong> <?= $lastUpdated ?></p>

        <p>At <strong><?= htmlspecialchars($siteName) ?></strong>, we respect your privacy and are committed to protecting your personal information. This Privacy Policy explains what data we collect, how we use it, and your rights.</p>

        <h2>1. Information We Collect</h2>
        <p><strong>1.1 Information you provide:</strong></p>
        <ul>
            <li>Name</li>
            <li>Email address</li>
            <li>Shipping address</li>
            <li>Payment details (we do not store full card numbers)</li>
            <li>Order details</li>
        </ul>

        <p><strong>1.2 Automatically collected information:</strong></p>
        <ul>
            <li>IP address</li>
            <li>Browser and device information</li>
            <li>Pages visited and session data</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>To process and deliver orders</li>
            <li>To communicate order status and support</li>
            <li>For fraud prevention and security</li>
            <li>To improve our website and services</li>
        </ul>

        <h2>3. Payment Information</h2>
        <p>Payments are handled by secure third-party providers. We do not store full payment card numbers or CVV codes. We may store the last 4 digits of your card for identification.</p>

        <h2>4. Cookies</h2>
        <p>We use cookies to maintain cart contents, analyze usage, and improve your experience. You can control or disable cookies via your browser settings, but certain features may not function properly without cookies.</p>

        <h2>5. Sharing and Disclosure</h2>
        <p>We only share personal data with third parties that help us operate the site and deliver orders, such as payment processors, shipping carriers, and analytics providers. All partners are contractually required to protect your data.</p>

        <h2>6. Data Retention</h2>
        <p>We retain personal data only as long as necessary for the purposes described: order records (up to 7 years if required for tax/accounting), account data until deletion requests, and fraud-related logs as required.</p>

        <h2>7. Your Rights</h2>
        <p>You have the right to access, correct, or delete your personal data. To exercise these rights, contact us at <strong>support@aquaminishop.com</strong>. We will respond to requests in accordance with applicable law.</p>

        <h2>8. Security</h2>
        <p>We use SSL/TLS encryption, secure hosting, and third-party payment processors to protect data. However, no system is 100% secure. If you suspect a breach, contact us immediately.</p>

        <h2>9. Changes to this Policy</h2>
        <p>We may update this Privacy Policy. Changes will be posted on this page with a revised "Last updated" date.</p>

        <footer>
            <div class="small">© <?= date("Y") ?> <?= htmlspecialchars($siteName) ?> — All rights reserved.</div>
            <div>
                <a class="btn" href="terms.php">Terms & Conditions</a>
            </div>
        </footer>
    </div>
</body>
</html>

