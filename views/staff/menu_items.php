<?php
// Require the shared database configuration instead of duplicating connection code
require_once '../../config/db.php';

$sql = "SELECT mi.id, mi.name, mi.availability, mi.description, mi.price, mi.image, mc.name AS category_name
        FROM MenuItems mi
        JOIN MenuCategories mc ON mi.category_id = mc.id";

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
    .list-group-item {
      transition: background-color 0.2s;
    }
    .list-group-item:hover {
      background-color: #f8f9fa;
    }
    .alert {
      margin-bottom: 1rem;
      padding: 1rem;
      border-radius: 0.5rem;
    }
    .alert-danger {
      background-color: #f8d7da;
      border-color: #f5c6cb;
      color: #721c24;
    }
    .alert-warning {
      background-color: #fff3cd;
      border-color: #ffeeba;
      color: #856404;
    }
    .alert-success {
      background-color: #d4edda;
      border-color: #c3e6cb;
      color: #155724;
    }
    .sidebar {
      height: 100vh;
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #343a40;
      padding-top: 20px;
    }
    .sidebar a {
      padding: 10px 15px;
      display: block;
      color: white;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .content {
      margin-left: 250px;
      padding: 20px;
    }
    .menu-image {
      width: 80px;
      height: auto;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 ms-sm-auto col-lg-2 px-8">
        <?php require_once "../layouts/sidebar.php"; ?>
      </div>
      <div class="col-md-9 ms-sm-auto col-lg-10 px-4">
        <h2 class="text-center my-4">Menu Items List</h2>
        <table class="table table-bordered">
          <thead>
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
                  <!-- Reference menu-image.php to retrieve the image for this menu item -->
                  <img src="/views/staff/menu-image.php?id=<?= htmlspecialchars($item['id']) ?>"
                       alt="<?= htmlspecialchars($item['name']) ?>"
                       class="menu-image">
                </td>
                <td><?= htmlspecialchars($item['id']) ?></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['availability']) ?></td>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td><?= htmlspecialchars($item['price']) ?></td>
                <td><?= htmlspecialchars($item['category_name']) ?></td>
                <td>
                  <a href="edit_menu_item.php?id=<?= htmlspecialchars($item['id']) ?>" class="btn btn-warning">Edit</a>
                  <a href="delete_menu_item.php?id=<?= htmlspecialchars($item['id']) ?>"
                     class="btn btn-danger"
                     onclick="return confirm('Are you sure you want to delete this item?');">
                    Delete
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
