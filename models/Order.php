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
            foreach ($items as $item) {
                $checkStmt = $this->pdo->prepare("SELECT id FROM MenuItems WHERE id = ?");
                $checkStmt->execute([$item['id']]);
                if (!$checkStmt->fetch()) {
                    throw new Exception("  item not exist: #" . $item['id']);
                }
            } 

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
        $stmt = $this->pdo->prepare("INSERT INTO OrderItems (order_id, menu_item_id, quantity) VALUES (?, ?, ?)");
        
        foreach ($items as $item) {

            if (isset($item['id'], $item['quantity'])) {
                $stmt->execute([$order_id, $item['id'], $item['quantity']]);
            }
        }
    }
    
    public function addMenuItem($category_id, $name, $description, $price, $image, $availability) {
        $stmt = $this->pdo->prepare("INSERT INTO MenuItems (category_id, name, description, price, image, availability) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $name, $description, $price, $image, $availability]);
    }
    

    public function getOrderHistory($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateOrderStatus($order_id, $status) {
        $stmt = $this->pdo->prepare("UPDATE Orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        return true;
    }
}
?>