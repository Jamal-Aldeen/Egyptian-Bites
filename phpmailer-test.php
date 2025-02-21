<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);
    echo 'PHPMailer loaded successfully!';
} catch (Exception $e) {
    echo 'Error loading PHPMailer: ' . $e->getMessage();
}
?>
