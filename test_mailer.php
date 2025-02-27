<?php
require_once __DIR__ . '/vendor/autoload.php'; // Ensure this path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    
    // Enable debugging
    $mail->SMTPDebug = 2;  // Set to 0 to disable debugging
    $mail->Debugoutput = 'html';

    echo "<p>✅ PHPMailer is loaded successfully!</p>";
    
    // Check if the class exists
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<p>✅ PHPMailer class is available.</p>";
    } else {
        echo "<p>❌ PHPMailer class is missing!</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error loading PHPMailer: " . $e->getMessage() . "</p>";
}
?>
