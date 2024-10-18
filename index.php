<?php
// Start the session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';

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


// Query to get sales data
$salesQuery = $conn->query("SELECT 
    COUNT(order_id) AS total_orders,
    SUM(total_price) AS total_sales,
    SUM(discount) AS total_discount
FROM orders");

$salesData = $salesQuery->fetch_assoc();

// Calculate net sales
$netSales = $salesData['total_sales'] - $salesData['total_discount'];


// Handle adding a new order and order items
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_order'])) {
  $customer_id = NULL; // Always set to NULL
  $total_price_before_discount = $_POST['total_price_before_discount'];
  
  // Check if discount amount is provided, if not, default to 0
  $discount_amount = !empty($_POST['discount_amount']) ? $_POST['discount_amount'] : 0.00;
  
  $pay_with = isset($_POST['pay_with']) ? $_POST['pay_with'] : 'cash'; // Default to cash

  // Calculate total price after discount
  $total_price = $total_price_before_discount - $discount_amount;

  // Start a transaction
  $conn->begin_transaction();

  try {
      // Insert the new order into the orders table
      $insert_order = $conn->prepare("INSERT INTO orders (user_id, customer_id, total_price_before_discount, discount, total_price, pay_with) VALUES (?, ?, ?, ?, ?, ?)");
      $insert_order->bind_param('iiddds', $user_id, $customer_id, $total_price_before_discount, $discount_amount, $total_price, $pay_with);
      
      if ($insert_order->execute()) {
          $order_id = $conn->insert_id; // Get the last inserted order ID

          // Loop through each product and insert into order_items
          foreach ($_POST['products']['product_id'] as $index => $product_id) {
              $quantity = $_POST['products']['quantity'][$index];

              // Fetch the product price and stock level
              $product_query = $conn->prepare("SELECT selling_price, stock_level FROM products WHERE product_id = ?");
              $product_query->bind_param('i', $product_id);
              $product_query->execute();
              $product_data = $product_query->get_result()->fetch_assoc();
              $price = $product_data['selling_price'];
              $stock_level = $product_data['stock_level'];

              if ($stock_level < $quantity) {
                  // Not enough stock for this product
                  throw new Exception("Not enough stock for product ID $product_id");
              }

              // Insert into order_items
              $insert_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
              $insert_item->bind_param('iiid', $order_id, $product_id, $quantity, $price);
              $insert_item->execute();

              // Update product stock level
              $update_stock = $conn->prepare("UPDATE products SET stock_level = stock_level - ? WHERE product_id = ?");
              $update_stock->bind_param('ii', $quantity, $product_id);
              $update_stock->execute();
          }

          // Commit the transaction
          $conn->commit();
          echo "Order added successfully!";
          // Redirect to avoid form resubmission
          header("Location: order_receipt.php?order_id=" . $order_id);
          exit;
      } else {
          throw new Exception("Failed to add order: " . $conn->error);
      }
  } catch (Exception $e) {
      // Rollback the transaction on error
      $conn->rollback();
      echo "Error: " . $e->getMessage();
  }
}
// Initialize search variable
$search = "";

// Check if a search query exists
if (isset($_GET['search'])) {
  $search = $_GET['search'];
}

// Prepare the SQL statement to fetch orders
// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT order_id, user_id, total_price_before_discount, discount, total_price, pay_with, order_date FROM orders WHERE order_id LIKE ? OR user_id LIKE ? ORDER BY order_date DESC");

// Use wildcard search for LIKE
$searchParam = "%" . $search . "%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

// Close the statement
$stmt->close();

