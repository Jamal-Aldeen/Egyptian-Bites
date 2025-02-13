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
}
?>
