<?php
class RateData {
    private $conn;
    
    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }
    
    public function getReviewTitle() {
        return "How was your shopping here?";
    }
    
    public function getPageTitle() {
        return "Aqua Mini Shop - Shopping Experience Review";
    }
    
    public function saveReview($user_name, $rating, $comment) {
        $review_title = $this->getReviewTitle();
        $user_name = $this->conn->real_escape_string($user_name);
        $comment = $this->conn->real_escape_string($comment);
        
        $sql = "INSERT INTO reviews (product_name, user_name, rating, comment) 
                VALUES ('$review_title', '$user_name', $rating, '$comment')";
        
        return $this->conn->query($sql);
    }
    
    public function getAllReviews($limit = 20) {
        $review_title = $this->getReviewTitle();
        $sql = "SELECT * FROM reviews WHERE product_name='$review_title' ORDER BY created_at DESC LIMIT $limit";
        $result = $this->conn->query($sql);
        
        $reviews = [];
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
        }
        return $reviews;
    }
    
    public function getReviewsForHomepage($limit = 3) {
        return $this->getAllReviews($limit);
    }
    
    public function getReviewStats() {
        $review_title = $this->getReviewTitle();
        
        $stats = ['total_reviews' => 0, 'avg_rating' => '0.0'];
        
        $total_result = $this->conn->query("SELECT COUNT(*) as total FROM reviews WHERE product_name='$review_title'");
        if($total_result && $total_result->num_rows > 0) {
            $total_data = $total_result->fetch_assoc();
            $stats['total_reviews'] = $total_data['total'];
        }
        
        $avg_result = $this->conn->query("SELECT AVG(rating) as avg FROM reviews WHERE product_name='$review_title'");
        if($avg_result && $avg_result->num_rows > 0) {
            $avg_data = $avg_result->fetch_assoc();
            $stats['avg_rating'] = number_format($avg_data['avg'] ?? 0, 1);
        }
        
        return $stats;
    }
}
?>