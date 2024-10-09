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

// Handle product CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Adding a new product
    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $real_price = $_POST['real_price'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $sql = "INSERT INTO products (product_name, real_price, price, quantity) VALUES ('$product_name', '$real_price', '$price', '$quantity')";
        $conn->query($sql);
    }

    // Deleting a product
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $sql = "DELETE FROM products WHERE product_id = $product_id";
        $conn->query($sql);
    }

    // Updating a product's quantity
    if (isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $sql = "UPDATE products SET quantity = $quantity WHERE product_id = $product_id";
        $conn->query($sql);
    }
}

// Fetch all products
$products = $conn->query("SELECT * FROM products");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Tokyo POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl">
            <h1 class="text-2xl font-bold mb-6 text-center">Manage Products</h1>

            <!-- Add New Product -->
            <form action="manage_products.php" method="POST" class="mb-6">
                <h2 class="text-xl mb-4">Add New Product</h2>
                <div class="grid gap-4 grid-cols-4">
                    <input type="text" name="product_name" placeholder="Product Name" required
                        class="p-2 border rounded">
                    <input type="number" name="real_price" step="0.01" placeholder="Real Price" required
                        class="p-2 border rounded">
                    <input type="number" name="price" step="0.01" placeholder="Selling Price" required
                        class="p-2 border rounded">
                    <input type="number" name="quantity" placeholder="Quantity" required class="p-2 border rounded">
                </div>
                <button type="submit" name="add_product"
                    class="mt-4 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Add Product</button>
            </form>

            <!-- Display All Products -->
            <h2 class="text-xl mb-4">Product List</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4">Product Name</th>
                        <th class="py-2 px-4">Real Price</th>
                        <th class="py-2 px-4">Selling Price</th>
                        <th class="py-2 px-4">Quantity</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2 px-4"><?php echo $product['product_name']; ?></td>
                        <td class="py-2 px-4"><?php echo $product['real_price']; ?></td>
                        <td class="py-2 px-4"><?php echo $product['price']; ?></td>
                        <td class="py-2 px-4"><?php echo $product['quantity']; ?></td>
                        <td class="py-2 px-4">
                            <!-- Update Quantity -->
                            <form action="manage_products.php" method="POST" class="inline">
                                <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>"
                                    class="w-20 p-2 border rounded">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <button type="submit" name="update_product"
                                    class="bg-green-500 text-white py-1 px-3 rounded hover:bg-green-700">Update</button>
                            </form>

                            <!-- Delete Product -->
                            <form action="manage_products.php" method="POST" class="inline">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <button type="submit" name="delete_product"
                                    class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-700">Delete</button>
                            </form>
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