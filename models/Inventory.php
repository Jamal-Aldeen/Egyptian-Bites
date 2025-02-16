<?php
require_once __DIR__ . '/../config/db.php';

class Inventory {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Fetch all inventory items
    public function getAllItems() {
        $stmt = $this->pdo->query("SELECT * FROM Inventory");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new inventory item
    public function addItem($itemName, $quantity, $reorderThreshold) {
        $stmt = $this->pdo->prepare("INSERT INTO Inventory (item_name, quantity, reorder_threshold) VALUES (?, ?, ?)");
        return $stmt->execute([$itemName, $quantity, $reorderThreshold]);
    }

    // Delete an inventory item
    public function deleteItem($itemId) {
        $stmt = $this->pdo->prepare("DELETE FROM Inventory WHERE id = ?");
        return $stmt->execute([$itemId]);
    }
    public function getLowStockItems() {
        $stmt = $this->pdo->query("
            SELECT item_name, quantity, reorder_threshold 
            FROM Inventory 
            WHERE quantity <= reorder_threshold
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>