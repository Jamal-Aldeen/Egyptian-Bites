<?php
session_start();

require_once __DIR__ . '/../../controllers/MenuController.php';
require_once '../../config/db.php';

$menuController = new MenuController();
$categories = $menuController->getCategories();
$menuItems = $menuController->getMenuItems();
$specialOffers = $menuController->getSpecialOffers();

// Build nested menu data from categories and items
$menuData = [];
foreach ($categories as $cat) {
    $menuData[$cat['id']] = [
        'name' => $cat['name'],
        'items' => []
    ];
}
foreach ($menuItems as $item) {
    if (isset($menuData[$item['category_id']])) {
        $menuData[$item['category_id']]['items'][] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Management</title>
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
  
  <div class=" col-md-9 ms-sm-auto col-lg-10 px-4">
    <div class="card shadow mb-4">
      <div class="card-header bg-dark text-white">
        <h3 class="mb-0">Menu Management</h3>
      </div>
      <div class="card-body">
        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
          <div class="alert alert-success">Operation completed successfully!</div>
        <?php elseif (isset($_GET['error'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        
        <!-- Manage Categories -->
        <h4 class="mt-4">Add New Category</h4>
        <form action="../../handlers/menu-handler.php" method="POST" class="mb-4">
          <div class="row g-3">
            <div class="col-md-6">
              <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
            </div>
            <div class="col-md-2">
              <button type="submit" name="add_category" class="btn btn-primary w-100">
                <i class="fas fa-plus"></i> Add
              </button>
            </div>
          </div>
        </form>

        <!-- Categories List with Nested Menu Items -->
        <h4 class="mt-4">Categories & Menu Items</h4>
        <ul class="list-group mb-4">
          <?php foreach ($menuData as $catId => $data): ?>
            <li class="list-group-item">
              <a data-bs-toggle="collapse" href="#collapseCat<?= $catId; ?>" role="button" aria-expanded="false" aria-controls="collapseCat<?= $catId; ?>">
                <?= htmlspecialchars($data['name']); ?>
              </a>
              <?php if (!empty($data['items'])): ?>
                <div class="collapse nested-menu" id="collapseCat<?= $catId; ?>">
                  <ul class="list-group ms-3 mt-2">
                    <?php foreach ($data['items'] as $item): ?>
                      <li class="list-group-item">
                        <a href="edit_menu_item.php?id=<?= $item['id']; ?>">
                          <?= htmlspecialchars($item['name']); ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>
              <!-- Delete category button -->
              <form action="../../handlers/menu-handler.php" method="POST" class="d-inline float-end">
                <input type="hidden" name="category_id" value="<?= $catId; ?>">
                <button type="submit" name="delete_category" class="btn btn-danger btn-sm" onclick="return confirm('Delete this category?')">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>

        <!-- Manage Menu Items -->
        <h4 class="mt-4">Add New Menu Item</h4>
        <form action="../../handlers/menu-handler.php" method="POST" class="mb-4" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-4">
              <select name="category_id" class="form-select" required>
                <option value="" disabled selected>Select Category</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <input type="text" name="menu_name" class="form-control" placeholder="Menu Item Name" required>
            </div>
            <div class="col-md-4">
              <input type="text" name="description" class="form-control" placeholder="Description" required>
            </div>
            <div class="col-md-4">
              <input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Menu Image (Optional)</label>
              <input type="file" class="form-control" name="menu_image">
            </div>
            <div class="col-md-2">
              <button type="submit" name="add_menu_item" class="btn btn-primary w-100">
                <i class="fas fa-plus"></i> Add Item
              </button>
            </div>
          </div>
        </form>

        <!-- Manage Special Offers -->
        <h4 class="mt-4">Special Offers</h4>
        <form action="../../handlers/menu-handler.php" method="POST" class="mb-4">
          <div class="row g-3">
            <div class="col-md-4">
              <select name="menu_item_id" class="form-select" required>
                <option value="" disabled selected>Select Menu Item</option>
                <?php foreach ($menuItems as $item): ?>
                  <option value="<?= $item['id']; ?>"><?= htmlspecialchars($item['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <input type="number" name="discount_value" class="form-control" placeholder="Discount Value" step="0.01" required>
            </div>
            <div class="col-md-3">
              <input type="date" name="start_date" class="form-control" required>
            </div>
            <div class="col-md-3">
              <input type="date" name="end_date" class="form-control" required>
            </div>
            <div class="col-md-2">
              <button type="submit" name="add_special_offer" class="btn btn-primary w-100">
                <i class="fas fa-plus"></i> Add Offer
              </button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
  </div>

</div>
  
  <!-- Bootstrap 5 JS and FontAwesome -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
