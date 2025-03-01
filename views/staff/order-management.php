<?php
session_start();
require_once '../../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

$query = "SELECT * FROM Orders ORDER BY created_at DESC"; 
$stmt = $GLOBALS['pdo']->prepare($query);  
$stmt->execute();  
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php require_once "../../public/css/dashboard.css"; ?>
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">
            <?php require_once "../layouts/sidebar.php"; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <h1 class="text-center mb-4 text-dark">Order Management</h1>
                
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Order Details</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($orders as $order) {

                            $order_items = json_decode($order['items'], true);  
                            echo "<tr>";
                            echo "<td>" . $order['id'] . "</td>";  
                            echo "<td>" . $order['user_id'] . "</td>";
                            echo "<td>";
                            foreach ($order_items as $item) {
                                echo $item['name'] . " (x" . $item['quantity'] . ")<br>";
                            }
                            echo "</td>";
                            echo "<td>" . $order['created_at'] . "</td>";
                            echo "<td>" . $order['status'] . "</td>";

                            echo '<td>
                            <form method="POST" action="/views/staff/update_order_status.php">
                                <div class="form-group">
                                    <label for="status">Order Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="Pending" '.($order['status'] == 'Pending' ? 'selected' : '').'>Pending</option>
                                        <option value="Preparing" '.($order['status'] == 'Preparing' ? 'selected' : '').'>Preparing</option>
                                        <option value="Ready" '.($order['status'] == 'Ready' ? 'selected' : '').'>Ready</option>
                                        <option value="Delivered" '.($order['status'] == 'Delivered' ? 'selected' : '').'>Delivered</option>
                                    </select>
                                </div>
                            
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status" class="form-control" required>
                                        <option value="Pending" '.($order['payment_status'] == 'Pending' ? 'selected' : '').'>Pending</option>
                                        <option value="Completed" '.($order['payment_status'] == 'Completed' ? 'selected' : '').'>Completed</option>
                                        <option value="Failed" '.($order['payment_status'] == 'Failed' ? 'selected' : '').'>Failed</option>
                                    </select>
                                </div>
                            
                                <input type="hidden" name="order_id" value="'.htmlspecialchars($order['id']).'">
                            
                                <button type="submit" class="btn btn-primary mt-2">Update</button>
                            </form>
                            </td>';
                            // echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
