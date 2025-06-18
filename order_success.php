<?php
include("db.php");

// Require login
if (empty($sessionID)) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if ($order_id <= 0) {
    header("Location: browse_menu.php");
    exit;
}

// Fetch order
$ordQ = "SELECT * FROM orders WHERE id = $order_id AND user_id = $sessionID";
$ordR = mysqli_query($con, $ordQ);
if (!$ordR || mysqli_num_rows($ordR) === 0) {
    header("Location: browse_menu.php");
    exit;
}
$order = mysqli_fetch_assoc($ordR);

// Fetch order items
$itemQ = "
  SELECT oi.quantity, oi.unit_price, oi.total_price, mi.name, mi.image_url
  FROM order_items oi
  JOIN menu_items mi ON oi.menu_item_id = mi.id
  WHERE oi.order_id = $order_id
";
$itemR = mysqli_query($con, $itemQ);
$order_items = [];
while ($row = mysqli_fetch_assoc($itemR)) {
    $order_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Order Confirmation – Restaurant</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    .receipt {
      max-width: 800px;
      margin: auto;
      border: 1px solid #dee2e6;
      padding: 2rem;
      border-radius: .5rem;
      background: #fff;
    }
    .receipt-header {
      border-bottom: 1px solid #dee2e6;
      margin-bottom: 1rem;
      padding-bottom: .5rem;
    }
    .receipt-header h1 {
      margin: 0;
      font-size: 1.75rem;
    }
    .receipt-info .col-sm-6 {
      margin-bottom: .5rem;
    }
    .product-img {
      width: 50px;
      height: 50px;
      object-fit: contain;
    }
    .btn-group {
      margin-top: 1.5rem;
    }
  </style>
</head>
<body class="starter-page-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <h1 class="sitename"><span>Restaurant</span></h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="browse_menu.php">Menu</a></li>
          <li><a href="account.php">My Account</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
        <i class="mobile-nav-toggle bi bi-list"></i>
      </nav>
    </div>
  </header>

  <main class="main py-5">
    <div class="container">
      <div class="receipt shadow-sm">
        <div class="receipt-header text-center">
          <h1>Thank you for your order!</h1>
          <p class="text-muted">Order #<?php echo $order['id']; ?> • Placed on <?php echo date('d-m-Y H:i:s', strtotime($order['order_date'])); ?></p>
        </div>

        <div class="row receipt-info mb-4">
          <div class="col-sm-6">
            <h6>Order Details</h6>
            <p>Status: <strong><?php echo ucfirst($order['status']); ?></strong></p>
            <p>Total Amount: <strong>Rs. <?php echo number_format($order['total_amount'],0); ?></strong></p>
          </div>
          <div class="col-sm-6 text-sm-end">
            <h6>Delivery Info</h6>
            <!-- If you had stored address/phone in orders, display here -->
            <p>We will contact you shortly on your phone.</p>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th></th>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th>Unit Price</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order_items as $it): ?>
                <tr>
                  <td>
                    <?php if ($it['image_url'] && file_exists("assets/img/menu_items/{$it['image_url']}")): ?>
                      <img src="assets/img/menu_items/<?php echo htmlspecialchars($it['image_url']); ?>" class="product-img" alt="">
                    <?php else: ?>
                      <i class="bi bi-image fs-2 text-muted"></i>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($it['name']); ?></td>
                  <td class="text-center"><?php echo $it['quantity']; ?></td>
                  <td>Rs. <?php echo number_format($it['unit_price'],0); ?></td>
                  <td class="text-end">Rs. <?php echo number_format($it['total_price'],0); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" class="text-end">Grand Total:</th>
                <th class="text-end">Rs. <?php echo number_format($order['total_amount'],0); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="text-center btn-group">
          <a href="user/orders.php" class="btn btn-outline-primary me-2">View Order History</a>
          <a href="browse_menu.php" class="btn btn-primary">Continue Shopping</a>
        </div>
      </div>
    </div>
  </main>

  <?php
    if ($logged_in) {
      include("include/private_footer.php");
    } else {
      include("include/public_footer.php");
    }
  ?>
  
</body>
</html>
