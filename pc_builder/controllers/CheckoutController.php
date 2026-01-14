<?php
class CheckoutController {
    private $configModel;
    
    public function __construct() {
        $this->configModel = new Configuration();
    }
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionId = session_id();
        $items = $this->configModel->getItems($sessionId);
        
        if (empty($items)) {
            header("Location: /Szak/pc_builder/");
            exit();
        }

        require_once dirname(__DIR__) . '/views/checkout.php';
    }
}
?>