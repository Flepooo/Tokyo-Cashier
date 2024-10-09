<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$dbname = 'tokyo_pos'; // Your database name
$username = 'root'; // Default username for XAMPP
$password = ''; // Default password for XAMPP (empty)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>