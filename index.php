<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Redirect based on user role
if ($_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php"); // Redirect to admin dashboard
} elseif ($_SESSION['role'] == 'user') {
    header("Location: user_dashboard.php"); // Redirect to user dashboard
} else {
    header("Location: login.php"); // Redirect to login if role is not recognized
}
exit;
?>