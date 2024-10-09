<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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
    <title>User Dashboard | Tokyo POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h1 class="text-3xl font-bold mb-6 text-center">Welcome, <?php echo $username; ?></h1>

            <!-- Links to user-specific pages -->
            <div class="grid gap-6 grid-cols-1">
                <a href="add_order.php" class="block bg-blue-500 text-white text-center p-4 rounded hover:bg-blue-700">
                    Add Order
                </a>
                <a href="refund.php" class="block bg-purple-500 text-white text-center p-4 rounded hover:bg-purple-700">
                    Refund Order
                </a>
            </div>

            <!-- Logout option -->
            <div class="mt-6 text-center">
                <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-700">Logout</a>
            </div>
        </div>
    </div>
</body>

</html>