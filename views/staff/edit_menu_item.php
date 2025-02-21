<?php
session_start();
require_once '../../config/db.php';

// Authorization check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("HTTP/1.1 403 Forbidden");
    exit("Unauthorized access");
}

// Fetch item details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, name, description, price, image FROM MenuItems WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo json_encode(['error' => 'Item not found']);
        exit();
    }
} else {
    echo json_encode(['error' => 'ID not provided']);
    exit();
}

// Handle form submission via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
        $price = floatval($_POST['price']);
        $newImage = $item['image']; // Default to current image

        // Handle file upload
        if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
            $menuImage = $_FILES['menu_image'];
            $imageFileType = strtolower(pathinfo($menuImage['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($imageFileType, $allowedExtensions)) {
                throw new Exception("Invalid file type. Allowed types: " . implode(", ", $allowedExtensions));
            }

            if ($menuImage['size'] > 2 * 1024 * 1024) {
                throw new Exception("Menu image must be less than 2MB.");
            }

            $targetDir = __DIR__ . '/../../public/uploads/menu-image/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $newImage = time() . "_" . bin2hex(random_bytes(5)) . "." . $imageFileType;
            $targetFile = $targetDir . $newImage;

            if (!move_uploaded_file($menuImage['tmp_name'], $targetFile)) {
                throw new Exception("Failed to upload new image.");
            }
        }

        // Update the item in the database
        $sql = "UPDATE MenuItems SET name = :name, description = :description, price = :price, image = :image WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image', $newImage);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'newImage' => $newImage !== $item['image'],
            'itemId' => $id
        ]);
        exit();
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        <?php include "../../public/css/dashboard.css"; ?>
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include "../layouts/sidebar.php"; ?>
            <div class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <h2>Edit Menu Item</h2>
                <?php if (!empty($item['image'])): ?>
                    <div class="mb-3">
                        <img src="/views/staff/menu-image.php?id=<?= htmlspecialchars($item['id']) ?>" 
                             alt="<?= htmlspecialchars($item['name']) ?>" 
                             style="max-width: 200px;" class="menu-image">
                    </div>
                <?php endif; ?>
                <form id="edit-item-form" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($item['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($item['description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" 
                               value="<?= htmlspecialchars($item['price']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="menu_image">Update Menu Image (Optional)</label>
                        <input type="file" class="form-control-file" id="menu_image" name="menu_image">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                    <div id="form-message" class="mt-2"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('edit-item-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('update_item', '1');

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Updating...';

            fetch(`edit_menu_item.php?id=<?= $item['id'] ?>`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.newImage) {
                        const imgElement = document.querySelector('.menu-image');
                        if (imgElement) {
                            imgElement.src = `/views/staff/menu-image.php?id=${data.itemId}&t=${new Date().getTime()}`;
                        }
                    }
                    document.getElementById('form-message').innerHTML = 
                        '<div class="alert alert-success">Item updated successfully!</div>';
                } else {
                    document.getElementById('form-message').innerHTML = 
                        `<div class="alert alert-danger">${data.error}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('form-message').innerHTML = 
                    '<div class="alert alert-danger">An error occurred</div>';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Update Item';
            });
        });
    </script>
</body>
</html>