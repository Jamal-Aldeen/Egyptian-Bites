<?php
require_once __DIR__ . '/../models/Inventory.php';

class InventoryController {
    private $inventoryModel;

    public function __construct() {
        $this->inventoryModel = new Inventory();
    }

    // Get all inventory items
    public function getAllItems() {
        return $this->inventoryModel->getAllItems();
    }

    // Add a new inventory item
    public function addItem($itemName, $quantity, $reorderThreshold) {
        return $this->inventoryModel->addItem($itemName, $quantity, $reorderThreshold);
    }

    // Delete an inventory item
    public function deleteItem($itemId) {
        return $this->inventoryModel->deleteItem($itemId);
    }
}
?>