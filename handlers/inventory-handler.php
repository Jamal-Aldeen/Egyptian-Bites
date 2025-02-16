<?php
session_start();
require_once __DIR__ . '/../controllers/InventoryController.php';

// Authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

$inventoryController = new InventoryController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        $itemName = htmlspecialchars($_POST['item_name']);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
        $reorderThreshold = filter_var($_POST['reorder_threshold'], FILTER_VALIDATE_INT);

        if ($itemName && $quantity !== false && $reorderThreshold !== false) {
            $inventoryController->addItem($itemName, $quantity, $reorderThreshold);
        }
    } elseif (isset($_POST['delete_item'])) {
        $itemId = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);
        if ($itemId) {
            $inventoryController->deleteItem($itemId);
        }
    }
}

header("Location: /views/staff/inventory-management.php");
exit();
?>