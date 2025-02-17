<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurant Menu</title>
    
    <!-- Bootstrap & FontAwesome -->
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

<!-- Search Bar -->
<div class="container my-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search for a dish...">
</div>

<!-- Menu Categories -->
<ul class="nav nav-pills justify-content-center mb-4">
    <li class="nav-item"><a class="nav-link active filter-btn" data-filter="all">All</a></li>
    <li class="nav-item"><a class="nav-link filter-btn" data-filter="main-dishes">Main Dishes</a></li>
    <li class="nav-item"><a class="nav-link filter-btn" data-filter="drinks">Drinks</a></li>
    <li class="nav-item"><a class="nav-link filter-btn" data-filter="desserts">Desserts</a></li>
</ul>

<!-- Menu Items -->
<div class="container">
    <div class="row g-4" id="menuContainer">
        <?php
        $menuItems = [
            ["name" => "Koshari", "price" => "5.99", "rating" => 4.5, "image" => "Egyptian-Koshari-Featured.jpg", "category" => "main-dishes","desc" => "A mix of lentils, rice, pasta, and chickpeas.","discount" => "10% Off"],
            ["name" => "Mahshi", "price" => "7.99", "rating" => 4, "image" => "mahshi-plato.jpg", "category" => "main-dishes","discount" => "5% Off" ,"desc" => "Stuffed vegetables with rice & spices." ],
            ["name" => "Molokhia", "price" => "6.99", "rating" => 3.5, "image" => "Molokhia-17.webp", "category" => "main-dishes","discount" => "","desc" => "Green soup made from jute leaves."],
            ["name" => "Feteer Meshaltet", "price" => "8.50", "rating" => 5, "image" => "th.jpg", "category" => "desserts","discount" => "15% Off","desc" => "Flaky layered pastry with honey, sugar, or cheese."]
        ];

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
            echo '<div class="col-md-3 menu-item" data-category="' . $item["category"] . '">
            <div class="card text-center border-0 shadow-sm p-3">
                <img src="/public/assets/images/' . $item["image"] . '" class="card-img-top rounded" alt="' . $item["name"] . '">
                <div class="card-body">
                    <h5 class="card-title">' . $item["name"] . '</h5>
                    <div class="rating">' . generateStars($item["rating"]) . '</div>
                    <p class="price text-warning fw-bold">$' . $item["price"] . '</p>
                    <p class="text-muted">' . $item["desc"] . '</p>

                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <button class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                        <input type="number" class="form-control text-center mx-2 quantity-input" value="1" min="1" style="width: 50px;">
                        <button class="btn btn-sm btn-outline-secondary increase-qty">+</button>
                    </div>';

            // Corrected "Order Now" button
            echo '
                   <a href="/views/customer/order-placement.php?item_id=5" class="btn btn-success">Order This</a>

                  </a>';

            echo '</div></div></div>'; // Closing divs properly
        }
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
