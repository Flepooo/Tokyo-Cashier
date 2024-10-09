<?php
// Start session
session_start();

// Check if user is logged in and if user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Display a welcome message with username
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Tokyo POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
            <h1 class="text-3xl font-bold mb-6 text-center">Welcome, Admin <?php echo $username; ?></h1>

            <!-- Links to various admin pages -->
            <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                <a href="manage_products.php"
                    class="block bg-blue-500 text-white text-center p-4 rounded hover:bg-blue-700">
                    Manage Products
                </a>
                <a href="manage_users.php"
                    class="block bg-green-500 text-white text-center p-4 rounded hover:bg-green-700">
                    Manage Users
                </a>
                <a href="manage_orders.php"
                    class="block bg-yellow-500 text-white text-center p-4 rounded hover:bg-yellow-700">
                    Manage Orders
                </a>
                <a href="add_order.php"
                    class="block bg-purple-500 text-white text-center p-4 rounded hover:bg-purple-700">
                    Add Order
                </a> <!-- New Add Order button -->
                <a href="refund.php" class="block bg-purple-500 text-white text-center p-4 rounded hover:bg-purple-700">
                    Refund Order
                </a> <!-- New Add Order button -->
                <a href="reports.php"
                    class="block bg-purple-500 text-white text-center p-4 rounded hover:bg-purple-700">
                    Reports
                </a> <!-- New Add Order button -->
            </div>

            <!-- Logout option -->
            <div class="mt-6 text-center">
                <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-700">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>