<?php
require_once __DIR__ . '/../config/db.php';
// Function to get unread notifications count
function getUnreadNotificationCount($userId, $pdo) {
    $query = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND status = 'unread'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count;
}

// Function to mark notifications as read
function markNotificationAsRead($notificationId, $pdo) {
    $query = "UPDATE notifications SET status = 'read' WHERE id = :notification_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':notification_id', $notificationId);
    $stmt->execute();
}

?>
