<?php
require_once '../../config/db.php';

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
  <style>
    .card {
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .table-hover tbody tr:hover {
      background-color: #f1f1f1;
    }
    .menu-image {
      width: 80px;
      height: auto;
      border-radius: 5px;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 ms-sm-auto col-lg-2 px-8">
        <?php require_once "../layouts/sidebar.php"; ?>
      </div>
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
              <tr>
                <td>
                  <img src="/views/staff/menu-image.php?id=<?= htmlspecialchars($item['id']) ?>"
                       alt="<?= htmlspecialchars($item['name']) ?>"
                       class="menu-image">
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
                  <a href="delete_menu_item.php?id=<?= htmlspecialchars($item['id']) ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Are you sure you want to delete this item?');">
                    <i class="fas fa-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
