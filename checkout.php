<?php
// ====================
// MUNKAMENET KEZDÉS
// ====================
session_start();
require_once 'config.php';

// ====================
// JOGOSULTSÁG ELLENŐRZÉS
// ====================
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// ====================
// RENDELÉSI ADATOK
// ====================
$order_list = $_SESSION['order'] ?? [];

if (empty($order_list)) {
    header("Location: myorder.php");
    exit();
}

// ====================
// KUPON BEÁLLÍTÁSOK
// ====================
$validCoupons = [
    'SUMMER10' => 0.10,
    'FREESHIP' => 'FREESHIP'
];

$shippingFlat = 4.99;
$freeShippingOver = 50.00;

// ====================
// RENDELÉS ÖSSZEGZÉS
// ====================
$subTotal = 0.0;
foreach ($order_list as $item) {
    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
    $price = isset($item['price']) ? (float)$item['price'] : 0.0;
    $subTotal += $price * $qty;
}

// ====================
// CSRF TOKEN
// ====================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// ====================
// VÁLTOZÓK INICIALIZÁLÁS
// ====================
$success = false;
$error = "";
$appliedCoupon = null;
$discountAmount = 0.0;
$shippingCost = ($subTotal >= $freeShippingOver) ? 0.0 : $shippingFlat;

