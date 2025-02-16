<?php
require_once __DIR__ . '/../models/Order.php';

class SalesController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order($GLOBALS['pdo']);
    }

    // Get sales report data
    public function getSalesReport($startDate, $endDate) {
        return $this->orderModel->getSalesReport($startDate, $endDate);
    }

    // Get total revenue
    public function getTotalRevenue() {
        return $this->orderModel->getTotalRevenue();
    }
}

?>