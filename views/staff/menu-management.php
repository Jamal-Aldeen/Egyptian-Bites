<?php
require_once __DIR__ . '/../../controllers/MenuController.php';
$menuController = new MenuController();
$categories = $menuController->getCategories();
$menuItems = $menuController->getMenuItems();
$specialOffers = $menuController->getSpecialOffers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <h1>Menu Management</h1>

    <!-- Success/Error Message -->
    <?php if (isset($_GET['success'])): ?>
        <p class="success-message">Operation completed successfully!</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p class="error-message"><?= htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <!-- Add Category -->
    <h2>Manage Categories</h2>
    <form action="../../handlers/menu-handler.php" method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" id="category_name" placeholder="Category Name" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>

    <ul>
        <?php foreach ($categories as $category): ?>
            <li><?= htmlspecialchars($category['name']); ?>
                <form action="../../handlers/menu-handler.php" method="POST" style="display:inline;">
                    <input type="hidden" name="category_id" value="<?= $category['id']; ?>">
                    <button type="submit" name="delete_category">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Add Menu Item -->
    <h2>Manage Menu Items</h2>
    <form action="../../handlers/menu-handler.php" method="POST">
        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="menu_name">Menu Item Name:</label>
        <input type="text" name="menu_name" id="menu_name" placeholder="Menu Item Name" required>

        <label for="description">Description:</label>
        <input type="text" name="description" id="description" placeholder="Description" required>

        <label for="price">Price:</label>
        <input type="number" name="price" id="price" placeholder="Price" step="0.01" required>

        <button type="submit" name="add_menu_item">Add Item</button>
    </form>

    <!-- Special Offers -->
    <h2>Special Offers</h2>
    <form action="../../handlers/menu-handler.php" method="POST">
        <label for="menu_item_id">Menu Item:</label>
        <select name="menu_item_id" id="menu_item_id" required>
            <?php foreach ($menuItems as $item): ?>
                <option value="<?= $item['id']; ?>"><?= htmlspecialchars($item['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="discount_value">Discount Value:</label>
        <input type="number" name="discount_value" id="discount_value" placeholder="Discount Value" step="0.01" required>

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" required>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" required>

        <button type="submit" name="add_special_offer">Add Offer</button>
    </form>

</body>
</html>
