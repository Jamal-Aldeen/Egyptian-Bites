<?php
// Add to index.php's switch statement
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'logout':
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login"); // Redirect to login page after logout
        exit();
        break;
    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header("Location: /views/shared/login.php");
            exit();
        }
        include 'views/customer/profile.php';
        break;
    case 'dashboard':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
            header("Location: /views/shared/login.php");
            exit();
        }
        include 'views/staff/dashboard.php';
        break;
    // ... other cases
}



?>
<?php include("views/layouts/header.php"); ?>

<!-- Hero Section -->
<div id="home" class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="display-4">Welcome to <span class="text-warning">Egyptian Bites</span></h1>
        <p class="lead">Authentic Egyptian flavors straight from the heart of Cairo!</p>
        <div class="button-container">
    <a href="#menu" class="btn btn-menu">Our Menu</a>
    <a href="#booking" class="btn btn-yellow">Book a Table</a>
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
                <img src="public/assets/images/khofo-Ground_View01.jpg" alt="Authentic Egyptian Food">
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
                <img src="public/assets/images/Egyptian-Koshari-Featured.jpg" alt="Koshari">
                <h5>Koshari</h5>
                <p class="price">$5.00</p>
                <p>A delicious mix of lentils, rice, pasta, and chickpeas topped with crispy onions and a rich tomato sauce.</p>
            </div>
            <div class="col-md-3 menu-item">
                <img src="public/assets/images/mahshi-plato.jpg" alt="Mahshi">
                <h5>Mahshi (Stuffed Vegetables)</h5>
                <p class="price">$7.50</p>
                <p>Stuffed zucchini, peppers, and vine leaves with a flavorful mix of rice, herbs, and spices.</p>
            </div>
            <!-- Menu Item 3: Molokhia -->
            <div class="col-md-3 menu-item">
                <img src="public/assets/images/Molokhia-17.webp" alt="Molokhia">
                <h5>Molokhia</h5>
                <p class="price">$6.99</p>
                <p>Traditional green soup made from jute leaves, served with rice or bread and chicken or rabbit.</p>
            </div>
            <!-- Menu Item 4: Feteer Meshaltet -->
            <div class="col-md-3 menu-item">
                <img src="public/assets/images/th.jpg" alt="Feteer Meshaltet">
                <h5>Feteer Meshaltet</h5>
                <p class="price">$8.50</p>
                <p>Flaky layered pastry served with honey, sugar, or stuffed with cheese and meat.</p>
            </div>
        </div>
          <!-- View Full Menu Button -->
        <div class="text-center mt-4">
            <a href="views/customer/menu.php" class="btn btn-warning btn-lg">View Full Menu</a>
        </div>
    </div>
</section>

<!-- Booking Section -->
<div id="booking" class="container my-5">
    <h2 class="booking-heading">Book Your Table with Us</h2>
    <div class="row align-items-center">
        <!-- Left Side Image -->
        <div class="col-md-6 booking-image">
            <img src="public/assets/images/image.png" alt="Elegant Egyptian Dining">
        </div>

        <!-- Right Side Form -->
        <div class="col-md-6">
            <div class="booking-section">
                <form>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Your Name">
                        </div>
                        <div class="col-md-4">
                            <input type="email" class="form-control" placeholder="Your Email">
                        </div>
                        <div class="col-md-4">
                            <input type="tel" class="form-control" placeholder="Your Phone">
                        </div>

                        <div class="col-md-4">
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" placeholder="# of People" min="1">
                        </div>

                        <div class="col-12">
                            <textarea class="form-control" rows="4" placeholder="Special Requests"></textarea>
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-book w-100">Book a Table</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- fd -->
<section id="testimonials" class="testimonial-section">
    <div class="container">
        <h2 class="testimonial-title">What Our Customers Say</h2>

        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                
                <!-- Testimonial 1 -->
                <div class="carousel-item active">
                    <div class="testimonial-box">
                        <p class="testimonial-text">"The best Egyptian food I've ever had! Authentic flavors and a great atmosphere."</p>
                        <img src="https://ui-avatars.com/api/?name=Ahmed+Youssef&background=random" alt="Customer 1" class="testimonial-avatar">
                        <p class="testimonial-author">Ahmed Youssef</p>
                        <p class="testimonial-role">Food Blogger</p>
                        <div class="testimonial-stars">★★★★★</div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="carousel-item">
                    <div class="testimonial-box">
                        <p class="testimonial-text">"Absolutely loved the Koshari! Reminded me of my childhood in Egypt. Highly recommended!"</p>
                        <img src="https://ui-avatars.com/api/?name=Sara+Khaled&background=random" alt="Customer 2" class="testimonial-avatar">
                        <p class="testimonial-author">Sara Khaled</p>
                        <p class="testimonial-role">Restaurant Critic</p>
                        <div class="testimonial-stars">★★★★★</div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="carousel-item">
                    <div class="testimonial-box">
                        <p class="testimonial-text">"Incredible service and delicious Molokhia! Will definitely come back!"</p>
                        <img src="https://ui-avatars.com/api/?name=Mohamed+Fathy&background=random" alt="Customer 3" class="testimonial-avatar">
                        <p class="testimonial-author">Mohamed Fathy</p>
                        <p class="testimonial-role">Local Guide</p>
                        <div class="testimonial-stars">★★★★★</div>
                    </div>
                </div>

            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

            <!-- Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2"></button>
            </div>

        </div>
    </div>
</section>
<!-- conneect us -->
 <!-- Contact Us Section -->
<section id="contact" class="contact-section">
    <div class="contact-overlay"></div>
    <div class="container contact-content">
        <h2 class="contact-title">Get in Touch</h2>

        <div class="row">
            <!-- Contact Info -->
            <div class="col-md-4 text-center">
                <p class="contact-info"><i class="fas fa-map-marker-alt"></i> 123 Cairo Street, Egypt</p>
                <p class="contact-info"><i class="fas fa-phone"></i> +20 100 200 3000</p>
                <p class="contact-info"><i class="fas fa-envelope"></i> contact@egyptianbites.com</p>
            </div>

            <!-- Contact Form -->
            <div class="col-md-8">
                <form class="contact-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" placeholder="Your Email" required>
                        </div>
                        <div class="col-12">
                            <textarea rows="4" placeholder="Your Message" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-send">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Optional: Google Map -->
        <div class="row mt-5">
            <div class="col-12">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3456.123456789012!2d31.23571131511691!3d30.04441928186057!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145846dd16b3e7cf%3A0x5d0d0a59a2f5c7f7!2sCairo%2C%20Egypt!5e0!3m2!1sen!2seg!4v1611177744575!5m2!1sen!2seg"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>

<?php include("views/layouts/footer.php"); ?>
