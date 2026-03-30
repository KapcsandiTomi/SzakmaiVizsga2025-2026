<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once 'trackdata.php';

class TrackHandler {
    private $trackData;
    
    public function __construct() {
        global $conn;
        $this->trackData = new TrackData($conn);
    }

    public function checkAuthentication() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }
        return $_SESSION['email'] ?? '';
    }

    public function parseOrderData($orderDataJson, $totalPrice) {
        if (empty($orderDataJson) || trim($orderDataJson) === '') {
            return $this->getDefaultOrderData($totalPrice);
        }
        
        $data = json_decode($orderDataJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $cleanJson = trim($orderDataJson);
            $cleanJson = str_replace(["\r", "\n", "\t"], '', $cleanJson);
            $cleanJson = preg_replace('/\s+/', ' ', $cleanJson);
            
            if (strpos($cleanJson, "'") !== false) {
                $cleanJson = str_replace("'", '"', $cleanJson);
            }
            
            if (strpos($cleanJson, '[') === 0 && strpos($cleanJson, ']') !== false) {
                $cleanJson = '{"items": ' . $cleanJson . '}';
            }
            
            $data = json_decode($cleanJson, true);
        }
        
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            error_log("JSON decode error: " . json_last_error_msg() . " - Data: " . $orderDataJson);
            return $this->getDefaultOrderData($totalPrice);
        }
        
        if (isset($data['items']) && is_array($data['items'])) {
            return [
                'items' => $data['items'],
                'coupon' => $data['coupon'] ?? null,
                'discount' => $data['discount'] ?? 0,
                'shipping' => $data['shipping_cost'] ?? $data['shipping'] ?? 0,
                'subtotal' => $data['subtotal'] ?? $totalPrice
            ];
        } 
        elseif (is_array($data) && !empty($data)) {
            $firstItem = reset($data);
            if (isset($firstItem['name']) || isset($firstItem['product_name']) || isset($firstItem['title'])) {
                return [
                    'items' => $data,
                    'coupon' => null,
                    'discount' => 0,
                    'shipping' => 0,
                    'subtotal' => $totalPrice
                ];
            }
        }
        
        return $this->getDefaultOrderData($totalPrice);
    }

    private function getDefaultOrderData($totalPrice) {
        return [
            'items' => [],
            'coupon' => null,
            'discount' => 0,
            'shipping' => 0,
            'subtotal' => $totalPrice
        ];
    }

    public function displayProduct($item) {
        $image = $this->getProductImage($item);
        $productName = $this->getProductName($item);
        $quantity = $this->getProductQuantity($item);
        $price = $this->getProductPrice($item);
        $total = $price * $quantity;
        
        $html = '<div class="track-product-item">';
        
        if (!empty($image)) {
            $html .= '<img src="' . htmlspecialchars($image) . '" alt="Product" class="track-product-image" onerror="this.onerror=null; this.src=\'../letoles.jpg\';">';
        } else {
            $defaultImage = '../letoles.jpg';
            if (file_exists($defaultImage)) {
                $html .= '<img src="' . $defaultImage . '" alt="Default Product" class="track-product-image">';
            } else {
                $html .= '<div class="track-product-image-placeholder">
                            <i class="fas fa-image"></i>
                          </div>';
            }
        }
        
        $html .= '<div class="track-product-details">';
        $html .= '<span class="track-product-name">' . htmlspecialchars($productName) . '</span>';
        
        if (isset($item['size']) || isset($item['color']) || isset($item['variant'])) {
            $html .= '<div class="track-product-info">';
            if (isset($item['size'])) {
                $html .= '<span class="me-2">Size: ' . htmlspecialchars($item['size']) . '</span>';
            }
            if (isset($item['color'])) {
                $html .= '<span class="me-2">Color: ' . htmlspecialchars($item['color']) . '</span>';
            }
            if (isset($item['variant'])) {
                $html .= '<span>Variant: ' . htmlspecialchars($item['variant']) . '</span>';
            }
            $html .= '</div>';
        }
        
        $html .= '<div class="track-product-meta">';
        $html .= '<span class="track-product-price">$' . number_format($price, 2) . ' each</span>';
        $html .= '<span class="track-product-quantity">× ' . $quantity . '</span>';
        $html .= '</div>';
        $html .= '<span class="track-product-total">$' . number_format($total, 2) . ' total</span>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function getProductImage($item) {
        if (isset($item['image']) && !empty($item['image'])) {
            $image = $item['image'];
        } elseif (isset($item['product_image']) && !empty($item['product_image'])) {
            $image = $item['product_image'];
        } elseif (isset($item['img']) && !empty($item['img'])) {
            $image = $item['img'];
        } else {
            return '';
        }
        
        if (strpos($image, '../') !== 0 && strpos($image, 'http') !== 0 && strpos($image, 'https') !== 0) {
            $image = '../' . $image;
        }
        
        return $image;
    }

    private function getProductName($item) {
        if (isset($item['name']) && !empty($item['name'])) {
            return $item['name'];
        } elseif (isset($item['product_name']) && !empty($item['product_name'])) {
            return $item['product_name'];
        } elseif (isset($item['title']) && !empty($item['title'])) {
            return $item['title'];
        }
        
        return 'Product';
    }

    private function getProductQuantity($item) {
        if (isset($item['quantity'])) {
            return intval($item['quantity']);
        } elseif (isset($item['qty'])) {
            return intval($item['qty']);
        }
        
        return 1;
    }

    private function getProductPrice($item) {
        if (isset($item['price']) && is_numeric($item['price'])) {
            return floatval($item['price']);
        } elseif (isset($item['product_price']) && is_numeric($item['product_price'])) {
            return floatval($item['product_price']);
        } elseif (isset($item['unit_price']) && is_numeric($item['unit_price'])) {
            return floatval($item['unit_price']);
        }
        
        return 0;
    }

    public function getMaskedCardNumber($card_number) {
        if (empty($card_number)) {
            return 'N/A';
        }
        
        return 'XXXX-XXXX-XXXX-' . substr($card_number, -4);
    }

    public function handleRequest() {
        $email = $this->checkAuthentication();
        $error = '';
        $trackingData = null;
        $orderInfo = null;
        
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $order_id = intval($_GET['id']);
            $trackingData = $this->trackData->getOrderTrackingData($order_id, $email);
            
            if ($trackingData) {
                $orderInfo = $this->parseOrderData(
                    $trackingData['order']['order_data'] ?? '',
                    $trackingData['order']['total_price']
                );
            } else {
                $error = "Order not found or you don't have permission to view this order!";
            }
        } else {
            $error = "No order ID specified!";
        }
        
        return [
            'error' => $error,
            'trackingData' => $trackingData,
            'orderInfo' => $orderInfo,
            'maskedCard' => $this->getMaskedCardNumber($trackingData['order']['card_number'] ?? '')
        ];
    }
}

$handler = new TrackHandler();
$data = $handler->handleRequest();
?>