<?php include("views/layouts/header.php"); ?>

<!-- Hero Section -->
<div id="home" class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="display-4">Welcome to <span class="text-warning">Egyptian Bites</span></h1>
        <p class="lead">Authentic Egyptian flavors straight from the heart of Cairo!</p>
        <div>
            <a href="#menu" class="btn btn-outline-light btn-lg">Our Menu</a>
            <a href="#booking" class="btn btn-warning btn-lg">Book a Table</a>
        </div>
    </div>
</div>

<!-- About Us Section -->
<section id="about" class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 about-content">
                <h2 class="about-title">Discover Our Story</h2>
                <p class="about-text">
                    Welcome to <strong>Egyptian Bites</strong>, where tradition meets taste. We take pride in serving 
                    authentic Egyptian cuisine, crafted with love and rich flavors. From the bustling streets of Cairo 
                    to your table, our dishes bring you the true essence of Egyptian hospitality.
                </p>
                <a href="#menu" class="btn btn-learn mt-3">Explore Our Menu</a>
            </div>
            <div class="col-md-6 about-image">
                <img src="assets/images/khofo-Ground_View01.jpg" alt="Authentic Egyptian Food">
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section id="menu" class="menu-section">
    <div class="container">
        <h2>Today's Menu</h2>
        <p class="text-center text-muted">Experience the best Egyptian dishes, made with love.</p>
        <div class="row">
            <div class="col-md-3 menu-item">
                <img src="assets/images/Egyptian-Koshari-Featured.jpg" alt="Koshari">
                <h5>Koshari</h5>
                <p class="price">$5.00</p>
                <p>A delicious mix of lentils, rice, pasta, and chickpeas topped with crispy onions and a rich tomato sauce.</p>
            </div>
            <div class="col-md-3 menu-item">
                <img src="assets/images/mahshi-plato.jpg" alt="Mahshi">
                <h5>Mahshi (Stuffed Vegetables)</h5>
                <p class="price">$7.50</p>
                <p>Stuffed zucchini, peppers, and vine leaves with a flavorful mix of rice, herbs, and spices.</p>
            </div>
        </div>
    </div>
</section>

<!-- Booking Section -->
<div id="booking" class="container my-5">
    <h2 class="booking-heading">Book Your Table with Us</h2>
    <form>
        <input type="text" class="form-control mb-3" placeholder="Your Name">
        <input type="email" class="form-control mb-3" placeholder="Your Email">
        <input type="date" class="form-control mb-3">
        <button type="submit" class="btn btn-book w-100">Book a Table</button>
    </form>
</div>

<?php include("views/layouts/footer.php"); ?>
