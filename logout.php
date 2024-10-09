<?php
// Start session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/'); // Delete session cookie
}

// Destroy the session
session_destroy();

// Redirect to the login page or home page
header("Location: login.php");
exit;
?>