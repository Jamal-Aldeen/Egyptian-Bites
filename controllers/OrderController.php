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
        $order_id = $this->orderModel->createOrder($user_id, $total_price);
        if ($order_id) {
            $this->orderModel->addOrderItems($order_id, $items);
            return true;
        }
        return false;
    }

    public function getOrderHistory($user_id) {
        return $this->orderModel->getOrderHistory($user_id);
    }
}
?>
