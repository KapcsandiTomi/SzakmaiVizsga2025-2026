<?php
session_start();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karbantartás - Aqua Mini Shop</title>
    <link rel="icon" href="/Szakmai/letoles.jpg?v=1" type="image/jpeg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: cursive, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .maintenance-container {
            max-width: 600px;
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .maintenance-icon {
            font-size: 80px;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .maintenance-title {
            font-size: 3.5rem;
            color: #dc2626;
            font-weight: 900;
            margin-bottom: 20px;
            letter-spacing: -2px;
        }
        
        .maintenance-subtitle {
            font-size: 1.5rem;
            color: #374151;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .maintenance-text {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 40px;
            line-height: 1.8;
        }
        
        .maintenance-box {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            border-left: 5px solid #dc2626;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 40px;
            color: #7f1d1d;
            font-weight: 500;
        }
        
        .logout-btn {
            display: inline-block;
            padding: 15px 50px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }
        
        .maintenance-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e5e7eb;
            color: #9ca3af;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">🔧</div>
        
        <h1 class="maintenance-title">MAINTENANCE</h1>
        <h2 class="maintenance-subtitle">We are currently under maintenance</h2>
        
        <p class="maintenance-text">
            We apologize! The site is currently unavailable due to ongoing maintenance work.
            <br><br>
            Please come back later.
        </p>
        
        <div class="maintenance-box">
            ⚠️ The site is only accessible to administrators during maintenance.
        </div>
        
        <a href="logout.php" class="logout-btn">Logout</a>
        
        <div class="maintenance-info">
            <p>Thank you for your patience!</p>
            <p>© Aqua Mini Shop 2026</p>
        </div>
    </div>
</body>
</html>
