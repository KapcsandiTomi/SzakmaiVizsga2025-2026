<?php
session_start();
require_once 'config.php';
require_once 'handler/ratedata.php';
require_once 'handler/ratehandler.php';

// Handler
$handler = new RateHandler();
$handler->handleRequest();

// Adatok lekérése
$model = $handler->getModel();
$errors = $handler->getErrors();

$review_title = $model->getReviewTitle();
$page_title = $model->getPageTitle();
$reviews = $model->getAllReviews();
$stats = $model->getReviewStats();

// Sikerüzenet
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
if(isset($_SESSION['success_message'])) {
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="letoles.jpg" type="image/png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #1d9cd3ff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .site-info {
            flex: 1;
        }
        
        .site-name {
            color: #333;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(to right, #667eea, #1d9cd3ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .site-subtitle {
            color: #666;
            font-size: 18px;
            font-weight: 500;
        }
        
        .home-btn {
            background: linear-gradient(to right, #667eea, #15e8f7ff);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .home-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .review-container {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .section-title {
            color: #333;
            font-size: 28px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
        }
        
        .question {
            text-align: center;
            font-size: 22px;
            color: #667eea;
            margin-bottom: 30px;
            font-weight: 600;
            padding: 15px;
            background: linear-gradient(to right, #f8f9ff, #f0f7ff);
            border-radius: 10px;
            border-left: 5px solid #667eea;
        }

        .error-container {
            background: #ffe6e6;
            border: 2px solid #ff3333;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: <?php echo !empty($errors) ? 'block' : 'none'; ?>;
        }
        
        .error-title {
            color: #cc0000;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .error-list {
            list-style-type: none;
            padding-left: 10px;
        }
        
        .error-list li {
            color: #ff3333;
            margin-bottom: 5px;
            padding: 5px 10px;
            background: rgba(255, 51, 51, 0.1);
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .success-container {
            background: #e6ffe6;
            border: 2px solid #33cc33;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: <?php echo !empty($success_message) ? 'block' : 'none'; ?>;
        }
        
        .success-title {
            color: #009933;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-weight: 600;
            font-size: 16px;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .rating-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .stars-input {
            display: flex;
            justify-content: center;
            gap: 15px;
            font-size: 32px;
            padding: 15px;
            background: #f8f9ff;
            border-radius: 10px;
        }
        
        .star {
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s;
            transform: scale(1);
        }
        
        .star:hover {
            transform: scale(1.2);
        }
        
        .star:hover,
        .star.active {
            color: #f39c12;
        }
        
        .hidden-rating {
            display: none;
        }
        
        .rating-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            color: #777;
            font-size: 14px;
        }
        
        textarea.form-control {
            min-height: 130px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .submit-btn {
            background: linear-gradient(to right, #2b00edff, #129be5ff);
            color: white;
            border: none;
            padding: 18px 32px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(to right, #4cd1faff, #1ec8fcff);
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(76, 209, 250, 0.4);
        }
        
        .reviews-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #f0f2f5;
        }
        
        .reviews-title {
            font-size: 26px;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
        }
        
        .review-card {
            background: #f9f9f9;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            border-left: 5px solid #667eea;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .review-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .reviewer-name {
            font-weight: 700;
            color: #333;
            font-size: 18px;
        }
        
        .review-date {
            color: #888;
            font-size: 14px;
        }
        
        .review-stars {
            color: #f39c12;
            font-size: 18px;
            margin-bottom: 12px;
        }
        
        .review-comment {
            color: #555;
            line-height: 1.7;
            font-size: 16px;
        }
        
        .no-reviews {
            text-align: center;
            color: #888;
            padding: 40px 30px;
            font-style: italic;
            font-size: 18px;
            background: #f8f9ff;
            border-radius: 10px;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            background: linear-gradient(to right, #667eea, #1d9cd3ff);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stat-item h3 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .stat-item p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .home-btn {
                width: 100%;
                justify-content: center;
            }
            
            .review-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .stats {
                flex-direction: column;
                gap: 20px;
            }
            
            .stars-input {
                gap: 10px;
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="site-info">
            <div class="site-name">Customer Feedback</div>
            <div class="site-subtitle">Share your shopping experience with us</div>
        </div>
        <a href="fooldal.php" class="home-btn">
            <i class="fas fa-home"></i> Back to Home Page
        </a>
    </div>
    
    <div class="review-container">
        <!-- SIKER ÜZENET -->
        <?php if(!empty($success_message)): ?>
        <div class="success-container">
            <div class="success-title">
                <i class="fas fa-check-circle"></i> Success!
            </div>
            <p><?php echo $success_message; ?></p>
        </div>
        <?php endif; ?>
        
        <!-- HIBA ÜZENETEK -->
        <?php if(!empty($errors)): ?>
        <div class="error-container">
            <div class="error-title">
                <i class="fas fa-exclamation-triangle"></i> Please correct the following errors:
            </div>
            <ul class="error-list">
                <?php foreach($errors as $error): ?>
                <li><i class="fas fa-times-circle"></i> <?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="question">
            <?php echo $review_title; ?>
        </div>
        
        <h2 class="section-title">Write Your Review</h2>
        
        <!-- Értékelés űrlap -->
        <form method="POST" id="reviewForm">
            <div class="form-group">
                <label for="user_name">Your Name</label>
                <input type="text" id="user_name" name="user_name" class="form-control" 
                       placeholder="Enter your name" required 
                       value="<?php echo isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Your Rating</label>
                <div class="rating-container">
                    <div class="stars-input" id="starsInput">
                        <?php 
                        $selected_rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
                        for($i = 1; $i <= 5; $i++): 
                            $active = ($i <= $selected_rating) ? 'active' : '';
                        ?>
                        <span class="star <?php echo $active; ?>" data-value="<?php echo $i; ?>">
                            <i class="<?php echo ($i <= $selected_rating) ? 'fas' : 'far'; ?> fa-star"></i>
                        </span>
                        <?php endfor; ?>
                    </div>
                    <div class="rating-labels">
                        <span>Poor</span>
                        <span>Excellent</span>
                    </div>
                    <select name="rating" id="ratingSelect" class="hidden-rating" required>
                        <option value="">Select rating</option>
                        <option value="5" <?php echo ($selected_rating == 5) ? 'selected' : ''; ?>>5 ★ - Excellent</option>
                        <option value="4" <?php echo ($selected_rating == 4) ? 'selected' : ''; ?>>4 ★ - Very Good</option>
                        <option value="3" <?php echo ($selected_rating == 3) ? 'selected' : ''; ?>>3 ★ - Good</option>
                        <option value="2" <?php echo ($selected_rating == 2) ? 'selected' : ''; ?>>2 ★ - Fair</option>
                        <option value="1" <?php echo ($selected_rating == 1) ? 'selected' : ''; ?>>1 ★ - Poor</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="comment">Your Review</label>
                <textarea id="comment" name="comment" class="form-control" 
                          placeholder="How was your overall shopping experience? What did you like or what could be improved? Share your thoughts..." 
                          required><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : ''; ?></textarea>
            </div>
            
            <button type="submit" name="submit_review" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Submit Review
            </button>
        </form>

        <br>
        
        <!-- STATISZTIKÁK -->
        <?php if($stats['total_reviews'] > 0): ?>
        <div class="stats">
            <div class="stat-item">
                <h3><?php echo $stats['total_reviews']; ?></h3>
                <p>Total Reviews</p>
            </div>
            <div class="stat-item">
                <h3><?php echo $stats['avg_rating']; ?>/5</h3>
                <p>Average Rating</p>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- VÉLEMÉNYEK MEGJELENÍTÉSE -->
        <div class="reviews-section">
            <h3 class="reviews-title">Customer Reviews</h3>
            
            <?php if(count($reviews) > 0): ?>
                <?php foreach($reviews as $row): ?>
                <div class='review-card'>
                    <div class='review-header'>
                        <div class='reviewer-name'><?php echo htmlspecialchars($row['user_name']); ?></div>
                        <div class='review-date'><?php echo date('F j, Y', strtotime($row['created_at'])); ?></div>
                    </div>
                    <div class='review-stars'><?php echo str_repeat("★", $row['rating']) . str_repeat("☆", 5 - $row['rating']); ?></div>
                    <div class='review-comment'><?php echo nl2br(htmlspecialchars($row['comment'])); ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class='no-reviews'>No reviews yet. Be the first to share your shopping experience!</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        const ratingSelect = document.getElementById('ratingSelect');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingSelect.value = value;
                
                stars.forEach(s => {
                    const icon = s.querySelector('i');
                    const sValue = s.getAttribute('data-value');
                    
                    if (sValue <= value) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        s.classList.add('active');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        s.classList.remove('active');
                    }
                });
            });
        });
        
        const reviewForm = document.getElementById('reviewForm');
        reviewForm.addEventListener('submit', function() {
            setTimeout(() => {
                alert('Thank you for your feedback! Your review has been submitted.');
            }, 100);
        });
        
        if(document.querySelector('.review-card')) {
            const loadMoreBtn = document.createElement('button');
            loadMoreBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load More Reviews';
            loadMoreBtn.style.cssText = 'background: linear-gradient(to right, #667eea, #1d9cd3ff); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; margin: 20px auto; display: block;';
            loadMoreBtn.addEventListener('click', function() {
                alert('There are not enough reviews to show more.');
            });
            
            document.querySelector('.reviews-section').appendChild(loadMoreBtn);
        }
    });
</script>
</body>
</html>
