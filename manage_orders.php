<?php
// Start session
session_start();

// Check if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
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

// Fetch all orders
$orders = $conn->query("SELECT * FROM orders");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Tokyo POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl">
            <h1 class="text-2xl font-bold mb-6 text-center">Manage Orders</h1>

            <!-- Display All Orders -->
            <h2 class="text-xl mb-4">Order List</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4">Order ID</th>
                        <th class="py-2 px-4">User ID</th>
                        <th class="py-2 px-4">Order Date</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2 px-4"><?php echo $order['order_id']; ?></td>
                        <td class="py-2 px-4"><?php echo $order['user_id']; ?></td>
                        <td class="py-2 px-4"><?php echo $order['order_date']; ?></td>
                        <td class="py-2 px-4">
                            <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>"
                                class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-700">View</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Button to go back to index.php -->
            <div class="mt-6 text-center">
                <a href="index.php" class="bg-gray-300 text-black py-2 px-4 rounded hover:bg-gray-400">Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>