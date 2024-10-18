<?php
// Start the session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db_connection.php'; // Include your database connection file


// Check if the user is logged in and display the username
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
} else {
    // Handle the case where user is not logged in
    header("Location: login.php"); // Redirect to login page or handle as needed
    exit;
}

// Fetch orders for the dropdown
$orders = [];
$query = "SELECT order_id FROM orders"; // Adjust the query as needed
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row['order_id'];
    }
}

// Initialize an empty array for order items
$orderItems = [];

// Check if the refund form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refund_amount'])) {
    $orderId = $_POST['order_id'];
    $refundAmounts = $_POST['refund_amount']; // This should be an array
    $reason = $_POST['reason'];

    // Fetch the order items for the selected order
    $query = "SELECT order_item_id, product_id, quantity FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare refund and restock queries
    $refundQuery = "INSERT INTO refunds (order_id, order_item_id, refund_amount, reason) VALUES (?, ?, ?, ?)";
    $restockQuery = "UPDATE products SET stock_level = stock_level + ? WHERE product_id = ?";
    
    // Loop through the order items and process refunds
    while ($row = $result->fetch_assoc()) {
        $orderItemId = $row['order_item_id'];
        $productId = $row['product_id'];
        $quantity = $row['quantity'];
        $refundAmount = isset($refundAmounts[$orderItemId]) ? $refundAmounts[$orderItemId] : 0;

        // Only process refunds if the refund amount is greater than zero
        if ($refundAmount > 0) {
            // Insert refund record
            $refundStmt = $conn->prepare($refundQuery);
            $refundStmt->bind_param("iiis", $orderId, $orderItemId, $refundAmount, $reason);
            $refundStmt->execute();

            // Restock the product
            $restockStmt = $conn->prepare($restockQuery);
            $restockStmt->bind_param("ii", $quantity, $productId);
            $restockStmt->execute();
        }
    }

    // Set a success message in the session and redirect to the same page
    $_SESSION['success_message'] = "Refund processed successfully.";
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
    exit; // Stop further processing
}

// Check for success message to display
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear message after displaying
}

// Fetch order items when an order is selected (via AJAX)
if (isset($_POST['order_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $query = "SELECT order_item_id, product_id, price, quantity FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $orderItems[] = $row;
    }
    echo json_encode($orderItems);
    exit; // Stop further processing
}

// Fetch refund records
$refunds = [];
$query = "SELECT r.refund_id, r.order_id, r.order_item_id, r.refund_amount, r.refund_date, r.reason, o.order_id, oi.order_item_id 
          FROM refunds r 
          JOIN orders o ON r.order_id = o.order_id 
          JOIN order_items oi ON r.order_item_id = oi.order_item_id 
          ORDER BY r.refund_date DESC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $refunds[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>Tokyo - Refund</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet" />

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Refund</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" />
    <link href="css/sb-admin-2.min.css" rel="stylesheet" />
    <script src="vendor/jquery/jquery.min.js"></script>
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
            <hr class="sidebar-divider my-0" />

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

            <li class="nav-item">
                <a class="nav-link" href="products.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Products</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="orders.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Orders</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="refund.php">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Refund</span></a>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block" />

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
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Refund</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate
                            Report</a>
                    </div>

                    <!-- Content Row -->

                    <!-- Content Row -->

                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        Earnings Overview
                                    </h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Refund ID</th>
                                                <th>Order ID</th>
                                                <th>Order Item ID</th>
                                                <th>Refund Amount</th>
                                                <th>Refund Date</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($refunds as $refund): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($refund['refund_id'], ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($refund['order_id'], ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($refund['order_item_id'], ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($refund['refund_amount'], ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($refund['refund_date'], ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($refund['reason'], ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        Refund Order
                                    </h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">


                                    <!-- Display success message if available -->
                                    <?php if ($successMessage): ?>
                                    <div class="alert alert-success"><?= $successMessage; ?></div>
                                    <?php endif; ?>

                                    <form method="POST" id="refundForm">
                                        <div class="form-group">
                                            <label for="order_id">Select Order ID</label>
                                            <select id="order_id" name="order_id" class="form-control" required>
                                                <option value="">Select an Order</option>
                                                <?php foreach ($orders as $order): ?>
                                                <option value="<?= $order ?>"><?= $order ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div id="orderItemsContainer" class="form-group" style="display:none;">
                                            <label>Order Items</label>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Item ID</th>
                                                        <th>Price</th>
                                                        <th>Quantity</th>
                                                        <th>Refund Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="orderItemsBody">
                                                    <!-- Order items will be populated here via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="form-group">
                                            <label for="reason">Reason for Refund</label>
                                            <input type="text" id="reason" name="reason" class="form-control"
                                                required />
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit Refund</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Content Column -->

                        <div class="col-lg-6 mb-4">
                            <!-- Illustrations -->

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
                        <span>Flepooo Copyright &copy; Tokyo Store POS 2024 </span>
                        <br />
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
                <div class="modal-body">
                    Select "Logout" below if you are ready to end your current session.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        Cancel
                    </button>
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
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

    <script>
    $(document).ready(function() {
        $('#order_id').change(function() {
            const orderId = $(this).val();
            if (orderId) {
                $.post('refund.php', {
                    order_id: orderId
                }, function(data) {
                    const orderItems = JSON.parse(data);
                    $('#orderItemsBody').empty();
                    orderItems.forEach(item => {
                        $('#orderItemsBody').append(`
                            <tr>
                                <td>${item.order_item_id}</td>
                                <td>${item.price}</td>
                                <td>${item.quantity}</td>
                                <td><input type="number" class="form-control" name="refund_amount[${item.order_item_id}]" step="0.01" required /></td>
                            </tr>
                        `);
                    });
                    $('#orderItemsContainer').show();
                });
            } else {
                $('#orderItemsContainer').hide();
            }
        });
    });
    </script>

</body>

</html>