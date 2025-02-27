<?php
session_start();
require_once __DIR__ . '/../../controllers/MenuController.php';
require_once '../../config/db.php';

$menuController = new MenuController();
$categories = $menuController->getCategories();
$menuItems = $menuController->getMenuItems();
$specialOffers = $menuController->getSpecialOffers();

// Build menu structure
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include "../../public/css/dashboard.css"; ?>
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">
            <?php include '../layouts/sidebar.php'; ?>

            <div class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-dark text-white">
                        <h3 class="mb-0">Menu Management</h3>
                    </div>
                    <div class="card-body">
                        <div id="message-container"></div>

                        <!-- Add Category Form -->
                        <h4 class="mt-4">Add New Category</h4>
                        <form id="add-category-form" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="category_name" class="form-control"
                                        placeholder="Category Name" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Categories List -->
                        <h4 class="mt-4">Categories & Menu Items</h4>
                        <ul class="list-group mb-4" id="categories-list">
                            <?php foreach ($menuData as $catId => $data): ?>
                                <li class="list-group-item" data-category-id="<?= $catId ?>">
                                    <a data-bs-toggle="collapse" href="#collapseCat<?= $catId ?>"
                                        role="button" aria-expanded="false">
                                        <?= htmlspecialchars($data['name']) ?>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm float-end delete-category"
                                        data-category-id="<?= $catId ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php if (!empty($data['items'])): ?>
                                        <div class="collapse" id="collapseCat<?= $catId ?>">
                                            <ul class="list-group ms-3 mt-2">
                                                <?php foreach ($data['items'] as $item): ?>
                                                    <li class="list-group-item">
                                                        <a href="edit_menu_item.php?id=<?= $item['id'] ?>">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Add Menu Item Form -->
                        <h4 class="mt-4">Add New Menu Item</h4>
                        <form id="add-menu-item-form" class="mb-4" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select name="category_id" class="form-select" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>">
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="menu_name" class="form-control"
                                        placeholder="Item Name" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="description" class="form-control"
                                        placeholder="Description" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="price" class="form-control"
                                        step="0.01" placeholder="Price" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Menu Image (Optional)</label>
                                    <input type="file" class="form-control" name="menu_image"
                                        accept="image/jpeg, image/png, image/gif">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Add Special Offer Form -->
                        <h4 class="mt-4">Special Offers</h4>
                        <form id="add-special-offer-form" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <select name="menu_item_id" class="form-select" required>
                                        <option value="" disabled selected>Menu Item</option>
                                        <?php foreach ($menuItems as $item): ?>
                                            <option value="<?= $item['id'] ?>">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="discount_type" class="form-select" required>
                                        <option value="" disabled selected>Type</option>
                                        <option value="Percentage">Percentage</option>
                                        <option value="Fixed">Fixed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="discount_value" class="form-control"
                                        step="0.01" placeholder="Value" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Special Offers Table -->
                        <h4 class="mt-4">Current Special Offers</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Discount</th>
                                    <th>Valid Until</th>
                                    <th>Actions</th>
                                </tr>
                            <tbody>
                                <?php foreach ($specialOffers as $offer): ?>
                                    <tr>
                                        <td><?= isset($offer['menu_item']) ? htmlspecialchars($offer['menu_item']) : 'Unknown Item' ?></td>
                                        <td>
                                            <?= ($offer['discount_type'] === 'Percentage')
                                                ? $offer['discount_value'] . '%'
                                                : '$' . $offer['discount_value'] ?>
                                        </td>
                                        <td><?= htmlspecialchars($offer['end_date']) ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm delete-offer" data-offer-id="<?= $offer['id'] ?>">
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageContainer = document.getElementById('message-container');

            function showMessage(message, type = 'success') {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible fade show`;
                alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                messageContainer.prepend(alert);
                setTimeout(() => alert.remove(), 5000);
            }

            // Add Category
            document.getElementById('add-category-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('add_category', '1');

                try {
                    const response = await fetch('../../handlers/menu-handler.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        const newCategory = `
                    <li class="list-group-item" data-category-id="${data.categoryId}">
                        <a data-bs-toggle="collapse" href="#collapseCat${data.categoryId}" 
                            role="button" aria-expanded="false">
                            ${data.categoryName}
                        </a>
                        <button type="button" class="btn btn-danger btn-sm float-end delete-category" 
                            data-category-id="${data.categoryId}">
                            <i class="fas fa-trash"></i>
                        </button>
                        <div class="collapse" id="collapseCat${data.categoryId}">
                            <ul class="list-group ms-3 mt-2"></ul>
                        </div>
                    </li>`;
                        document.getElementById('categories-list').insertAdjacentHTML('beforeend', newCategory);
                        e.target.reset();
                        showMessage('Category added successfully!');
                    } else {
                        throw new Error(data.error || 'Failed to add category');
                    }
                } catch (error) {
                    showMessage(error.message, 'danger');
                }
            });

            // Add Menu Item (Fixed Version)
            document.getElementById('add-menu-item-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                formData.append('add_menu_item', '1');

                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                try {
                    const response = await fetch('../../handlers/menu-handler.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        // Find or create category container
                        let categoryItem = document.querySelector(`li[data-category-id="${data.categoryId}"]`);
                        if (!categoryItem) {
                            throw new Error('Category container not found');
                        }

                        // Find or create collapse element
                        let collapseDiv = categoryItem.querySelector('.collapse');
                        if (!collapseDiv) {
                            collapseDiv = document.createElement('div');
                            collapseDiv.className = 'collapse';
                            collapseDiv.id = `collapseCat${data.categoryId}`;
                            categoryItem.appendChild(collapseDiv);
                        }

                        // Find or create items list
                        let itemsList = collapseDiv.querySelector('ul');
                        if (!itemsList) {
                            itemsList = document.createElement('ul');
                            itemsList.className = 'list-group ms-3 mt-2';
                            collapseDiv.appendChild(itemsList);
                        }

                        // Add new item
                        const newItem = `
                    <li class="list-group-item">
                        <a href="edit_menu_item.php?id=${data.itemId}">
                            ${data.itemName}
                        </a>
                    </li>`;
                        itemsList.insertAdjacentHTML('beforeend', newItem);

                        form.reset();
                        showMessage('Menu item added successfully!');
                    } else {
                        throw new Error(data.error || 'Failed to add menu item');
                    }
                } catch (error) {
                    showMessage(error.message, 'danger');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-plus"></i> Add Item';
                }
            });

            // Delete Category
            document.getElementById('categories-list').addEventListener('click', async (e) => {
                if (e.target.classList.contains('delete-category')) {
                    if (!confirm('Are you sure you want to delete this category?')) return;

                    const categoryId = e.target.dataset.categoryId;
                    const formData = new FormData();
                    formData.append('delete_category', '1');
                    formData.append('category_id', categoryId);

                    try {
                        const response = await fetch('../../handlers/menu-handler.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();

                        if (data.success) {
                            e.target.closest('li').remove();
                            showMessage('Category deleted successfully!');
                        } else {
                            throw new Error(data.error || 'Failed to delete category');
                        }
                    } catch (error) {
                        showMessage(error.message, 'danger');
                    }
                }
            });

            // Add Special Offer
            document.getElementById('add-special-offer-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                formData.append('add_special_offer', '1');

                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                try {
                    const response = await fetch('../../handlers/menu-handler.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        form.reset();
                        showMessage('Special offer added successfully!');
                    } else {
                        throw new Error(data.error || 'Failed to add special offer');
                    }
                } catch (error) {
                    showMessage(error.message, 'danger');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-plus"></i> Add';
                }
            });
            //delete offer
            document.addEventListener('click', async function(event) {
                if (event.target.classList.contains('delete-offer')) {
                    const offerId = event.target.dataset.offerId;
                    if (!confirm('Are you sure you want to delete this offer?')) return;

                    try {
                        const response = await fetch('../../handlers/menu-handler.php', {
                            method: 'POST',
                            body: JSON.stringify({
                                delete_special_offer: 1,
                                offer_id: offerId
                            }),
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            event.target.closest('tr').remove();
                            alert('Offer deleted successfully!');
                        } else {
                            throw new Error(data.error);
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                }
            });

        });
    </script>
</body>

</html>