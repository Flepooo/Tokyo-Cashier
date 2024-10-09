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

// Fetch the order details
$order_id = $_GET['order_id'];
$orderQuery = $conn->query("SELECT * FROM orders WHERE order_id = $order_id");
$order = $orderQuery->fetch_assoc();

// Fetch the username from the users table
$user_id = $order['user_id'];
$userQuery = $conn->query("SELECT username FROM users WHERE user_id = $user_id");
$user = $userQuery->fetch_assoc();
$username = $user['username'];

// Fetch order items
$orderItemsQuery = $conn->query("SELECT order_items.*, products.product_name FROM order_items JOIN products ON order_items.product_id = products.product_id WHERE order_items.order_id = $order_id");

// Check if order exists
if (!$order) {
    die("Order not found.");
}

// Define tax rate (for example, 15%)
$taxRate = 0.15; 
$totalTax = $order['total_price'] * $taxRate;
$totalPriceWithTax = $order['total_price'] + $totalTax;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        width: 300px;
        margin: 0;
        padding: 10px;
        font-size: 14px;
        line-height: 1.5;
    }

    h1,
    h2 {
        text-align: center;
        margin: 0;
    }

    h1 {
        font-size: 18px;
    }

    h2 {
        font-size: 16px;
    }

    p {
        margin: 5px 0;
        text-align: left;
    }

    .logo {
        text-align: center;
        margin-bottom: 10px;
    }

    .cut-line {
        border-top: 1px dashed #000;
        margin: 20px 0;
        text-align: center;
        font-size: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        text-align: left;
        padding: 5px;
        border-bottom: 1px solid #000;
        font-size: 14px;
    }

    /* Flexbox for buttons at the top */
    .button-container {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .button-container button,
    .button-container a {
        padding: 8px 12px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .button-container a {
        background-color: #007BFF;
        text-align: center;
    }

    @media print {
        .no-print {
            display: none;
        }
    }
    </style>
</head>

<body>

    <!-- Buttons at the top -->
    <div class="button-container no-print">
        <button onclick="window.print()">Print Receipt</button>
        <a href="add_order.php">Add New Order</a>
    </div>

    <!-- Logo at the top -->
    <div class="logo">
        <img src="logo.jpg" alt="Logo" style="width: 100px;">
    </div>

    <h1>Order Receipt</h1>
    <h2>Order ID: <?php echo $order['order_id']; ?></h2>
    <p><strong>User:</strong> <?php echo $username; ?></p> <!-- Displaying the username -->
    <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>

    <h3>Order Items:</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price (EGP)</th>
                <th>Total (EGP)</th> <!-- Added new column for total -->
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $orderItemsQuery->fetch_assoc()) { 
                $totalPrice = $item['quantity'] * $item['price']; // Calculate total price for the item
            ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo number_format($totalPrice, 2); ?></td> <!-- Display total price for the item -->
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <p><strong>Total Price:</strong> EGP <?php echo number_format($order['total_price'], 2); ?></p>
    <p style="margin-top: 20px;"><strong>Refund Policy:</strong> Please retain this receipt for any future refunds.</p>

    <!-- Cut line -->
    <div class="cut-line">-</div>

</body>

</html>

<?php
// Close connection
$conn->close();
?>