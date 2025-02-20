<?php
session_start();
require_once __DIR__ . '/../../models/Inventory.php';
require_once '../../config/db.php';



if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

$inventoryModel = new Inventory();
$items = $inventoryModel->getAllItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
<?php
    require_once "../../public/css/dashboard.css";
    ?>
</style>
</head>
<body class=" bg-light">
    
    <div class="container-fluid mt-4 ">
        <div class="row">
        <?php include '../layouts/sidebar.php'; ?>

        </div>
        <div class=" row card shadow col-md-6 ms-sm-auto col-lg-10 px-4">
            <div class="card-header bg-dark text-white">
                <h3 class="mb-0">Inventory Management</h3>
            </div>
            <div class="card-body">
                <!-- Add Item Form -->
                <form action="../../handlers/inventory-handler.php" method="POST" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="item_name" class="form-control" placeholder="Item Name" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="reorder_threshold" class="form-control" placeholder="Reorder Threshold">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_item" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Inventory Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Reorder Threshold</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= htmlspecialchars($item['reorder_threshold']) ?></td>
                                <td>
                                    <form action="../../handlers/inventory-handler.php" method="POST" class="d-inline">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="delete_item" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this item?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>