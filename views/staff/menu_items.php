<?php
session_start();
require_once '../../config/db.php';

// Authorization check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

// Fetch menu items
$sql = "SELECT mi.id, mi.name, mi.availability, mi.description, mi.price, mi.image, mc.name AS category_name, mi.category_id
        FROM MenuItems mi
        JOIN MenuCategories mc ON mi.category_id = mc.id
        ORDER BY mc.name, mi.name";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php require_once "../../public/css/dashboard.css"; ?>
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include "../layouts/sidebar.php"; ?>
            <div class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <h2 class="text-center my-4">Menu Items List</h2>
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Item ID</th>
                            <th>Name</th>
                            <th>Availability</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menuItems as $item): ?>
                            <tr data-item-id="<?= htmlspecialchars($item['id']) ?>">
                                <td>
                                    <img src="/views/staff/menu-image.php?id=<?= htmlspecialchars($item['id']) ?>"
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         class="menu-image" style="width: 100px; height: 100px;">
                                </td>
                                <td><?= htmlspecialchars($item['id']) ?></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['availability']) ?></td>
                                <td><?= htmlspecialchars($item['description']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= htmlspecialchars($item['category_name']) ?></td>
                                <td>
                                    <a href="edit_menu_item.php?id=<?= htmlspecialchars($item['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button onclick="deleteMenuItem(<?= htmlspecialchars($item['id']) ?>)" 
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for AJAX Delete -->
    <script>
        function deleteMenuItem(itemId) {
            if (confirm('Are you sure you want to delete this item?')) {
                fetch(`delete_menu_item.php?id=${itemId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                        if (row) {
                            row.remove();
                        }
                        alert('Item deleted successfully!');
                    } else {
                        alert(data.error || 'Failed to delete item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the item');
                });
            }
        }
    </script>
</body>
</html>