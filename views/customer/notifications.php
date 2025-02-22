<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Fetch notifications from the database for the logged-in user
$user_id = $_SESSION['user_id'];  // Assuming the user is logged in and their user_id is stored in session
$notifications_query = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($notifications_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center">Your Notifications</h2>

        <?php if (count($notifications) > 0): ?>
            <ul class="list-group">
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item">
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
