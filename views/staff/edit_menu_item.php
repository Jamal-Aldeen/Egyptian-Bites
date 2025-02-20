<?php
require_once '../../config/db.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Fetch the item details including current image
    $sql = "SELECT id, name, description, price, image FROM MenuItems WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

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

    // Initialize a variable for the new image filename.
    $newImage = $item['image']; // default to current image

    // Check if a new image has been uploaded
    if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
        $menuImage = $_FILES['menu_image'];
        $imageFileType = strtolower(pathinfo($menuImage['name'], PATHINFO_EXTENSION));

        // Allowed file extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedExtensions)) {
            die("Invalid file type. Allowed types: " . implode(", ", $allowedExtensions));
        }

        // Validate file size (limit to 2MB)
        if ($menuImage['size'] > 2 * 1024 * 1024) {
            die("Menu image must be less than 2MB.");
        }

        // Define target directory - adjust relative path as needed
        $targetDir = __DIR__ . '/../../public/uploads/menu-image/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate a unique filename for the uploaded image
        $newImage = time() . "_" . bin2hex(random_bytes(5)) . "." . $imageFileType;
        $targetFile = $targetDir . $newImage;

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($menuImage['tmp_name'], $targetFile)) {
            die("Failed to upload new image.");
        }
    }

    // Update the item in the database
    // If a new image was uploaded, update the image field as well.
    $sql = "UPDATE MenuItems SET name = :name, description = :description, price = :price, image = :image WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':image', $newImage);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
         <div class="col-md-9 ms-sm-auto col-lg-10 px-4">
         <h2>Edit Menu Item</h2>
         <!-- Display current image if available -->
         <?php if (!empty($item['image'])): ?>
             <div class="mb-3">
                 <img src="/views/staff/menu-image.php?id=<?= htmlspecialchars($item['id']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="max-width: 200px;">
             </div>
         <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($item['price']) ?>" required>
            </div>
            <div class="form-group">
                <label for="menu_image">Update Menu Image (Optional)</label>
                <input type="file" class="form-control-file" id="menu_image" name="menu_image">
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
