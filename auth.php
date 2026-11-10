

<?php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if SK official is logged in
if (!isset($_SESSION['official_id'])) {
    header("Location: login.php");
    exit();
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>
