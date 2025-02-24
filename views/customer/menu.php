<?php
session_start();
require_once '../../config/db.php';
include '../layouts/header.php';
// include_once('../../controllers/Notification.php'); 

// --- Pagination Setup ---
$limit = 4;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

// --- Count total menu items ---
$countQuery = "SELECT COUNT(*) AS total FROM MenuItems";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute();
$totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $limit);

// --- Fetch categories ---
$query = "SELECT DISTINCT id, name FROM MenuCategories ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Fetch paginated menu items ---
$query = "SELECT id, name, description, price, image, category_id 
          FROM MenuItems 
          ORDER BY name 
          LIMIT :offset, :limit";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <style>
    body {
      background-color: #f8f9fa;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    /* Hero Section */
    .hero {
      background: url('/public/assets/images/egypt-food.jpg') no-repeat center center/cover;
      height: 50vh;
      position: relative;
      color: #fff;
      margin-bottom: 2rem;
    }

    .hero-overlay {
      position: absolute;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
    }

    .hero .container {
      position: relative;
      z-index: 2;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.25rem;
    }

    /* Search Bar */
    .search-bar {
      max-width: 400px;
      margin: 20px auto;
    }

    .search-bar input {
      border-radius: 20px;
      padding: 10px 20px;
      border: 1px solid #ced4da;
    }

    /* Category Navigation */
    .nav-pills .nav-link {
      border-radius: 20px;
      margin: 0 5px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
      background-color: #d4a017;
      color: white;
    }

    .nav-pills .nav-link:hover {
      background-color: rgba(212, 160, 23, 0.1);
      color: #d4a017;
    }

    /* Menu Card Styling */
    .menu-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin: 15px;
      transition: transform 0.3s ease;
      overflow: hidden;
    }

    .menu-card:hover {
      transform: translateY(-5px);
    }

    .card-img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 12px 12px 0 0;
    }

    .card-body {
      padding: 15px;
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 10px;
    }

    .card-desc {
      font-size: 0.9rem;
      color: #7f8c8d;
      height: 60px;
      overflow: hidden;
    }

    .card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 15px;
    }

    .card-price {
      font-size: 1.1rem;
      font-weight: bold;
      color: #d4a017;
    }

    .btn-add-cart {
      background: #d4a017;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 25px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn-add-cart:hover {
      background: #b58900;
    }

    /* Pagination */
    .pagination {
      margin-top: 20px;
    }

    .page-link {
      color: #d4a017;
    }

    .page-item.active .page-link {
      background-color: #d4a017;
      border-color: #d4a017;
    }
  </style>
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
            <div class="card-header">
              <img src="<?= !empty($item['image']) ? '/public/uploads/menu-image/' . htmlspecialchars($item['image']) : '/public/uploads/menu-image/default-menu.jpg'; ?>" 
                   alt="<?= htmlspecialchars($item['name']) ?>"
                   class="card-img">
            </div>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
              <p class="card-desc"><?= htmlspecialchars($item['description']) ?></p>
              <div class="card-footer">
                <span class="card-price">$<?= number_format($item['price'], 2) ?></span>
                <button class="btn-add-cart" 
                        onclick="addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>', <?= $item['price'] ?>, <?= $item['category_id'] ?>)">
                  <i class="fa fa-cart-plus"></i> Add to Cart
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
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
    function addToCart(id, name, price, category_id) {
      let cart = JSON.parse(localStorage.getItem("cart")) || [];
      const existingItem = cart.find(item => item.id === id);

      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cart.push({
          id: id,
          name: name,
          price: parseFloat(price),
          quantity: 1,
          category_id: category_id
        });
      }

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
    }

    // Update cart count in header
    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const count = cart.reduce((acc, item) => acc + item.quantity, 0);
      document.getElementById('cart-count').textContent = count;
    }

    // Initialize cart count on page load
    updateCartCount();
  </script>
  
  <?php include '../layouts/footer.php'; ?>
</body>
</html>