<?php
// Database credentials
$servername = "localhost";
$username = "root";     // Replace with your database username
$password = "";         // Replace with your database password
$dbname = "pos"; // Replace with your database name

// Check if a session is already started, if not, start it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment this block if you want to include character encoding (UTF-8)
// $conn->set_charset("utf8");

?>