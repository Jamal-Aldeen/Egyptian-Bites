<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /views/shared/login.php"); // Not logged in → login page
    exit();
} elseif ($_SESSION['role'] !== 'Staff') {
    header("Location: /index.php"); // Logged in but not staff → index
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
