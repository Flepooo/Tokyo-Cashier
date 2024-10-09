<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'tokyo_pos'; // Database name
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users (to assign an order to a user)
$users = $conn->query("SELECT user_id, username FROM users");

// Fetch all products to populate the product dropdown
$products = $conn->query("SELECT product_id, product_name, price, quantity FROM products");

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $order_date = date('Y-m-d H:i:s'); // Get the current date/time

    // Get the product and quantity data from the form
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    // Initialize total price and stock availability check
    $total_price = 0;
    $outOfStock = false;

    // Check if any quantity is zero and calculate the total price
    foreach ($quantities as $index => $quantity) {
        if ($quantity <= 0) {
            $outOfStock = true; // Set flag if any quantity is zero or less
            break;
        }

        // Check available quantity in stock
        $product_id = $product_ids[$index];
        $product = $conn->query("SELECT quantity FROM products WHERE product_id = $product_id")->fetch_assoc();
        if ($product['quantity'] < $quantity) {
            $outOfStock = true; // Set flag if not enough stock
            break;
        }

        // Calculate total price if stock is available
        $total_price += $conn->query("SELECT price FROM products WHERE product_id = $product_id")->fetch_assoc()['price'] * $quantity;
    }

    if ($outOfStock) {
        // Display alert if any product is out of stock
        echo "<script>alert('One or more products are not in stock. Please check your quantities.');</script>";
    } else {
        // Insert the order into the orders table
        $insertOrder = $conn->query("INSERT INTO orders (user_id, total_price, order_date) VALUES ('$user_id', '$total_price', '$order_date')");

        if ($insertOrder) {
            // Get the last inserted order ID
            $order_id = $conn->insert_id;

            // Insert each product in the order into the order_items table
            foreach ($product_ids as $index => $product_id) {
                $quantity = $quantities[$index];
                $product = $conn->query("SELECT price FROM products WHERE product_id = $product_id")->fetch_assoc();
                $price = $product['price'];

                // Insert order item
                $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '$product_id', '$quantity', '$price')");

                // Update the product quantity in the products table
                $conn->query("UPDATE products SET quantity = quantity - $quantity WHERE product_id = $product_id");
            }

            // Redirect to the order receipt page with the order ID
            header("Location: order_receipt.php?order_id=$order_id");
            exit;
        } else {
            // Error message
            error_log("Insert Order Error: " . $conn->error); // Log the error
            $errorMessage = "Failed to add the order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Order</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto mt-10">
        <div class="w-full max-w-3xl mx-auto bg-white p-8 rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-6">Add New Order</h1>

            <?php if (isset($successMessage)) { ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                <?php echo $successMessage; ?>
            </div>
            <?php } elseif (isset($errorMessage)) { ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php echo $errorMessage; ?>
            </div>
            <?php } ?>

            <form id="orderForm" action="add_order.php" method="POST">
                <!-- Select User -->
                <div class="mb-6">
                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">User:</label>
                    <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <?php while ($user = $users->fetch_assoc()) { ?>
                        <option value="<?php echo $user['user_id']; ?>"><?php echo $user['username']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Add Products and Quantities -->
                <div id="product-section">
                    <div class="flex mb-4">
                        <div class="w-2/3 mr-2">
                            <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">Product:</label>
                            <select name="product_id[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <?php 
                                // Resetting products' result pointer to the beginning
                                $products->data_seek(0); // Reset the pointer
                                while ($product = $products->fetch_assoc()) { ?>
                                <option value="<?php echo $product['product_id']; ?>">
                                    <?php echo $product['product_name']; ?> - EGP<?php echo $product['price']; ?>
                                    (Available: <?php echo $product['quantity']; ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="w-1/3">
                            <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                            <input type="number" name="quantity[]" value="1" min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Add More Products Button -->
                <div class="mb-6">
                    <button type="button" id="add-product" class="bg-blue-500 text-white px-4 py-2 rounded">Add Another
                        Product</button>
                </div>

                <!-- Submit Button -->
                <div class="mb-6">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Submit Order</button>
                </div>
            </form>
            <!-- Button to go back to index.php -->
            <div class="mt-6 text-center">
                <a href="index.php" class="bg-gray-300 text-black py-2 px-4 rounded hover:bg-gray-400">Back to Home</a>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('add-product').addEventListener('click', function() {
        const productSection = document.getElementById('product-section');

        // Create a new product row
        const newProductRow = document.createElement('div');
        newProductRow.classList.add('flex', 'mb-4');
        newProductRow.innerHTML = `
            <div class="w-2/3 mr-2">
                <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">Product:</label>
                <select name="product_id[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <?php 
                    // Resetting products' result pointer to the beginning
                    $products->data_seek(0); // Reset the pointer
                    while ($product = $products->fetch_assoc()) { ?>
                    <option value="<?php echo $product['product_id']; ?>">
                        <?php echo $product['product_name']; ?> - EGP<?php echo $product['price']; ?> (Available: <?php echo $product['quantity']; ?>)
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="w-1/3">
                <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                <input type="number" name="quantity[]" value="1" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <button type="button" class="remove-product bg-red-500 text-white px-3 py-2 rounded ml-2">Remove</button>
        `;
        productSection.appendChild(newProductRow);

        // Add event listener to remove product row
        newProductRow.querySelector('.remove-product').addEventListener('click', function() {
            productSection.removeChild(newProductRow);
        });
    });

    document.getElementById('orderForm').addEventListener('submit', function(event) {
        const confirmation = confirm("Are you sure you want to submit this order?");
        if (!confirmation) {
            event.preventDefault(); // Prevent form submission
        }
    });
    </script>
</body>

</html>