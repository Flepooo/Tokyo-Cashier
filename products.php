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
        // Handle case where user ID is not found
        header("Location: login.php"); // Redirect to login page or handle as needed
        exit;
    }
} else {
    // Handle the case where user is not logged in
    header("Location: login.php"); // Redirect to login page or handle as needed
    exit;
}

// Include the database connection file

// Handle form submission to insert a new product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    // Collect and sanitize form data
    $product_name = htmlspecialchars(trim($_POST['product_name']));
    $cost_price = htmlspecialchars(trim($_POST['cost_price']));
    $selling_price = htmlspecialchars(trim($_POST['selling_price']));
    $stock_level = htmlspecialchars(trim($_POST['stock_level']));
    
    // Calculate net profit
    $net_profit = $selling_price - $cost_price;

    // Insert new product into the database
    $sql_insert = "INSERT INTO products (product_name, cost_price, selling_price, stock_level, net_profit) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("sddid", $product_name, $cost_price, $selling_price, $stock_level, $net_profit);

    if ($stmt->execute()) {
        echo "<script>alert('New product added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding product: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Handle product deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product_id'])) {
    $product_id = (int)$_POST['delete_product_id'];

    // Delete the product from the database
    $sql_delete = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting product: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Handle product update logic
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

// Handle product fetch for editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product_id'])) {
    $product_id = (int)$_POST['edit_product_id'];

    // Fetch product data to populate the form
    $sql_fetch = "SELECT product_name, cost_price, selling_price, stock_level FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql_fetch);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($product_name, $cost_price, $selling_price, $stock_level);
    $stmt->fetch();
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

    <title>SB Admin 2 - Products</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <img src="img/tokyo-white-bg.png" width="130" />
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="super_admin_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Heading -->
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

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
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
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
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

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2" />
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->


                        <!-- Nav Item - Messages -->


                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $username; ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" />
                            </a>
                            <!-- Dropdown - User Information -->
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

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Products</h1>

                    <!-- Insert New Product Form -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Add New Product</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="product_name">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="cost_price">Cost Price</label>
                                    <input type="number" step="0.01" class="form-control" id="cost_price"
                                        name="cost_price" required>
                                </div>
                                <div class="form-group">
                                    <label for="selling_price">Selling Price</label>
                                    <input type="number" step="0.01" class="form-control" id="selling_price"
                                        name="selling_price" required>
                                </div>
                                <div class="form-group">
                                    <label for="stock_level">Stock Level</label>
                                    <input type="number" class="form-control" id="stock_level" name="stock_level"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Product</button>
                            </form>
                        </div>
                    </div>

                    <!-- DataTables Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Cost Price</th>
                                            <th>Selling Price</th>
                                            <th>Stock Level</th>
                                            <th>Net Profit</th>
                                            <th>Date Added</th>
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Cost Price</th>
                                            <th>Selling Price</th>
                                            <th>Stock Level</th>
                                            <th>Net Profit</th>
                                            <th>Date Added</th>
                                            <th>Edit</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
    // Check if there are results and loop through them
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['cost_price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['selling_price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['stock_level']) . "</td>";
            echo "<td>" . htmlspecialchars($row['net_profit']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "<td>
            <a href='update_product.php?edit_product_id=" . htmlspecialchars($row['product_id']) . "' class='btn btn-warning btn-sm'>
                Edit
            </a>
                    <form action='' method='POST' style='display:inline;'>
                        <input type='hidden' name='delete_product_id' value='" . htmlspecialchars($row['product_id']) . "'>
                        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No products found</td></tr>";
    }
    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>