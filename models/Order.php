<?php
require_once __DIR__ . '/../config/db.php';

class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder($user_id, $total_price) {
        $stmt = $this->pdo->prepare("INSERT INTO Orders (user_id, total_price, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$user_id, $total_price]);
        return $this->pdo->lastInsertId();
    }

    public function addOrderItems($order_id, $items) {
        $stmt = $this->pdo->prepare("INSERT INTO OrderItems (order_id, menu_item_id, quantity, customizations) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$order_id, $item['menu_item_id'], $item['quantity'], $item['customizations'] ?? '']);
        }
    }

    public function getOrderHistory($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalSales() {
        $stmt = $this->pdo->query("SELECT SUM(total_price) AS total FROM Orders WHERE status = 'Delivered'");
        return $stmt->fetchColumn() ?? 0; // Return 0 if no sales
    }
    
    public function getActiveOrderCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM Orders WHERE status IN ('Pending', 'Preparing')");
        return $stmt->fetchColumn();
    }
    public function getSalesReport($startDate, $endDate) {
        $stmt = $this->pdo->prepare("
            SELECT DATE(created_at) AS date, COUNT(*) AS total_orders, SUM(total_price) AS revenue 
            FROM Orders 
            WHERE created_at BETWEEN ? AND ?
            GROUP BY DATE(created_at)
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalRevenue() {
        $stmt = $this->pdo->query("SELECT SUM(total_price) AS total FROM Orders WHERE status = 'Delivered'");
        return $stmt->fetchColumn() ?? 0;
    }
}
?>
