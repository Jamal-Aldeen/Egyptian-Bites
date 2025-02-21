<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/MenuController.php';

header('Content-Type: application/json'); // Ensure JSON response

// Authorization check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
} elseif ($_SESSION['role'] !== 'Staff') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if the item ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No item ID provided']);
    exit();
}

$itemId = intval($_GET['id']);
$menuController = new MenuController();

try {
    // Attempt to delete the menu item
    if ($menuController->deleteMenuItem($itemId)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete item']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>