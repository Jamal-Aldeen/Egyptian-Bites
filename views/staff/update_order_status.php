<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input validation
    $required_fields = ['order_id', 'status', 'payment_status'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die("Missing required field: $field");
        }
    }

    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];

    // Validate status values
    $valid_statuses = ['Pending', 'Preparing', 'Ready', 'Delivered'];
    $valid_payment_statuses = ['Pending', 'Completed', 'Failed'];

    if (!in_array($status, $valid_statuses)) {
        die("Invalid order status");
    }

    if (!in_array($payment_status, $valid_payment_statuses)) {
        die("Invalid payment status");
    }

    try {
        $query = "UPDATE Orders 
                 SET status = :status, 
                     payment_status = :payment_status 
                 WHERE id = :order_id";

        $stmt = $GLOBALS['pdo']->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':payment_status', $payment_status, PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Add notification for successful update
            $_SESSION['success'] = "Order #$order_id updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating order status";
        }

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error'] = "Database error occurred";
    }

    header("Location: /views/staff/order-management.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: /views/staff/order-management.php");
    exit();
}
?>

<!-- HTML Form -->
<form method="POST" action="/views/staff/update_order_status.php">
    <div class="form-group">
        <label for="status">Order Status</label>
        <select name="status" class="form-control" required>
            <option value="Pending">Pending</option>
            <option value="Preparing">Preparing</option>
            <option value="Ready">Ready</option>
            <option value="Delivered">Delivered</option>
        </select>
    </div>

    <!-- Payment Status -->
    <div class="form-group">
        <label for="payment_status">Payment Status</label>
        <select name="payment_status" class="form-control" required>
            <option value="Pending">Pending</option>
            <option value="Completed">Completed</option>
            <option value="Failed">Failed</option>
        </select>
    </div>

    <!-- Hidden order ID -->
    <input type="hidden" name="order_id" value="<?= isset($order['id']) ? $order['id'] : '' ?>">

    <button type="submit" class="btn btn-primary mt-2">Update</button>
</form>
