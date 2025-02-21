<?php
session_start();
require_once __DIR__ . '/../controllers/InventoryController.php';

// Set response header to JSON
header('Content-Type: application/json');

// Authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$inventoryController = new InventoryController();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Log the incoming POST data for debugging
        error_log(print_r($_POST, true));

        if (isset($_POST['add_item'])) {
            // Validate input data
            $itemName = htmlspecialchars($_POST['item_name']);
            $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
            $reorderThreshold = filter_var($_POST['reorder_threshold'], FILTER_VALIDATE_INT);

            if (!$itemName || !$quantity || !$reorderThreshold) {
                throw new Exception('Invalid input data');
            }

            // Add item
            $success = $inventoryController->addItem($itemName, $quantity, $reorderThreshold);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Item added successfully']);
            } else {
                throw new Exception('Failed to add item');
            }
        } elseif (isset($_POST['delete_item'])) {
            // Validate item ID
            $itemId = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);

            if (!$itemId) {
                throw new Exception('Invalid item ID');
            }

            // Delete item
            $success = $inventoryController->deleteItem($itemId);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
            } else {
                throw new Exception('Failed to delete item');
            }
        } else {
            throw new Exception('Invalid action: No valid action detected in POST data');
        }
    } else {
        throw new Exception('Invalid request method: Only POST requests are allowed');
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}