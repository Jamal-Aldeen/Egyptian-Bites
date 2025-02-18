<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../config/db.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        global $pdo;
        $this->orderModel = new Order($pdo);
    }

    public function placeOrder($user_id, $items, $total_price) {
        try {
            if (!is_numeric($user_id) || $user_id <= 0) {
                throw new InvalidArgumentException('Invalid user ID');
            }
            
            if (!is_array($items) || empty($items)) {
                throw new InvalidArgumentException('Invalid cart items');
            }
            
            if (!is_numeric($total_price) || $total_price <= 0) {
                throw new InvalidArgumentException('Invalid total price');
            }
    
            $order_id = $this->orderModel->createOrder($user_id, $total_price, $items);  
            return true;
        } catch (PDOException $e) {
            error_log("Order Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getOrderHistory($user_id) {
        return $this->orderModel->getOrderHistory($user_id);
    }
}
?>
