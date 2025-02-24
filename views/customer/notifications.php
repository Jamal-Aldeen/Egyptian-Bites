<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/shared/login.php");
    exit();
}

// Fetch notifications from the database for the logged-in user
$user_id = $_SESSION['user_id'];  
$notifications_query = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($notifications_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark notifications as read
$update_query = "UPDATE notifications SET status = 'read' WHERE user_id = :user_id AND status = 'unread'";
$stmt_update = $pdo->prepare($update_query);
$stmt_update->bindParam(':user_id', $user_id);
$stmt_update->execute();

$error = $success = null; // Define variables for error and success messages if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center">Your Notifications</h2>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (count($notifications) > 0): ?>
            <ul class="list-group">
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item <?= $notification['status'] === 'unread' ? 'list-group-item-warning' : '' ?>">
                        <strong><?= htmlspecialchars($notification['title']) ?></strong>
                        <p><?= htmlspecialchars($notification['message']) ?></p>
                        <small>Received on: <?= htmlspecialchars($notification['created_at']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center">You have no notifications at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
