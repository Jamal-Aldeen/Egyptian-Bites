<?php

// Include the database connection and notification helper file
require_once __DIR__ . '/../config/db.php';
include_once('../controllers/Notification.php'); // Ensure the path is correct

// Start session
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];  // Get the user ID from the session

    // Get unread notification count
    $notificationCount = getUnreadNotificationCount($userId, $pdo);

    // Return the count as a response
    echo $notificationCount;
} else {
    echo 0; // Return 0 if the user is not logged in
}
?>

