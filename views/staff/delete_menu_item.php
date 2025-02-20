<?php
session_start();

// Check if the user is logged in and has the Staff role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/MenuController.php';

// Check if the item id is provided
if (!isset($_GET['id'])) {
    header("Location: menu_items.php?error=No item id provided");
    exit();
}

$itemId = intval($_GET['id']);
$menuController = new MenuController();

// Attempt to delete the menu item using the controller
if ($menuController->deleteMenuItem($itemId)) {
    header("Location: menu_items.php?success=Item deleted successfully");
    exit();
} else {
    header("Location: menu_items.php?error=Failed to delete item");
    exit();
}
?>
