<?php
// Start the session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';

// Check if the user is logged in and display the username
if (isset($_SESSION['username'])) {
    $user_id = $_SESSION['user_id']; // Get user ID from the session

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare a statement to fetch the username based on user ID
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id); // Use "i" for integer type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the username from the result
        $user = $result->fetch_assoc();
        $username = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
    } else {
        header("Location: login.php"); // Redirect to login page or handle as needed
        exit;
    }
} else {
    $username = 'Guest'; // Fallback if user is not logged in
}

// Initialize variables for product details
$product_id = $product_name = $cost_price = $selling_price = $stock_level = '';

// Check if the edit_product_id is set in the URL
if (isset($_GET['edit_product_id'])) {
    $product_id = (int)$_GET['edit_product_id'];

    // Fetch product data to populate the form
    $sql_fetch = "SELECT product_name, cost_price, selling_price, stock_level FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql_fetch);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($product_name, $cost_price, $selling_price, $stock_level);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission to update the product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $product_id = (int)$_POST['update_product_id'];
    $product_name = htmlspecialchars(trim($_POST['product_name']));
    $cost_price = htmlspecialchars(trim($_POST['cost_price']));
    $selling_price = htmlspecialchars(trim($_POST['selling_price']));
    $stock_level = htmlspecialchars(trim($_POST['stock_level']));
    
    // Calculate net profit
    $net_profit = $selling_price - $cost_price;

    // Update the product in the database
    $sql_update = "UPDATE products SET product_name=?, cost_price=?, selling_price=?, stock_level=?, net_profit=? WHERE product_id=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sddidi", $product_name, $cost_price, $selling_price, $stock_level, $net_profit, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating product: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Query to fetch products from the products table
$sql = "SELECT product_id, product_name, cost_price, selling_price, stock_level, created_at, net_profit FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SB Admin 2 - Update Product</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <img src="img/tokyo-white-bg.png" width="130" />
                </div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item ">
                <a class="nav-link" href="super_admin_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider" />
            <div class="sidebar-heading">Interface</div>
            <li class="nav-item active">
                <a class="nav-link" href="products.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Products</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="orders.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Orders</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="refund.php">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Refund</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2" />
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $username; ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" />
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Update Product</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Product Data</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="update_product_id"
                                    value="<?php echo htmlspecialchars($product_id); ?>">
                                <div class="form-group">
                                    <label for="product_name">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                        value="<?php echo htmlspecialchars($product_name); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="cost_price">Cost Price</label>
                                    <input type="number" step="0.01" class="form-control" id="cost_price"
                                        name="cost_price" value="<?php echo htmlspecialchars($cost_price); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="selling_price">Selling Price</label>
                                    <input type="number" step="0.01" class="form-control" id="selling_price"
                                        name="selling_price" value="<?php echo htmlspecialchars($selling_price); ?>"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="stock_level">Stock Level</label>
                                    <input type="number" class="form-control" id="stock_level" name="stock_level"
                                        value="<?php echo htmlspecialchars($stock_level); ?>" required>
                                </div>
                                <button type="submit" name="update_product" class="btn btn-primary">Update
                                    Product</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End of Page Content -->
            </div>
            <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>