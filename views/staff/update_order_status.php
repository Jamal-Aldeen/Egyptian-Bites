<?php
session_start();
require_once '../../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status']) && !empty($_POST['status']) && !empty($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $query = "UPDATE Orders SET status = :status WHERE id = :order_id";
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_id', $order_id);

    if ($stmt->execute()) {
        header("Location: /views/staff/order-management.php");
        exit();
    } else {
        echo "Error occurred while updating the status.";
    }
} else {
    echo "Invalid or missing data.";
}
?>
