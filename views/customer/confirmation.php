<?php
session_start();
include __DIR__ . '/../layouts/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
 /* Global Styles */
body {
    background: #f4f7f6; /* Light grey background for a clean look */
    font-family: 'Arial', sans-serif;
    padding-top: 80px;
    color: #333; /* Dark text for readability */
}

/* Container for the confirmation section */
.confirmation-container {
    max-width: 800px;
    margin: 0 auto;
    background: #ffffff; /* White background for clarity */
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    text-align: center;
    animation: slideUp 1s ease-out; /* Slide-up animation for smooth appearance */
}

/* Heading styles */
.confirmation-container h1 {
    color: #f39c12; /* Warm yellow for energy and happiness */
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 20px;
    animation: fadeIn 1s ease-in-out; /* Fade-in animation */
}

/* Paragraph for the confirmation message */
.confirmation-container p {
    font-size: 1.2rem;
    color: #555; /* Slightly lighter text for a calm feel */
    margin-bottom: 30px;
    animation: fadeIn 1.5s ease-in-out;
}

/* Icon for the confirmation */
.confirmation-icon {
    font-size: 4rem;
    color: #27ae60; /* Green color for positivity and confirmation */
    margin-bottom: 20px;
    animation: bounce 1s infinite; /* Bouncing animation for energy */
}

/* Button for navigating back to home */
.btn-back-home {
    background-color: #27ae60; /* Blue for trust and calm */
    color: white;
    padding: 12px 30px;
    font-size: 1.1rem;
    text-transform: uppercase;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    font-weight: bold;
    border: none;
    animation: fadeIn 2s ease-in-out; /* Fade-in animation */
}

.btn-back-home:hover {
    background-color: #27ae60; /* Darker blue for hover effect */
    transform: scale(1.1); /* Slight zoom effect on hover */
}

/* Footer styling */
.footer {
    background-color:rgb(14, 15, 15); /* Dark footer background for balance */
    color: #ecf0f1; /* Light footer text */
    text-align: center;
    padding: 20px;
    margin-top: 40px;
}

.footer a {
    color: #f39c12; /* Gold color for footer links */
    text-decoration: none;
    font-weight: bold;
}

.footer a:hover {
    text-decoration: underline;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}


    </style>
</head>
<body>

<!-- Confirmation Message Section -->
<div class="container confirmation-container">
    <div class="confirmation-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <h1>Reservation Confirmed!</h1>
    <p>Thank you for your reservation! You will receive a confirmation email shortly.</p>

    <a href="/index.php" class="btn btn-back-home">
        <i class="fas fa-home"></i> Back to Home
    </a>
</div>

<?php
// Include the footer
include __DIR__ . '/../layouts/footer.php';
?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
