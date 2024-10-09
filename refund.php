<?php
// Start the session
session_start();

// Include your database connection
$host = 'localhost';
$dbname = 'tokyo_pos'; // Database name
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Refund Form Submission
if (isset($_POST['refund_product'])) {
    // Sanitize user inputs
    $product_id = (int)$_POST['product_id'];
    $refund_quantity = (int)$_POST['refund_quantity'];
    $refund_reason = $conn->real_escape_string($_POST['refund_reason']);
    $order_id = (int)$_POST['order_id']; // Get the order ID from the form

    // Get the product price from the database
    $product_query = $conn->query("SELECT price FROM products WHERE product_id = $product_id");
    $product = $product_query->fetch_assoc();
    $refund_price = $product['price'] * $refund_quantity; // Calculate the total refund price

    // Update the product quantity in the inventory (increase stock)
    $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE product_id = ?");
    $stmt->bind_param("ii", $refund_quantity, $product_id);
    $stmt->execute();
    $stmt->close();

    // Check if the session variable is set
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO refunds (product_id, refund_quantity, refund_reason, refunded_by, order_id, refund_price) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        // Prepare the statement to avoid SQL injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissid", $product_id, $refund_quantity, $refund_reason, $user_id, $order_id, $refund_price);
        
        // Execute the query and check for success
        if ($stmt->execute()) {
            echo "<script>alert('New refund record created successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>"; // Output error message
        }
        $stmt->close();
    } else {
        echo "<script>alert('User is not logged in.');</script>";
    }
}

// Fetch Refund History
$refunds = $conn->query("SELECT r.*, p.product_name, o.order_id 
                         FROM refunds r 
                         JOIN products p ON r.product_id = p.product_id 
                         JOIN orders o ON r.order_id = o.order_id");

// Calculate total refund amount
$total_refund = 0;
while ($refund = $refunds->fetch_assoc()) {
    $total_refund += $refund['refund_price'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refunds</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-center">Process Refund</h1>

        <!-- Refund Form -->
        <form action="refund.php" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-6">
            <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
                <!-- Select Product -->
                <div class="col-span-1">
                    <label for="product_id" class="block text-sm font-medium text-gray-700">Select Product</label>
                    <select name="product_id" id="product_id" required class="mt-1 block w-full p-2 border rounded">
                        <?php
                        // Fetch products for dropdown
                        $product_result = $conn->query("SELECT * FROM products");
                        while ($product = $product_result->fetch_assoc()) {
                            echo "<option value='" . $product['product_id'] . "'>" . $product['product_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Select Order -->
                <div class="col-span-1">
                    <label for="order_id" class="block text-sm font-medium text-gray-700">Select Order</label>
                    <select name="order_id" id="order_id" required class="mt-1 block w-full p-2 border rounded">
                        <?php
                        // Fetch orders for dropdown
                        $order_result = $conn->query("SELECT * FROM orders");
                        while ($order = $order_result->fetch_assoc()) {
                            echo "<option value='" . $order['order_id'] . "'>Order #" . $order['order_id'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Refund Quantity -->
                <div class="col-span-1">
                    <label for="refund_quantity" class="block text-sm font-medium text-gray-700">Refund Quantity</label>
                    <input type="number" name="refund_quantity" id="refund_quantity" placeholder="Quantity" required
                        class="mt-1 block w-full p-2 border rounded" min="1">
                </div>

                <!-- Refund Reason -->
                <div class="col-span-1">
                    <label for="refund_reason" class="block text-sm font-medium text-gray-700">Refund Reason</label>
                    <input type="text" name="refund_reason" id="refund_reason" placeholder="Reason"
                        class="mt-1 block w-full p-2 border rounded">
                </div>
            </div>

            <!-- Refund Button -->
            <button type="submit" name="refund_product"
                class="mt-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">Refund Product</button>
        </form>

        <!-- Total Refund Amount -->
        <h2 class="text-2xl font-bold mb-4 text-center">Total Refund Amount: <span
                class="text-red-600"><?php echo number_format($total_refund, 2); ?> EGP</span></h2>

        <!-- Refund History Table -->
        <table class="min-w-full bg-white shadow-md rounded mb-6">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Product Name</th>
                    <th class="py-2 px-4 border-b">Quantity</th>
                    <th class="py-2 px-4 border-b">Reason</th>
                    <th class="py-2 px-4 border-b">Refunded By</th>
                    <th class="py-2 px-4 border-b">Order ID</th>
                    <th class="py-2 px-4 border-b">Refund Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Reset the result pointer to the beginning
                $refunds->data_seek(0);

                // Display refund history
                while ($refund = $refunds->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($refund['product_name']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($refund['refund_quantity']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($refund['refund_reason']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($refund['refunded_by']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($refund['order_id']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . number_format($refund['refund_price'], 2) . " EGP</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>

        </table>
        <!-- Button to go back to index.php -->
        <div class="mt-6 text-center">
            <a href="index.php" class="bg-gray-300 text-black py-2 px-4 rounded hover:bg-gray-400">Back to Home</a>
        </div>
    </div>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>