<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db_connection.php';

$order_id = $_GET['order_id'];
$orderQuery = $conn->query("SELECT * FROM orders WHERE order_id = $order_id");
$order = $orderQuery->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$user_id = $order['user_id'];
$userQuery = $conn->query("SELECT username FROM users WHERE user_id = $user_id");
$user = $userQuery->fetch_assoc();
$username = $user['username'];

$orderItemsQuery = $conn->query("SELECT order_items.*, products.product_name FROM order_items JOIN products ON order_items.product_id = products.product_id WHERE order_items.order_id = $order_id");

// Assuming there's a `discount` column in the `orders` table
$discount = isset($order['discount']) ? $order['discount'] : 0; // Default discount is 0 if none is applied

// Assuming there's a `pay_with` column in the `orders` table
$paymentType = isset($order['pay_with']) ? $order['pay_with'] : 'Unknown'; // Default is 'Unknown' if not available

$taxRate = 0.15; 
$totalTax = $order['total_price'] * $taxRate;
$totalPriceWithTax = $order['total_price'] + $totalTax;

// Calculate total price after applying discount
$finalPrice = $order['total_price'] - $discount;
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
        width: 80mm;
        margin: 0 auto;
        padding: 10px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
    }

    h1,
    h2,
    h3 {
        text-align: center;
        margin: 0;
        font-size: 16px;
    }

    p {
        margin: 5px 0;
    }

    .logo {
        text-align: center;
        margin-bottom: 10px;
    }

    .button-container {
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
        margin-right: 5px;
    }

    .button-container a {
        background-color: #007BFF;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        padding: 5px;
        border-bottom: 1px solid #000;
        text-align: left;
        font-size: 14px;
    }

    .total {
        text-align: left;
        font-size: 14px;
        font-weight: bold;
    }

    @media print {
        .no-print {
            display: none;
        }

        body {
            background-color: #fff;
            border: none;
        }
    }
    </style>
</head>

<body>

    <div class="button-container no-print">
        <button onclick="window.print()">Print Receipt</button>
        <a href="orders.php">Add New Order</a>
    </div>

    <br />
    <div class="logo">
        <img src="img/tokyo-black.png" alt="Logo" style="width: 100px;">
    </div>

    <br />
    <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
    <p><strong>Cashier:</strong> <?php echo $username; ?></p>
    <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
    <p><strong>Payment:</strong> <?php echo $paymentType; ?></p> <!-- Payment type displayed here -->

    <p><strong>Order Items:</strong></p>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $orderItemsQuery->fetch_assoc()) { 
                $totalPrice = $item['quantity'] * $item['price']; ?>
            <tr>
                <td><?php echo $item['product_name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo number_format($totalPrice, 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>


    <br />
    <?php if ($discount > 0) { ?>
    <p class="total">Discount: <?php echo number_format($discount, 2); ?> EGP</p>
    <p class="total">Total Price: <?php echo number_format($order['total_price'], 2); ?> EGP</p>
    <?php } ?>

    <br />
    <p><strong>Refund Policy:</strong> Please retain this receipt for any future refunds.</p>
    <br />

</body>

</html>

<?php
$conn->close();
?>