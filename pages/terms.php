<?php

//DATUM ÉS NEVE AZ OLDALNAK
$siteName = "Aqua Mini Shop";
$lastUpdated = date("F j, Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Terms & Conditions – <?= htmlspecialchars($siteName) ?></title>
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
    <style>
        body { font-family: cursive; background:#f5f8fb; color:#1f3342; margin:0; padding:20px; }
        .wrap { max-width:980px; margin:28px auto; background:#fff; padding:32px; border-radius:10px; box-shadow:0 8px 30px rgba(8,30,52,0.06); }
        header { display:flex; justify-content:space-between; align-items:center; gap:12px; }
        header h1 { margin:0; font-size:20px; }
        p.meta { color:#60788f; font-size:14px; margin:6px 0 18px; }
        h2 { color:#0b3b66; margin-top:26px; }
        ul { margin:8px 0 18px 20px; }
        footer { margin-top:28px; padding-top:18px; border-top:1px solid #eef4fb; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
        a.btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#eaf6ff; color:#0b3b66; text-decoration:none; border:1px solid #d9eefc; }
        .small { font-size:13px; color:#6b8093; }
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <h1>Terms & Conditions</h1>
            <div class="small"><?= htmlspecialchars($siteName) ?></div>
        </header>

        <p class="meta"><strong>Last updated:</strong> <?= $lastUpdated ?></p>

        <p>Welcome to <strong><?= htmlspecialchars($siteName) ?></strong>. By accessing or placing an order with us, you agree to be bound by these Terms & Conditions. Please read them carefully before using our website or purchasing any products.</p>

        <h2>1. General</h2>
        <p>This website is operated by <?= htmlspecialchars($siteName) ?> ("we", "us", "our"). By using our site or placing an order you confirm that you are at least 18 years old or have permission from a legal guardian.</p>

        <h2>2. Products and Availability</h2>
        <p>We strive to provide accurate product descriptions and availability. However, colors may vary by screen and availability may change without notice. If an ordered item is unavailable, we reserve the right to cancel the order and issue a refund.</p>

        <h2>3. Pricing and Payment</h2>
        <ul>
            <li>All prices are displayed in USD unless otherwise noted.</li>
            <li>Payment is processed via a secure third-party payment provider. We do not store full card numbers or CVV codes.</li>
            <li>Orders are billed at the time of purchase.</li>
        </ul>

        <h2>4. Shipping</h2>
        <p>Shipping costs and estimated delivery times are shown during checkout. Delivery times are estimates and carriers may experience delays. We are not responsible for customs delays or fees on international shipments.</p>

        <h2>5. Returns and Refunds</h2>
        <p>We offer a 30-day return policy for unused items in their original packaging. To request a return, contact <strong>kapcsandi.tomi@gmail.com</strong> with your order number and reason for return. Refunds are issued to the original payment method after the returned item is inspected.</p>

        <h2>6. Order Cancellation</h2>
        <p>You may cancel an order within 12 hours of placing it. After this period, orders may already be processed or shipped and cannot be canceled.</p>

        <h2>7. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, our liability is limited to the total amount paid for the order. We shall not be liable for indirect, incidental, or consequential damages.</p>

        <h2>8. Changes to Terms</h2>
        <p>We may update these Terms at any time. Continued use of the site after changes indicates acceptance of the updated Terms.</p>

        <h2>9. Contact</h2>
        <p>If you have questions about these Terms, please contact us at <strong>aquaminishop@gmail.com</strong>.</p>

        <footer>
            <div class="small">© <?= date("Y") ?> <?= htmlspecialchars($siteName) ?> — All rights reserved.</div>
            <div>
                <a class="btn" href="privacy.php">Privacy Policy</a>
            </div>
        </footer>
    </div>
</body>
</html>

