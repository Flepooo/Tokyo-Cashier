<?php
// Include the database connection file
include 'db_connection.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product from the database
    $sql = "DELETE FROM products WHERE id = $product_id";

    if ($conn->query($sql) === TRUE) {
        echo "Product deleted successfully";
        header('Location: products.php'); // Redirect back to the products list
        exit;
    } else {
        echo "Error deleting product: " . $conn->error;
    }
} else {
    echo "Invalid request!";
}
?>