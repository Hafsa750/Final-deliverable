<?php
include("../db.php");

// Require login
if (empty($sessionID)) {
    header("Location: ../login.php");
    exit;
}

// Pagination setup
$ORDERS_PER_PAGE = 50;
$page = isset($_GET['page']) ? max(1,intval($_GET['page'])) : 1;
$offset = ($page - 1) * $ORDERS_PER_PAGE;

// Count total orders for this user
$countQ = "SELECT COUNT(*) AS cnt FROM orders WHERE user_id = $sessionID";
$countR = mysqli_query($con, $countQ);
$totalOrders = mysqli_fetch_assoc($countR)['cnt'];
$totalPages  = ceil($totalOrders / $ORDERS_PER_PAGE);

// Fetch paginated orders
$ordQ = "
  SELECT id, order_date, status, total_amount
  FROM orders
  WHERE user_id = $sessionID
  ORDER BY order_date DESC
  LIMIT $ORDERS_PER_PAGE OFFSET $offset
";
$ordR = mysqli_query($con, $ordQ);
$orders = [];
while ($o = mysqli_fetch_assoc($ordR)) {
    $orders[] = $o;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Orders History - Restaurant</title>
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
  <style>
    .order-card {
      margin-bottom: 1.5rem;
    }
    .order-card .card-body {
      padding: 1.5rem;
    }
    .product-img {
      width: 50px;
      height: 50px;
      object-fit: contain;
    }
  </style>
</head>
<body class="starter-page-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <h1 class="sitename"><span> Restaurant </span> Customer </h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="../browse_menu.php">Browse Menu</a></li>
          <li><a href="orders.php" class="active">Orders</a></li>
          <li><a href="table_reservations.php">Table Reservations</a></li>
          <li><a href="support.php">Support</a></li>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="../logout.php">Logout</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title" data-aos="fade">
      <div class="heading">
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8">
              <h2 class="m-3">Your Orders</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders List -->
    <div class="container-fluid px-3 px-md-5">
      <?php if (empty($orders)): ?>
        <div class="alert alert-info text-center">You have not placed any orders yet.</div>
      <?php else: ?>
        <?php foreach ($orders as $order): ?>
          <?php
            // Fetch items for this order
            $oid = intval($order['id']);
            $itemQ = "
              SELECT oi.quantity, oi.unit_price, oi.total_price, mi.name, mi.image_url
              FROM order_items oi
              JOIN menu_items mi ON oi.menu_item_id = mi.id
              WHERE oi.order_id = $oid
            ";
            $itemR = mysqli_query($con, $itemQ);
            $items = [];
            while ($it = mysqli_fetch_assoc($itemR)) {
              $items[] = $it;
            }
          ?>
          <div class="card order-card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-3">
                <div>
                  <h5>Order #<?php echo $order['id']; ?></h5>
                  <small class="text-muted">Placed: <?php echo date('d-m-Y H:i:s', strtotime($order['order_date'])); ?></small>
                </div>
                <div class="text-end">
                  <span class="badge bg-info"><?php echo ucfirst($order['status']); ?></span><br>
                  <strong>Total: Rs. <?php echo number_format($order['total_amount'],0); ?></strong>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
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
                    <?php foreach ($items as $it): ?>
                      <tr>
                        <td>
                          <?php if ($it['image_url'] && file_exists("../assets/img/menu_items/{$it['image_url']}")): ?>
                            <img src="../assets/img/menu_items/<?php echo htmlspecialchars($it['image_url']); ?>" class="product-img" alt="">
                          <?php else: ?>
                            <i class="bi bi-image fs-3 text-muted"></i>
                          <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($it['name']); ?></td>
                        <td class="text-center"><?php echo $it['quantity']; ?></td>
                        <td>Rs. <?php echo number_format($it['unit_price'],0); ?></td>
                        <td class="text-end">Rs. <?php echo number_format($it['total_price'],0); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <nav aria-label="Order history pagination">
            <ul class="pagination justify-content-center">
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?php if ($p === $page) echo 'active'; ?>">
                  <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>

  </main>

  <?php
    if ($logged_in) {
      include("../include/dashboard_footer.php");
    } else {
      session_destroy();
      header("refresh: 0; URL = ../login.php");
    }
  ?>
</body>
</html>
