<?php
session_start();
require_once '../../config/db.php';
include '../layouts/header.php';
// include_once('../../controllers/Notification.php'); 

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
        <div class="col-md-3 col-sm-6 menu-item">
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
                    <span style="color: green; font-weight: bold;">$<?= number_format($item['final_price'], 2) ?></span>
                    <span class="offer-badge">🔥 <?= $item['discount_value'] . ($item['discount_type'] === 'Percentage' ? '%' : '$') ?> Off!</span>
                  <?php else: ?>
                    $<?= number_format($item['price'], 2) ?>
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
  </div>
  
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Category Filtering
      const navLinks = document.querySelectorAll('#categoryNav .nav-link');
      navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
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
      searchInput.addEventListener('input', function() {
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

        let finalPrice = price; // السعر الافتراضي
        let discountMessage = '';

        // التحقق مما إذا كان هناك عرض خاص وتطبيقه
        if (discount_type) {
            if (discount_type === 'Percentage') {
                finalPrice = price - (price * discount_value / 100);
                discountMessage = `<span style="color: red; font-weight: bold;">Special Offer: ${discount_value}% OFF!</span>`;
            } else if (discount_type === 'Fixed') {
                finalPrice = price - discount_value;
                discountMessage = `<span style="color: red; font-weight: bold;">Discount: -$${discount_value}</span>`;
            }
        }

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
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

        localStorage.setItem("cart", JSON.stringify(cart));
        updateCartCount();

        // عرض رسالة SweetAlert عند الإضافة للسلة
        Swal.fire({
            title: "Item Added!",
            html: `
                <strong>${name}</strong> has been added to your cart.<br>
                <p><strong>Price:</strong> <span style="text-decoration: ${discountMessage ? 'line-through' : 'none'};">$${price}</span>
                ${discountMessage ? `<span style="color: green; font-size: 1.2em;">$${finalPrice.toFixed(2)}</span>` : ''}</p>
                ${discountMessage ? `<p>${discountMessage}</p>` : ''}
            `,
            icon: "success",
            confirmButtonText: "OK",
            confirmButtonColor: "#d4a017"
        });
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        const count = cart.reduce((acc, item) => acc + item.quantity, 0);
        document.getElementById('cart-count').textContent = count;
    }

    updateCartCount();



      localStorage.setItem("cart", JSON.stringify(cart));
      updateCartCount();
      
      // Show added feedback
      const btn = event.target;
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<i class="fa fa-check"></i> Added!';
      btn.style.backgroundColor = '#28a745';
      
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.backgroundColor = '#d4a017';
      }, 2000);
    

    // Update cart count in header
    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const count = cart.reduce((acc, item) => acc + item.quantity, 0);
      document.getElementById('cart-count').textContent = count;
    }
    

    function showMenuItemDetails(item) {
        let discountHTML = '';
        let finalPrice = item.price;

        // التحقق مما إذا كان هناك عرض خاص
        if (item.discount_type) {
            if (item.discount_type === 'Percentage') {
                finalPrice = item.price - (item.price * item.discount_value / 100);
                discountHTML = `<p style="color: red; font-weight: bold;">Special Offer: ${item.discount_value}% OFF!</p>`;
            } else if (item.discount_type === 'Fixed') {
                finalPrice = item.price - item.discount_value;
                discountHTML = `<p style="color: red; font-weight: bold;">Special Offer: -$${item.discount_value} OFF!</p>`;
            }
        }

        Swal.fire({
            title: item.name,
            html: `
                <img src="/public/uploads/menu-image/${item.image || 'default-menu.jpg'}" 
                     alt="${item.name}" style="width: 100%; border-radius: 10px; margin-bottom: 10px;">
                <p><strong>Description:</strong> ${item.description}</p>
                ${discountHTML}
                <p><strong>Price:</strong> <span style="text-decoration: ${discountHTML ? 'line-through' : 'none'};">$${item.price}</span> 
                ${discountHTML ? `<span style="color: green; font-size: 1.2em;">$${finalPrice.toFixed(2)}</span>` : ''}</p>
                <p><strong>Availability:</strong> ${item.availability ? 'Available' : 'Not Available'}</p>
            `,
            showCloseButton: true,
            confirmButtonText: 'Close',
            confirmButtonColor: '#d4a017'
        });
        function addToCart(id, name, price, category_id, discount_type = '', discount_value = 0) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let existingItem = cart.find(item => item.id === id);

    let finalPrice = price; 
    let discountMessage = '';

    // Apply discount logic
    if (discount_type) {
        if (discount_type === 'Percentage') {
            finalPrice = price - (price * discount_value / 100);
            discountMessage = `<span style="color: red; font-weight: bold;">Special Offer: ${discount_value}% OFF!</span>`;
        } else if (discount_type === 'Fixed') {
            finalPrice = price - discount_value;
            discountMessage = `<span style="color: red; font-weight: bold;">Discount: -$${discount_value}</span>`;
        }
    }

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
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

    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();

    // Show success alert
    Swal.fire({
        title: "Item Added!",
        html: `
            <strong>${name}</strong> has been added to your cart.<br>
            <p><strong>Price:</strong> <span style="text-decoration: ${discountMessage ? 'line-through' : 'none'};">$${price}</span>
            ${discountMessage ? `<span style="color: green; font-size: 1.2em;">$${finalPrice.toFixed(2)}</span>` : ''}</p>
            ${discountMessage ? `<p>${discountMessage}</p>` : ''}
        `,
        icon: "success",
        confirmButtonText: "OK",
        confirmButtonColor: "#d4a017"
    });
}

    }



    // Initialize cart count on page load
    updateCartCount();

  </script>
  
  <?php include '../layouts/footer.php'; ?>
</body>
</html>