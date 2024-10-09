<?php
// Start session
session_start();

// Check if user is logged in and is an admin
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

// Get order ID from the query string
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$orderQuery = $conn->query("SELECT * FROM orders WHERE order_id = $order_id");
$order = $orderQuery->fetch_assoc();

// Fetch order items
$orderItemsQuery = $conn->query("SELECT order_items.*, products.product_name FROM order_items JOIN products ON order_items.product_id = products.product_id WHERE order_items.order_id = $order_id");

// Check if order exists
if (!$order) {
    die("Order not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt | Tokyo POS</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        width: 300px;
        /* Suitable for thermal receipt printers */
        margin: 0;
        padding: 10px;
        font-size: 14px;
        line-height: 1.5;
    }

    h1 {
        font-size: 16px;
        text-align: center;
        margin: 0;
        font-weight: bold;
    }

    h2 {
        font-size: 14px;
        text-align: center;
        margin: 5px 0;
    }

    p {
        margin: 5px 0;
        text-align: left;
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
        font-size: 12px;
        /* Smaller font for items */
    }

    th {
        text-align: left;
        font-weight: bold;
    }

    .total {
        font-weight: bold;
    }

    @media print {
        .no-print {
            display: none;
            /* Hide non-print elements */
        }
    }
    </style>
    <script>
    function printReceipt() {
        window.print();
    }
    </script>
</head>

<body>

    <h1>Order Receipt</h1>
    <h2>Order ID: <?php echo $order['order_id']; ?></h2>
    <p><strong>User ID:</strong> <?php echo $order['user_id']; ?></p>
    <p><strong>Total Price:</strong> EGP <?php echo number_format($order['total_price'], 2); ?></p>
    <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>

    <h2>Order Items:</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $orderItemsQuery->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>EGP <?php echo number_format($item['price'], 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <p class="total">Total: EGP <?php echo number_format($order['total_price'], 2); ?></p>

    <p class="no-print">
        <button onclick="printReceipt()">Print Receipt</button>
        <a href="manage_orders.php">Back to Orders</a>
    </p>
</body>

</html>