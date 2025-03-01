<?php
session_start();
require_once '../../config/db.php';
include '../layouts/header.php';

// --- Pagination Setup ---
$limit = 8; // Increased limit for better user experience
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensures the page is at least 1

$offset = ($page - 1) * $limit;

// --- Count total menu items ---
$countQuery = "SELECT COUNT(*) AS total FROM MenuItems";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute();
$totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ($totalRows > 0) ? ceil($totalRows / $limit) : 1; // Avoid division by zero

// --- Fetch categories ---
$categoryQuery = "SELECT id, name FROM MenuCategories ORDER BY name";
$categoryStmt = $pdo->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// --- Fetch paginated menu items with active offers ---
$menuQuery = "SELECT mi.id, mi.name, mi.description, mi.price, mi.image, mi.category_id,
                     so.discount_type, so.discount_value, so.end_date,
                     CASE 
                         WHEN so.discount_type = 'Percentage' THEN mi.price - (mi.price * so.discount_value / 100)
                         WHEN so.discount_type = 'Fixed' THEN mi.price - so.discount_value
                         ELSE mi.price
                     END AS final_price
              FROM MenuItems mi
              LEFT JOIN SpecialOffers so 
              ON mi.id = so.menu_item_id 
              AND so.end_date >= CURDATE()  -- Ensuring active offers
              ORDER BY mi.name
              LIMIT :offset, :limit";

$menuStmt = $pdo->prepare($menuQuery);
$menuStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$menuStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$menuStmt->execute();
$menuItems = $menuStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Our Menu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 & Font Awesome 4.7.0 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/public/css/menu.css">
</head>

<body>

  <!-- Hero Section -->
  <section class="hero d-flex align-items-center justify-content-center">
    <div class="hero-overlay"></div>
    <div class="container text-center">
      <h1 class="fw-bold">Our Delicious Menu</h1>
      <p class="lead">Explore our variety of authentic Egyptian dishes!</p>
    </div>
  </section>

  <!-- Search Bar -->
  <div class="container">
    <div class="search-bar">
      <input type="text" id="searchInput" class="form-control" placeholder="Search menu items...">
    </div>
  </div>

  <!-- Category Navigation -->
  <div class="container mb-4">
    <ul class="nav nav-pills justify-content-center" id="categoryNav">
      <li class="nav-item">
        <a class="nav-link active" data-category="all" href="#">All</a>
      </li>
      <?php foreach ($categories as $category): ?>
        <li class="nav-item">
          <a class="nav-link" data-category="<?= htmlspecialchars($category['id']) ?>">
            <?= htmlspecialchars($category['name']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Product Grid -->
  <div class="container">
    <div class="row" id="menuItemsContainer">
      <?php foreach ($menuItems as $item): ?>
        <div class="col-md-3 col-sm-6 menu-item" data-category="<?= htmlspecialchars($item['category_id']) ?>">
          <div class="menu-card">
            <img src="<?= !empty($item['image']) ? '/public/uploads/menu-image/' . htmlspecialchars($item['image']) : '/public/uploads/menu-image/default-menu.jpg'; ?>"
              alt="<?= htmlspecialchars($item['name']) ?>" class="card-img">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
              <p class="card-desc"><?= htmlspecialchars($item['description']) ?></p>
              <div class="card-footer">
                <span class="card-price">
                  <?php if (!empty($item['discount_type'])): ?>
                    <span style="text-decoration: line-through; color: red;">$<?= number_format($item['price'], 2) ?></span>
                    <span style="color: green; font-weight: bold;" id="final-price-<?= $item['id'] ?>">
                      $<?= number_format($item['final_price'], 2) ?>
                    </span>
                    <span class="offer-badge">🔥 <?= $item['discount_value'] . ($item['discount_type'] === 'Percentage' ? '%' : '$') ?> Off!</span>
                  <?php else: ?>
                    <span id="final-price-<?= $item['id'] ?>">$<?= number_format($item['price'], 2) ?></span>
                  <?php endif; ?>
                </span>
                <button class="btn-add-cart"
                  onclick="addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>, <?= $item['category_id'] ?>, '<?= $item['discount_type'] ?>', <?= $item['discount_value'] ?? 0 ?>)">
                  <i class="fa fa-cart-plus"></i> Add to Cart
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Pagination Navigation -->
  <nav aria-label="Menu pagination">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Category Filtering
      const navLinks = document.querySelectorAll('#categoryNav .nav-link');
      navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          navLinks.forEach(l => l.classList.remove('active'));
          this.classList.add('active');
          const category = this.getAttribute('data-category');
          document.querySelectorAll('.menu-item').forEach(item => {
            item.style.display = (category === 'all' || item.dataset.category === category) ? 'block' : 'none';
          });
        });
      });

      // Search Functionality
      const searchInput = document.getElementById('searchInput');
      searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.menu-item').forEach(item => {
          const title = item.querySelector('.card-title').textContent.toLowerCase();
          const desc = item.querySelector('.card-desc').textContent.toLowerCase();
          item.style.display = (title.includes(query) || desc.includes(query)) ? 'block' : 'none';
        });
      });
    });

    // Add to Cart Functionality
    function addToCart(id, name, price, category_id, discount_type = '', discount_value = 0) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let existingItem = cart.find(item => item.id === id);

    let finalPrice = price;  // Default final price is the base price
    let discountMessage = '';

    // Check if there is a special offer and apply it
    if (discount_type) {
        if (discount_type === 'Percentage') {
            finalPrice = price - (price * discount_value / 100); // Apply percentage discount
            discountMessage = `<span style="color: red; font-weight: bold;">Special Offer: ${discount_value}% OFF!</span>`;
        } else if (discount_type === 'Fixed') {
            finalPrice = price - discount_value;  // Apply fixed amount discount
            discountMessage = `<span style="color: red; font-weight: bold;">Discount: -$${discount_value}</span>`;
        }
    }

    if (existingItem) {
        // If item already in cart, just update quantity and price
        existingItem.quantity += 1;
        existingItem.finalPrice = finalPrice;  // Update final price in cart
    } else {
        // If new item, add it to the cart with the calculated final price
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            finalPrice: parseFloat(finalPrice),
            quantity: 1,
            category_id: category_id,
            discount_type: discount_type,
            discount_value: discount_value
        });
    }

    // Save the updated cart to localStorage
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();  // Update the cart count in the UI

    // Display SweetAlert notification when item is added
    Swal.fire({
        title: "Item Added!",
        html: `
            <strong>${name}</strong> has been added to your cart.<br>
            <p><strong>Price:</strong> <span style="text-decoration: ${discountMessage ? 'line-through' : 'none'};">$${price}</span>
            ${discountMessage ? `<span style="color: green; font-size: 1.2em;">$${finalPrice.toFixed(2)}</span>` : ''}</p>
            ${discountMessage ? `<p>${discountMessage}</p>` : ''}`
        ,
        icon: "success",
        confirmButtonText: "OK",
        confirmButtonColor: "#d4a017"
    });

    // Update the final price in the cart page view
    updateCartTotal();
}

function updateCartTotal() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const total = cart.reduce((acc, item) => acc + item.finalPrice * item.quantity, 0);
    document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`; // Update total with discount
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const count = cart.reduce((acc, item) => acc + item.quantity, 0);
    document.getElementById('cart-count').textContent = count;
}

// Initialize cart count and total on page load
updateCartCount();
updateCartTotal();

  </script>

</body>

</html>
