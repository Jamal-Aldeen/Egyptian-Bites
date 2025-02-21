<?php
session_start();
require_once __DIR__ . '/../../models/Inventory.php';
require_once '../../config/db.php';

// Authorization check
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/shared/login.php"); // Not logged in → login page
    exit();
} elseif ($_SESSION['role'] !== 'Staff') {
    header("Location: /index.php"); // Logged in but not staff → index
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
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">
            <?php include '../layouts/sidebar.php'; ?>
        </div>
        <div class="row card shadow col-md-6 ms-sm-auto col-lg-10 px-4">
            <div class="card-header bg-dark text-white">
                <h3 class="mb-0">Inventory Management</h3>
            </div>
            <div class="card-body">
                <!-- Add Item Form -->
                <form id="add-item-form" class="mb-4">
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
                        <tbody id="inventory-table-body">
                            <?php foreach ($items as $item): ?>
                            <tr id="item-<?= $item['id'] ?>">
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= htmlspecialchars($item['reorder_threshold']) ?></td>
                                <td>
                                <form class="delete-item-form">
    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
    <button type="submit" name="delete_item" class="btn btn-danger btn-sm">
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
    <script>
       document.addEventListener('DOMContentLoaded', function() {
    // Handle adding new items
    document.getElementById('add-item-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        // Add the add_item action to the form data
        formData.append('add_item', '1');

        // Show loading state
        const submitButton = form.querySelector('[name="add_item"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        submitButton.disabled = true;

        fetch('../../handlers/inventory-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show the new item
                location.reload();
            } else {
                alert(data.error || 'Failed to add item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the item');
        })
        .finally(() => {
            // Restore original button state
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });

    // Handle deleting items
    document.querySelectorAll('.delete-item-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (confirm('Are you sure you want to delete this item?')) {
                const formData = new FormData(form);

                // Add the delete_item action to the form data
                formData.append('delete_item', '1');

                // Show loading state
                const submitButton = form.querySelector('[name="delete_item"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                submitButton.disabled = true;

                fetch('../../handlers/inventory-handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        const itemId = formData.get('item_id');
                        document.getElementById(`item-${itemId}`).remove();
                    } else {
                        alert(data.error || 'Failed to delete item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the item');
                })
                .finally(() => {
                    // Restore original button state
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
            }
        });
    });
});
    </script>
</body>
</html>