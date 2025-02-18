<?php
require_once __DIR__ . '/../config/db.php';

class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder($user_id, $total_price, $items) {
        $this->pdo->beginTransaction();
        try {

            $stmt = $this->pdo->prepare(
                "INSERT INTO Orders (user_id, total_price, status, created_at) 
                VALUES (?, ?, 'Pending', NOW())"
            );
            

            $stmt->execute([$user_id, $total_price]);
            
            $order_id = $this->pdo->lastInsertId();
            
            if (!empty($items)) {
                $this->addOrderItems($order_id, $items);
            }
            
            $this->pdo->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Order creation failed: " . $e->getMessage());
        }
    }

    public function addOrderItems($order_id, $items) {
        $stmt = $this->pdo->prepare("INSERT INTO OrderItems (order_id, menu_item_id, quantity, customizations) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['customizations'] ?? '']);
        }
    }

    public function getOrderHistory($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>