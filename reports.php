<?php
// Database connection settings
$servername = "localhost"; // Update if necessary
$username = "root"; // Your username
$password = ""; // Your password
$dbname = "tokyo_pos"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get total sales
$totalSellingQuery = "
    SELECT SUM(o.total_price) AS total_selling
    FROM orders o
";

$totalSellingResult = $conn->query($totalSellingQuery);
$totalSelling = $totalSellingResult->fetch_assoc()['total_selling'] ?? 0;

// Query to get total refunds
$totalRefundQuery = "
    SELECT SUM(r.refund_price) AS total_refund
    FROM refunds r
";

$totalRefundResult = $conn->query($totalRefundQuery);
$totalRefund = $totalRefundResult->fetch_assoc()['total_refund'] ?? 0;

// Calculate net sales after refunds
$netSellingAfterRefunds = $totalSelling - $totalRefund;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto mt-10 p-5 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold text-center mb-6">Sales Reports</h2>
        <table class="min-w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 text-left">Description</th>
                    <th class="py-2 px-4 text-left">Amount (EGP)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-gray-100">
                    <td class="py-2 px-4 border-b">Total Sales</td>
                    <td class="py-2 px-4 border-b"><?php echo number_format($totalSelling, 2); ?></td>
                </tr>
                <tr class="hover:bg-gray-100">
                    <td class="py-2 px-4 border-b">Total Refunds</td>
                    <td class="py-2 px-4 border-b"><?php echo number_format($totalRefund, 2); ?></td>
                </tr>
                <tr class="hover:bg-gray-100">
                    <td class="py-2 px-4 border-b font-bold">Net Sales After Refunds</td>
                    <td class="py-2 px-4 border-b font-bold"><?php echo number_format($netSellingAfterRefunds, 2); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Button to go back to index.php -->
        <div class="mt-6 text-center">
            <a href="index.php"
                class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-200">Back to
                Home</a>
        </div>
    </div>

</body>

</html>

<?php
// Close connection
$conn->close();
?>