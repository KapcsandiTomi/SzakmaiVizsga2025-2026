<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/ratedata.php';

class RateHandler {
    private $model;
    private $errors = [];
    
    public function __construct() {
        global $conn;
        $this->model = new RateData($conn);
    }
    
    public function handleRequest() {
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
            $this->processReview($_POST);
        }
    }
    
    private function processReview($post_data) {
        $user_name = trim($_SESSION['name'] ?? '');
        $rating = isset($post_data['rating']) ? (int)$post_data['rating'] : 0;
        $comment = trim($post_data['comment'] ?? '');
        
        if(empty($user_name)) {
            $this->errors[] = "Please log in to submit a review.";
        }
        
        if($rating < 1 || $rating > 5) {
            $this->errors[] = "Please select a valid rating.";
        }
        
        if(empty($comment)) {
            $this->errors[] = "Please write your review.";
        }
        
        if(empty($this->errors)) {
            if($this->model->saveReview($user_name, $rating, $comment)) {
                $_SESSION['success_message'] = "Thank you! Your review has been submitted successfully.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                global $conn;
                $this->errors[] = "Error saving review: " . $conn->error;
            }
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getModel() {
        return $this->model;
    }
}
?>