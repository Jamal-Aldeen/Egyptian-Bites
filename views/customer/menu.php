<?php
include('../../config/db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurant Menu</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
<?php include '../layouts/header.php'; ?>

<!-- Hero Section -->
<section class="hero text-center text-white d-flex align-items-center justify-content-center" 
    style="background: url('/public/assets/images/egypt-food.jpg') no-repeat center center/cover; 
    height: 50vh; position: relative;">
    <div class="hero-overlay" style="position: absolute; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6);"></div>
    <div class="container position-relative">
        <h1 class="display-4 fw-bold">Our Delicious Menu</h1>
        <p class="lead">Explore our variety of authentic Egyptian dishes!</p>
    </div>
</section>

<div class="container my-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search for a dish...">
</div>

<ul class="nav nav-pills justify-content-center mb-4">
    <li class="nav-item"><a class="nav-link active filter-btn" data-filter="all">All</a></li>
    <li class="nav-item"><a class="nav-link filter-btn" data-filter="piza">piza</a></li>
    <li class="nav-item"><a class="nav-link filter-btn" data-filter="crip">crip</a></li>
    <li class="nav-item"><a class="nav-link filter-btn" data-filter="desserts">Desserts</a></li>
</ul>

<!-- Menu Items -->
<div class="container">
    <div class="row g-4" id="menuContainer">
        <?php

        $sql = "SELECT 
        mi.id AS item_id,
        mi.name AS item_name,
        mi.description AS item_description,
        mi.price AS item_price,
        mi.image AS item_image,
        mi.availability AS item_availability,
        mi.created_at AS item_created_at,
        mi.updated_at AS item_updated_at,
        mc.id AS category_id,
        mc.name AS category_name
       
    FROM 
        MenuItems mi
    JOIN 
        MenuCategories mc 
    ON 
        mi.category_id = mc.id";


        $result = $pdo->query($sql);

        if (!$result) {
            die("Query failed: " . $pdo->errorInfo()[2]);
        }

        $menuItems = [];
        if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $menuItems[] = $row;
            }
        } else {
            echo "No items found in the menu.";
        }
       
        function generateStars($rating) {
            $fullStars = floor($rating);
            $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
            $emptyStars = 5 - ($fullStars + $halfStar);
            
            $stars = str_repeat('<i class="fas fa-star text-warning"></i>', $fullStars);
            if ($halfStar) $stars .= '<i class="fas fa-star-half-alt text-warning"></i>';
            $stars .= str_repeat('<i class="far fa-star text-warning"></i>', $emptyStars);
            
            return $stars;
        }


       foreach ($menuItems as $item) {
    echo '<div class="col-md-3 menu-item" data-category="' . $item["category_name"] . '">
    <div class="card text-center border-0 shadow-sm p-3">
    
        <img src="/public/uploads/' . $item["item_image"] . '" class="card-img-top rounded" alt="' . $item["item_name"] . '">
        <div class="card-body">
            <h5 class="card-title">' . $item["category_name"] . '</h5>
            <p class="price text-warning fw-bold">$' . $item["item_price"] . '</p>
            <p class="text-muted">' . $item["item_description"] . '</p>

            <div class="d-flex justify-content-center align-items-center mb-3">
                <button class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                <input type="number" class="form-control text-center mx-2 quantity-input" value="1" min="1" style="width: 50px;">
                <button class="btn btn-sm btn-outline-secondary increase-qty">+</button>
            </div>';

   
    echo '
           <a href="/views/customer/order-placement.php?item_id=' . $item["item_id"] . '" class="btn btn-success">Order This</a>
          </a>';

    echo '</div></div></div>';
]
        ?>
    </div>
</div>

<!-- Pagination -->
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item"><a class="page-link" href="#">Next</a></li>
    </ul>
</nav>

<?php include '../layouts/footer.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/public/js/scripts.js"></script>

</body>
</html>