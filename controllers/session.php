<?php
session_start();

// Regenerate session ID to prevent session fixation attacks
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Set session cookie parameters for security
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $cookieParams['lifetime'],
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true, // Only send cookies over HTTPS
    'httponly' => true, // Prevent JavaScript access to cookies
    'samesite' => 'Strict' // Prevent CSRF attacks
]);
?>