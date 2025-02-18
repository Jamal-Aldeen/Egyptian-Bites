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

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch the item details
    $sql = "SELECT id, name, description, price FROM menuitems WHERE id = :id";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $item = $stmt->fetch();

    if (!$item) {
        die("Item not found.");
    }
} else {
    die("ID not provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Update the item in the database
    $sql = "UPDATE menuitems SET name = :name, description = :description, price = :price WHERE id = :id";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Redirect to menu items list after update
    header("Location: menu_items.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
    <?php
         require_once "../layouts/sidebar.php";
         ?>
         <div class="col-md-9 ms-sm-auto col-lg-10 px-4">
         <h2>Edit Menu Item</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
            <a href="menu_items.php" class="btn btn-secondary">Cancel</a>
        </form>
             </div>
     </div>
       
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 