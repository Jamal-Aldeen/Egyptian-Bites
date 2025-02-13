<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../config/db.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order($GLOBALS['conn']);
    }

    public function placeOrder($user_id, $items, $total_price) {
        $order_id = $this->orderModel->createOrder($user_id, $total_price);
        if ($order_id) {
            $this->orderModel->addOrderItems($order_id, $items);
            return ["status" => "success", "order_id" => $order_id];
        }
        return ["status" => "error"];
    }
    
    public function getOrderHistory($user_id) {
        return $this->orderModel->getOrderHistory($user_id);
    }
    
}
?>