$soldItemsQuery = $conn->query("
    SELECT 
        p.product_name, 
        SUM(oi.quantity) AS total_sold, 
        COALESCE(SUM(r.refund_amount), 0) AS total_refunded
    FROM 
        products p
    JOIN 
        order_items oi ON p.product_id = oi.product_id
    LEFT JOIN 
        refunds r ON oi.order_item_id = r.order_item_id
    GROUP BY 
        p.product_id
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>Tokyo - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet" />

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet" />
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
            <li class="nav-item active">
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
            <li class="nav-item">
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
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Orders
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <td><?php echo $salesData['total_orders']; ?></td>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Sales (EGP)
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <td><?php echo number_format($salesData['total_sales'], 2); ?></td>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Discounts (EGP)
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                        <td><?php echo number_format($salesData['total_discount'], 2); ?>
                                                        </td>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Net Sales (EGP)
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <td><?php echo number_format($netSales, 2); ?></td>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                    <div class="card-body">
                                        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                                            <thead>
                                                <tr style="background-color: #007BFF; color: white; text-align: left;">
                                                    <th style="padding: 12px; border: 1px solid #ddd;">Product Name</th>
                                                    <th style="padding: 12px; border: 1px solid #ddd;">Total Sold</th>
                                                    <th style="padding: 12px; border: 1px solid #ddd;">Total Refunded
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
        while ($item = $soldItemsQuery->fetch_assoc()) { ?>
                                                <tr style="background-color: #f9f9f9;">
                                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                                        <?php echo $item['product_name']; ?></td>
                                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                                        <?php echo $item['total_sold']; ?></td>
                                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                                        <?php echo $item['total_refunded']; ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        Add Order
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
                                    <form method="POST" action="orders.php">


                                        <!-- Dynamic Product Selection -->
                                        <div id="product-section">
                                            <div class="form-group product-row">
                                                <label for="products">Select Product</label>
                                                <select name="products[product_id][]"
                                                    class="form-control product-select">
                                                    <!-- Fetch products from the database -->
                                                    <?php
$product_query = $conn->query("SELECT product_id, product_name, selling_price, stock_level FROM products");
while ($product = $product_query->fetch_assoc()) {
echo "<option value='{$product['product_id']}' data-price='{$product['selling_price']}'>{$product['product_name']} - Price: {$product['selling_price']} (Stock: {$product['stock_level']})</option>";
}
?>
                                                </select>

                                                <!-- Quantity Input -->
                                                <label for="quantity">Quantity</label>
                                                <input type="number" name="products[quantity][]" value="1" min="1"
                                                    class="form-control product-quantity">
                                            </div>
                                        </div>

                                        <!-- Button to Add More Products -->
                                        <button type="button" id="add-product" class="btn btn-secondary">Add Another
                                            Product</button>

                                        <div class="form-group" style="margin-top: 20px">
                                            <label for="user_id">User: <?php echo $username; ?></label>
                                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                        </div>

                                        <!-- Total Price Before Discount (calculated automatically) -->
                                        <div class="form-group">
                                            <label for="total_price_before_discount">Total Price Before Discount</label>
                                            <input type="number" step="0.01" class="form-control"
                                                id="total_price_before_discount" name="total_price_before_discount"
                                                required readonly>
                                        </div>

                                        <!-- Optional Discount -->
                                        <div class="form-group">
                                            <label for="discount_amount">Discount Amount (Optional)</label>
                                            <input type="number" step="0.01" class="form-control" id="discount_amount"
                                                name="discount_amount" placeholder="Enter discount amount">
                                        </div>

                                        <!-- Payment Method -->
                                        <div class="form-group">
                                            <label for="pay_with">Pay With</label>
                                            <select class="form-control" id="pay_with" name="pay_with">
                                                <option value="cash" selected>Cash</option>
                                                <option value="visa">Visa</option>
                                            </select>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-primary" name="add_order">Add
                                            Order</button>
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
    function updateTotalPrice() {
        let total = 0;
        // Loop through each selected product
        document.querySelectorAll('.product-select').forEach(function(select, index) {
            const price = parseFloat(select.options[select.selectedIndex].dataset
                .price); // Get product price from data-price
            const quantity = parseFloat(document.querySelectorAll('.product-quantity')[index]
                .value); // Get corresponding quantity
            total += price * quantity; // Calculate total for that product
        });
        // Update the total price before discount
        document.getElementById('total_price_before_discount').value = total.toFixed(2);
    }

    // Add a new product row when "Add Another Product" is clicked
    document.getElementById('add-product').addEventListener('click', function() {
        const productSection = document.getElementById('product-section');
        const newProductRow = document.createElement('div');
        newProductRow.classList.add('form-group', 'product-row');
        newProductRow.innerHTML = `
            <label for="products">Select Product</label>
            <select name="products[product_id][]" class="form-control product-select">
                <?php
                $product_query->data_seek(0); // Reset product query result pointer
                while ($product = $product_query->fetch_assoc()) {
                    echo "<option value='{$product['product_id']}' data-price='{$product['selling_price']}'>{$product['product_name']} - Price: {$product['selling_price']} (Stock: {$product['stock_level']})</option>";
                }
                ?>
            </select>
            <label for="quantity">Quantity</label>
            <input type="number" name="products[quantity][]" value="1" min="1" class="form-control product-quantity">
            <button type="button" class="remove-product btn btn-secondary">Remove</button>
        `;
        productSection.appendChild(newProductRow);

        // Add event listeners to new select and quantity inputs for price calculation
        newProductRow.querySelector('.product-select').addEventListener('change', updateTotalPrice);
        newProductRow.querySelector('.product-quantity').addEventListener('input', updateTotalPrice);

        // Remove product row functionality
        newProductRow.querySelector('.remove-product').addEventListener('click', function() {
            productSection.removeChild(newProductRow);
            updateTotalPrice();
        });
    });

    // Update total price when product or quantity changes
    document.querySelectorAll('.product-select').forEach(select => select.addEventListener('change', updateTotalPrice));
    document.querySelectorAll('.product-quantity').forEach(input => input.addEventListener('input', updateTotalPrice));

    // Initial total price calculation
    updateTotalPrice();
    </script>

</body>

</html>