// ====================
// FORM FELDOLGOZÁS
// ====================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF ellenőrzés
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request (CSRF). Try again.";
    } else {
        // Adatok kinyerése
        $name = trim($_POST["fullname"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $address = trim($_POST["address"] ?? "");
        $cardType = trim($_POST["card_type"] ?? "");
        $cardNumber = preg_replace('/\D/', '', $_POST["card_number"] ?? "");
        $expiry = trim($_POST["expiry"] ?? "");
        $cvv = preg_replace('/\D/', '', $_POST["cvv"] ?? "");
        $terms = isset($_POST['terms']) ? true : false;
        $coupon = trim($_POST['coupon'] ?? "");

        // ====================
        // VALIDÁCIÓ
        // ====================
        if ($name === "" || $email === "" || $address === "" || $cardType === "") {
            $error = "Please fill in all required fields.";
        } else if (!$terms) {
            $error = "You must accept the Terms & Conditions and Privacy Policy.";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } else if (!preg_match('/^\d{12}$/', $cardNumber)) {
            $error = "Card number must be exactly 12 digits.";
        } else if (!preg_match('/^\d{3}$/', $cvv)) {
            $error = "CVV must be exactly 3 digits.";
        } else if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
            $error = "Invalid expiry format (MM/YY).";
        } else {
            // ====================
            // KUPON FELDOLGOZÁS
            // ====================
            if ($coupon !== "" && isset($validCoupons[$coupon])) {
                $appliedCoupon = $coupon;
                if ($validCoupons[$coupon] === 'FREESHIP') {
                    $shippingCost = 0.0;
                } else {
                    $discountAmount = $subTotal * floatval($validCoupons[$coupon]);
                }
            }

            // ====================
            // VÉGÖSSZEG SZÁMÍTÁS
            // ====================
            $totalPrice = round($subTotal - $discountAmount + $shippingCost, 2);
            $cardNumberMasked = 'XXXX-XXXX-' . substr($cardNumber, -4);
            $cvvMasked = '***';
            
            // ====================
            // RENDELÉS ADATOK KÉSZÍTÉS
            // ====================
            $orderDataArray = [
                'items' => $order_list,
                'coupon' => $appliedCoupon,
                'discount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'subtotal' => $subTotal
            ];
            
            $orderData = json_encode($orderDataArray, JSON_UNESCAPED_UNICODE);

            // ====================
            // ADATBÁZIS MŰVELET
            // ====================
            try {
                $stmt = $conn->prepare("INSERT INTO orders 
                    (customer_name, customer_email, customer_address, card_type, card_number, expiry, cvv, order_data, total_price, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Not Processed', NOW())");
                
                if ($stmt === false) {
                    throw new Exception("DB prepare error: " . $conn->error);
                }
                
                $stmt->bind_param(
                    "ssssssssd",
                    $name,
                    $email,
                    $address,
                    $cardType,
                    $cardNumberMasked,
                    $expiry,
                    $cvvMasked,
                    $orderData,
                    $totalPrice
                );

                if ($stmt->execute()) {
                    // Kupon naplózása
                    if ($appliedCoupon) {
                        $couponLog = date('Y-m-d H:i:s') . " - Order saved. User: $email, Coupon: $appliedCoupon, Discount: $discountAmount, Shipping: $shippingCost\n";
                        file_put_contents('coupon_log.txt', $couponLog, FILE_APPEND);
                    }
                    
                    // Rendelés törlése a munkamenetből
                    unset($_SESSION['order']);
                    $success = true;
                } else {
                    throw new Exception("Error saving order: " . $stmt->error);
                }

                $stmt->close();
                
            } catch (Exception $e) {
                $error = $e->getMessage();
                error_log("Checkout error: " . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Aqua Mini Shop - Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ==================== */
        /* VÁLTOZÓK */
        /* ==================== */
        :root {
            --accent: #4facfe;
            --accent-2: #00c6ff;
            --muted: #6c7a89;
            --card: #ffffff;
        }
        
        /* ==================== */
        /* ALAP STÍLUSOK */
        /* ==================== */
        body { 
            background: linear-gradient(135deg, #eef7ff 0%, #e6fbff 100%); 
            min-height: 100vh; 
            padding: 20px; 
            font-family: "Inter", "Segoe UI", Arial, sans-serif; 
            color: #16324f;
        }
        
        /* ==================== */
        /* FO KONTÉNER */
        /* ==================== */
        .checkout-container { 
            background: var(--card); 
            max-width: 980px; 
            margin: 18px auto; 
            padding: 28px; 
            border-radius: 14px; 
            box-shadow: 0 10px 30px rgba(8, 30, 52, 0.08); 
        }
        
        /* ==================== */
        /* GRID RENDSZER */
        /* ==================== */
        .grid {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 24px;
        }
        
        @media (max-width: 900px) { 
            .grid { 
                grid-template-columns: 1fr; 
            } 
        }
        
        /* ==================== */
        /* TERMÉK DOBOZ */
        /* ==================== */
        .product-box { 
            display: flex; 
            gap: 12px; 
            margin-bottom: 12px; 
            background: #fbfdff; 
            padding: 12px; 
            border-radius: 10px; 
            border: 1px solid #eef4fb;
            align-items: center;
        }
        
        .product-box img { 
            width: 82px; 
            height: 82px; 
            object-fit: cover; 
            border-radius: 8px; 
            flex-shrink: 0;
        }
        
        .product-info h5 { 
            margin: 0 0 6px 0; 
            font-size: 15px; 
        }
        
        .product-info p { 
            margin: 0; 
            font-size: 14px; 
            color: var(--muted); 
        }
        
        /* ==================== */
        /* ÖSSZEGZŐ PANEL */
        /* ==================== */
        .summary {
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #eef4fb;
            background: #fcfeff;
        }
        
        .summary-row { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 6px 0; 
        }
        
        .total-price { 
            font-size: 20px; 
            font-weight: 700; 
            color: #0b3b66; 
        }
        
        /* ==================== */
        /* KUPON SOR */
        /* ==================== */
        .coupon-row { 
            display: flex; 
            gap: 8px; 
            margin-top: 10px; 
        }
        
        /* ==================== */
        /* FORM ELEMEK */
        /* ==================== */
        label { 
            font-weight: 600; 
            font-size: 14px; 
            color: #27475a; 
        }
        
        input.form-control, 
        select.form-control {
            height: 46px;
            border-radius: 10px;
            border: 1px solid #e6eef8;
            padding-left: 12px;
            font-size: 15px;
            transition: 0.18s;
        }
        
        input.form-control:focus, 
        select.form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 6px 18px rgba(79, 172, 254, 0.14);
        }
        
        /* ==================== */
        /* BEMENET KONTÉNER */
        /* ==================== */
        .input-container { 
            position: relative; 
            margin-bottom: 12px; 
        }
        
        .input-feedback {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            pointer-events: none;
        }
        
        /* ==================== */
        /* FIZETÉSI DOBOZ */
        /* ==================== */
        .payment-box {
            padding: 14px;
            margin-top: 6px;
            border-radius: 10px;
            border: 1px solid #eef4fb;
            background: linear-gradient(180deg, #ffffff, #fbfdff);
        }
        
        /* ==================== */
        /* MEGBÍZHATÓSÁG SOR */
        /* ==================== */
        .trust-row { 
            display: flex; 
            gap: 10px; 
            align-items: center; 
            margin-top: 10px; 
        }
        
        .trust-badge { 
            display: flex; 
            gap: 8px; 
            align-items: center; 
            background: #f7fbff; 
            padding: 8px 10px; 
            border-radius: 8px; 
            border: 1px solid #eaf6ff; 
            font-size: 13px; 
            color: #0b3b66; 
        }
        
        /* ==================== */
        /* FIZETÉS GOMB */
        /* ==================== */
        .btn-pay {
            height: 50px;
            font-size: 16px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(11, 59, 102, 0.08);
        }
        
        /* ==================== */
        /* KISZÖVEGEK */
        /* ==================== */
        .small-muted { 
            color: #60788f; 
            font-size: 13px; 
        }
        
        .contact { 
            margin-top: 12px; 
            font-size: 13px; 
            color: #27475a; 
        }
        
        /* ==================== */
        /* ÉRTESÍTÉSEK */
        /* ==================== */
        .alert { 
            border-radius: 10px; 
        }
        
        /* ==================== */
        /* BETÖLTŐ ANIMÁCIÓ */
        /* ==================== */
        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }
        
        .processing-spinner {
            width: 80px;
            height: 80px;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #4facfe;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .processing-text {
            font-size: 18px;
            color: #16324f;
            font-weight: 600;
        }
        
        .processing-subtext {
            font-size: 14px;
            color: #6c7a89;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- ==================== -->
<!-- BETÖLTŐ OVERLAY -->
<!-- ==================== -->
<div id="processingOverlay" class="processing-overlay">
    <div class="processing-spinner"></div>
    <div class="processing-text">Processing Payment...</div>
    <div class="processing-subtext">Please wait while we process your payment</div>
    <div class="processing-timer mt-3">
        <small>This may take up to 10 seconds</small>
    </div>
</div>

<!-- ==================== -->
<!-- FŐ TARTALOM -->
<!-- ==================== -->
<div class="checkout-container">
    <h2 style="margin:0 0 20px 0;">💳 Checkout</h2>

    <!-- ==================== -->
    <!-- SIKER ÜZENET -->
    <!-- ==================== -->
    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            <h4>✔ Payment Successful!</h4>
            <p>Your order has been recorded. Order ID: #<?= $conn->insert_id ?></p>
            <p>Wait while your package will arrive!</p>
        </div>
        <div style="margin-top:12px;">
            <a href="products.php" class="btn btn-primary w-100">Back to Shop</a>
        </div>
        <div style="margin-top:10px;" class="contact">
            Questions? Email: <strong>kapcsandi.tomi@gmail.com</strong> or call: <strong>+36 70 645 1793</strong>
        </div>
        <?php exit; endif; ?>

    <!-- ==================== -->
    <!-- HIBA ÜZENET -->
    <!-- ==================== -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ==================== -->
    <!-- GRID RENDSZER -->
    <!-- ==================== -->
    <div class="grid">
        <!-- ==================== -->
        <!-- BAL OLDAL: ÖSSZEGZÉS -->
        <!-- ==================== -->
        <div>
            <div class="summary">
                <h4 style="margin-top:0;">🛒 Order Summary</h4>

                <!-- TERMÉK LISTA -->
                <?php foreach ($order_list as $item): 
                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                    $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                    $name = htmlspecialchars($item['name'] ?? 'Product');
                    $img = htmlspecialchars($item['image'] ?? 'placeholder.png');
                ?>
                    <div class="product-box">
                        <img src="<?= $img ?>" alt="<?= $name ?>">
                        <div class="product-info" style="flex:1;">
                            <h5><?= $name ?></h5>
                            <p class="small-muted"><?= $qty ?> × $<?= number_format($price,2) ?></p>
                        </div>
                        <div style="text-align:right; min-width:85px;">
                            <div style="font-weight:700;">$<?= number_format($price * $qty,2) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- RÉSZLETES ÖSSZEGZÉS -->
                <div class="summary-row">
                    <div class="small-muted">Subtotal</div>
                    <div class="small-muted">$<?= number_format($subTotal,2) ?></div>
                </div>

                <div class="summary-row">
                    <div class="small-muted">Shipping</div>
                    <div class="small-muted" id="shippingLabel">
                        <?php if ($shippingCost == 0): ?>
                            <span style="color:green;">FREE</span>
                        <?php else: ?>
                            $<?= number_format($shippingCost,2) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="summary-row">
                    <div class="small-muted">Discount</div>
                    <div class="small-muted" id="discountLabel" style="color:green;">
                        -$<?= number_format($discountAmount,2) ?>
                    </div>
                </div>

                <!-- VÉGÖSSZEG -->
                <div class="summary-row" style="border-top:1px dashed #e6eef8; margin-top:8px; padding-top:10px;">
                    <div class="total-price">Total</div>
                    <div class="total-price" id="finalTotal">
                        $<?= number_format(round($subTotal - $discountAmount + $shippingCost,2),2) ?>
                    </div>
                </div>

                <!-- KUPON BEVITEL -->
                <div class="coupon-row">
                    <input id="couponInput" name="coupon_local" class="form-control" placeholder="Coupon code (e.g. SUMMER10)" />
                    <button id="applyCouponBtn" class="btn btn-outline-primary">Apply</button>
                </div>

                <!-- MEGBÍZHATÓSÁG JELZŐK -->
                <div class="trust-row">
                    <div class="trust-badge">🔒 Secure payment</div>
                    <div class="trust-badge">✅ 30-day return</div>
                    <div class="trust-badge">💳 Visa / MC</div>
                </div>
            </div>

            <!-- KAPCSOLAT -->
            <div class="contact">
                <strong>Support:</strong> kapcsandi.tomi@gmail.com • +36 70 645 1793
            </div>
        </div>

        <!-- ==================== -->
        <!-- JOBB OLDAL: ŰRLAP -->
        <!-- ==================== -->
        <div>
            <form method="POST" id="checkoutForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <!-- SZÁLLÍTÁSI ADATOK -->
                <h4>💼 Shipping Details</h4>
                <label for="fullname">Full Name *</label>
                <input id="fullname" name="fullname" class="form-control mb-2" required value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>">

                <label for="email">Email Address *</label>
                <input id="email" name="email" type="email" class="form-control mb-2" required value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">

                <label for="address">Shipping Address *</label>
                <textarea id="address" name="address" class="form-control mb-3" rows="3" required></textarea>

                <!-- FIZETÉSI ADATOK -->
                <div class="payment-box">
                    <h4>💳 Payment Details</h4>

                    <label for="card_type">Card Type *</label>
                    <select id="card_type" name="card_type" class="form-control mb-2" required>
                        <option value="">Select…</option>
                        <option>Visa</option>
                        <option>MasterCard</option>
                        <option>Revolut</option>
                        <option>Maestro</option>
                    </select>

                    <div class="input-container">
                        <label for="card_number">Card Number *</label>
                        <input id="card_number" name="card_number" class="form-control" maxlength="12" inputmode="numeric" required>
                        <span class="input-feedback" id="cardFeedback"></span>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;" class="input-container">
                            <label for="expiry">Expiry (MM/YY) *</label>
                            <input id="expiry" name="expiry" class="form-control" maxlength="5" required placeholder="MM/YY">
                            <span class="input-feedback" id="expiryFeedback"></span>
                        </div>
                        <div style="width:120px;" class="input-container">
                            <label for="cvv">CVV *</label>
                            <input id="cvv" name="cvv" class="form-control" maxlength="3" inputmode="numeric" required>
                            <span class="input-feedback" id="cvvFeedback"></span>
                        </div>
                    </div>

                    <div style="margin-top:10px;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label small-muted" for="terms">
                                I accept the <a href="terms.php" target="_blank">Terms & Conditions</a> 
                                and <a href="privacy.php" target="_blank">Privacy Policy</a>.
                            </label>
                        </div>
                    </div>
                </div>

                <!-- REJTETT KUPON MEZŐ -->
                <input type="hidden" name="coupon" id="couponHidden" value="">

                <!-- FIZETÉS GOMB -->
                <div style="margin-top:12px;">
                    <button id="payBtn" class="btn btn-success btn-pay w-100" type="submit">
                        Pay Now
                    </button>
                </div>
            </form>

            <!-- KIS BETÖLTŐ -->
            <div id="loader" style="display:none; text-align:center; margin-top:12px;">
                <div class="spinner-border text-primary" role="status" style="width:48px; height:48px;"></div>
                <p class="small-muted" style="margin-top:8px;">Processing… Please wait.</p>
            </div>
        </div>
    </div>
</div>

<!-- ==================== -->
<!-- JAVASCRIPT KÓD -->
<!-- ==================== -->
<script>
// ====================
// VÁLTOZÓK
// ====================
const cardInput = document.getElementById('card_number');
const cardFeedback = document.getElementById('cardFeedback');
const expiryInput = document.getElementById('expiry');
const expiryFeedback = document.getElementById('expiryFeedback');
const cvvInput = document.getElementById('cvv');
const cvvFeedback = document.getElementById('cvvFeedback');
const applyCouponBtn = document.getElementById('applyCouponBtn');
const couponInput = document.getElementById('couponInput');
const couponHidden = document.getElementById('couponHidden');
const shippingLabel = document.getElementById('shippingLabel');
const discountLabel = document.getElementById('discountLabel');
const finalTotal = document.getElementById('finalTotal');
const payBtn = document.getElementById('payBtn');
const form = document.getElementById('checkoutForm');
const processingOverlay = document.getElementById('processingOverlay');

// Biztonsági ellenőrzés - ha nincs overlay, csinálunk egyet
if (!processingOverlay) {
    console.error("Processing overlay not found in DOM!");
}

let subtotal = <?= json_encode($subTotal) ?>;
let shipping = <?= json_encode($shippingCost) ?>;
let discount = <?= json_encode($discountAmount) ?>;
const freeShippingOver = <?= json_encode($freeShippingOver) ?>;
const shippingFlat = <?= json_encode($shippingFlat) ?>;

// ====================
// ÖSSZEGZÉS FRISSÍTÉS
// ====================
function updateSummary() {
    if (shippingLabel) {
        shippingLabel.innerHTML = shipping === 0 ? 
            '<span style="color:green;">FREE</span>' : 
            '$' + shipping.toFixed(2);
    }
    
    if (discountLabel) {
        discountLabel.textContent = '-$' + discount.toFixed(2);
        discountLabel.style.color = discount > 0 ? 'green' : '#60788f';
    }
    
    if (finalTotal) {
        finalTotal.textContent = '$' + ((subtotal - discount + shipping).toFixed(2));
    }
}

// ====================
// KUPON ALKALMAZÁS
// ====================
if (applyCouponBtn && couponInput) {
    applyCouponBtn.addEventListener('click', function(e){
        e.preventDefault();
        const code = couponInput.value.trim().toUpperCase();
        
        if (!code) { 
            alert('Enter a coupon code!'); 
            return; 
        }

        const known = {
            'SUMMER10': { type:'percent', value:0.10 },
            'FREESHIP': { type:'shipping', value:0 }
        };

        if (!known[code]) {
            alert('Unknown coupon code.');
            return;
        }

        if (couponHidden) {
            couponHidden.value = code;
        }

        if (known[code].type === 'percent') {
            discount = parseFloat((subtotal * known[code].value).toFixed(2));
            shipping = (subtotal >= freeShippingOver) ? 0 : shippingFlat;
        } else if (known[code].type === 'shipping') {
            discount = 0;
            shipping = 0;
        }

        updateSummary();
        alert('Coupon applied successfully!');
    });
}

// ====================
// BANKKÁRTYA VALIDÁCIÓ
// ====================
if (cardInput && cardFeedback) {
    cardInput.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,12);
        if (/^\d{12}$/.test(this.value)) {
            cardFeedback.textContent = '✔';
            cardFeedback.style.color = 'green';
        } else {
            cardFeedback.textContent = '❌';
            cardFeedback.style.color = 'red';
        }
    });
}

// ====================
// LEJÁRAT VALIDÁCIÓ
// ====================
if (expiryInput && expiryFeedback) {
    expiryInput.addEventListener('input', function(){
        let v = this.value.replace(/\D/g,'').slice(0,4);
        if (v.length >= 3) this.value = v.slice(0,2) + '/' + v.slice(2);
        else this.value = v;
        if (/^(0[1-9]|1[0-2])\/\d{2}$/.test(this.value)) {
            expiryFeedback.textContent = '✔';
            expiryFeedback.style.color = 'green';
        } else {
            expiryFeedback.textContent = '❌';
            expiryFeedback.style.color = 'red';
        }
    });
}

// ====================
// CVV VALIDÁCIÓ
// ====================
if (cvvInput && cvvFeedback) {
    cvvInput.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,3);
        if (/^\d{3}$/.test(this.value)) {
            cvvFeedback.textContent = '✔';
            cvvFeedback.style.color = 'green';
        } else {
            cvvFeedback.textContent = '❌';
            cvvFeedback.style.color = 'red';
        }
    });
}

// ====================
// ŰRLAP BEKÜLDÉS - JAVÍTOTT
// ====================
if (form && payBtn) {
    form.addEventListener('submit', function(e){
        // Validáció
        const fullname = document.getElementById('fullname')?.value.trim() || '';
        const email = document.getElementById('email')?.value.trim() || '';
        const address = document.getElementById('address')?.value.trim() || '';
        const cardVal = cardInput?.value.trim() || '';
        const expiryVal = expiryInput?.value.trim() || '';
        const cvvVal = cvvInput?.value.trim() || '';
        const terms = document.getElementById('terms')?.checked || false;

        // Validációs üzenetek
        let validationError = '';
        
        if (!fullname || !email || !address) {
            validationError = 'Please fill in shipping details.';
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            validationError = 'Enter a valid email address.';
        } else if (!/^\d{12}$/.test(cardVal)) {
            validationError = 'Card number must be 12 digits.';
        } else if (!/^\d{3}$/.test(cvvVal)) {
            validationError = 'CVV must be 3 digits.';
        } else if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryVal)) {
            validationError = 'Expiry format: MM/YY';
        } else if (!terms) {
            validationError = 'You must accept the Terms & Conditions and Privacy Policy.';
        }

        // Ha van hiba, megállítjuk a beküldést
        if (validationError) {
            e.preventDefault();
            alert(validationError);
            return false;
        }

        // Ha minden valid, indítjuk a betöltőt
        e.preventDefault(); // Először megállítjuk, hogy kézzel kezelhessük
        
        // Betöltő animáció indítása
        payBtn.disabled = true;
        payBtn.textContent = 'Processing...';
        
        // Megjelenítjük a betöltő overlay-t
        if (processingOverlay) {
            processingOverlay.style.display = 'flex';
        } else {
            // Ha nincs overlay, csinálunk egy egyszerűt
            const tempOverlay = document.createElement('div');
            tempOverlay.id = 'tempOverlay';
            tempOverlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.95);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                flex-direction: column;
            `;
            tempOverlay.innerHTML = `
                <div style="width: 80px; height: 80px; border: 8px solid #f3f3f3; border-top: 8px solid #4facfe; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px;"></div>
                <div style="font-size: 18px; color: #16324f; font-weight: 600;">Processing Payment...</div>
                <div style="font-size: 14px; color: #6c7a89; margin-top: 10px;">Please wait while we process your payment</div>
                <div style="margin-top: 15px;"><small>Processing... 10s remaining</small></div>
            `;
            document.body.appendChild(tempOverlay);
        }
        
        // 10 másodperc időzítő
        let timer = 10;
        const timerUpdate = setInterval(() => {
            timer--;
            const timerElement = document.querySelector('.processing-timer') || 
                                document.querySelector('#tempOverlay div:last-child');
            if (timerElement) {
                if (timer > 0) {
                    timerElement.innerHTML = `<small>Processing... ${timer}s remaining</small>`;
                } else {
                    timerElement.innerHTML = '<small>Almost done...</small>';
                }
            }
            
            if (timer <= 0) {
                clearInterval(timerUpdate);
            }
        }, 1000);
        
        // 10 másodperc múlva elküldjük a formot
        setTimeout(() => {
            // Eltávolítjuk a temp overlay-t ha van
            const tempOverlay = document.getElementById('tempOverlay');
            if (tempOverlay) {
                tempOverlay.remove();
            }
            
            // Beküldjük a formot
            form.submit();
        }, 10000); // 10 másodperc
        
        return false;
    });
}

// ====================
// INICIALIZÁLÁS
// ====================
// Várunk egy kicsit, hogy minden betöltődjön
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
    
    // Hibakeresés
    console.log('Checkout page loaded successfully');
    console.log('Processing overlay found:', !!document.getElementById('processingOverlay'));
    console.log('Form found:', !!document.getElementById('checkoutForm'));
});
</script>
</body>
</html>