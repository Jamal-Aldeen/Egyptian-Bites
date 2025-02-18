<?php
define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'restaurant_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $GLOBALS['pdo'] = new PDO($dsn, DB_USER, DB_PASSWORD);
    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Query to fetch all menu items
$sql = "SELECT mi.id, mi.name, mi.availability, mi.description, mi.price, mc.name AS category_name 
        FROM menuitems mi 
        JOIN menucategories mc ON mi.category_id = mc.id";
$stmt = $GLOBALS['pdo']->prepare($sql);
$stmt->execute();
$menuItems = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 ms-sm-auto col-lg-2 px-8" >
                <?php require_once "../layouts/sidebar.php"; ?>
            </div>
            <div class="col-md-9 ms-sm-auto col-lg-10 px-4">
               
                    <h2 class="text-center">Menu Items List</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
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
                                <td><?php echo htmlspecialchars($item['id']); ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['availability']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td><?php echo htmlspecialchars($item['price']); ?></td>
                                <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                <td>
                                    <a href="edit_menu_item.php?id=<?php echo $item['id']; ?>" class="btn btn-warning">Edit</a>
                                    <a href="delete_menu_item.php?id=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
       
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 