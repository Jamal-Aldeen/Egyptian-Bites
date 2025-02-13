<?php
require_once __DIR__ . '/../config/db.php';

class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($user_id, $total_price) {
        $stmt = $this->conn->prepare("INSERT INTO Orders (user_id, total_price, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("id", $user_id, $total_price);
        if ($stmt->execute()) {
            return $this->conn->insert_id; 
        }
        return false;
    }
    public function addOrderItems($order_id, $items) {
        $stmt = $this->conn->prepare("INSERT INTO OrderItems (order_id, menu_item_id, quantity, customizations) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->bind_param("iiis", $order_id, $item['menu_item_id'], $item['quantity'], $item['customizations']);
            $stmt->execute();
        }
        return true;
    }
    public function getOrderHistory($user_id) {
        $query = "SELECT * FROM Orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function updateOrderStatus($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE Orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }
    
}
?>
