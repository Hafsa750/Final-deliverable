<?php
include("db.php");

// Require login to view cart
if (empty($sessionID)) {
    header("Location: login.php");
    exit;
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update quantity
    if (isset($_POST['update_qty'], $_POST['item_id'], $_POST['quantity'])) {
        $item_id = intval($_POST['item_id']);
        $qty     = max(1, intval($_POST['quantity']));
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id] = $qty;
        }
    }
    // Remove item
    if (isset($_POST['remove_item'], $_POST['item_id'])) {
        $item_id = intval($_POST['item_id']);
        unset($_SESSION['cart'][$item_id]);
    }
    header("Location: cart.php");
    exit;
}

// Compute cart contents
$cart        = $_SESSION['cart'] ?? [];
$cart_items  = [];
$total_price = 0.00;

if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $sql = "SELECT mi.id, mi.name, mi.price, mi.image_url
            FROM menu_items mi
            WHERE mi.id IN ($ids)";
    $res = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $id       = $row['id'];
        $qty      = $cart[$id];
        $subtotal = $row['price'] * $qty;
        $total_price += $subtotal;
        $cart_items[] = [
            'id'       => $id,
            'name'     => $row['name'],
            'price'    => $row['price'],
            'image'    => $row['image_url'],
            'quantity' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Your Cart - Restaurant</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    .cart-img {
      width: 60px;
      height: 60px;
      object-fit: contain;
    }
    .qty-input {
      width: 60px;
    }
  </style>
  <style>
    .badge-cart-count {
      background-color: #dc3545;
      color: #fff;
      font-size: 0.75rem;
      position: absolute;
      top: 0;
      right: 0;
      transform: translate(50%, -50%);
      border-radius: 50%;
      padding: 0.25em 0.45em;
    }
    .nav-cart {
      position: relative;
      margin-left: 1rem;
    }
    /* Enlarge the cart icon */
    .nav-cart .bi-cart3 {
      font-size: 1.6rem;
    }
  </style>
</head>
<body class="starter-page-page">
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <h1 class="sitename"><span>Restaurant</span> Chatbot</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="browse_menu.php">Browse Menu</a></li>
          <li><a href="table_reservation.php">Table Reservation</a></li>
          <li><a href="faqs.php">FAQs</a></li>
          
          <?php if ($logged_in): ?>
            <li><a href="user/dashboard.php">My Account</a></li>
            <li class="nav-cart">
              <a href="cart.php" title="Cart"><i class="bi bi-cart3"></i></a>
              <?php if ($cart_count > 0): ?>
                <span class="badge-cart-count"><?php echo $cart_count; ?></span>
              <?php endif; ?>
            </li>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
            <li class="nav-cart">
              <a href="cart.php" title="Cart" class="active"><i class="bi bi-cart3"></i></a>
              <?php if ($cart_count > 0): ?>
                <span class="badge-cart-count"><?php echo $cart_count; ?></span>
              <?php endif; ?>
            </li>
          <?php endif; ?>
          
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>

  <main class="main py-5">
    <div class="container">
      <h2 class="mb-4">Your Cart</h2>

      <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <a href="browse_menu.php" class="btn btn-primary">Add More Items</a>
      <?php else: ?>
        <div class="table-responsive mb-4">
          <table class="table align-middle">
            <thead>
              <tr>
                <th></th>
                <th>Item</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart_items as $it): ?>
                <tr>
                  <td>
                    <?php if ($it['image'] && file_exists("assets/img/menu_items/{$it['image']}")): ?>
                      <img src="assets/img/menu_items/<?php echo htmlspecialchars($it['image']); ?>" class="cart-img" alt="">
                    <?php else: ?>
                      <div class="bg-light d-flex align-items-center justify-content-center cart-img">
                        <i class="bi bi-image text-muted"></i>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($it['name']); ?></td>
                  <td>Rs. <?php echo number_format($it['price'],0); ?></td>
                  <td>
                    <form method="POST" class="d-flex align-items-center">
                      <input type="hidden" name="item_id" value="<?php echo $it['id']; ?>">
                      <input type="number" name="quantity" value="<?php echo $it['quantity']; ?>"
                             min="1" class="form-control form-control-sm qty-input me-2">
                      <button type="submit" name="update_qty" class="btn btn-sm btn-secondary">Update</button>
                    </form>
                  </td>
                  <td>Rs. <?php echo number_format($it['subtotal'],0); ?></td>
                  <td>
                    <form method="POST">
                      <input type="hidden" name="item_id" value="<?php echo $it['id']; ?>">
                      <button type="submit" name="remove_item" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" class="text-end">Total:</th>
                <th>Rs. <?php echo number_format($total_price,0); ?></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="d-flex justify-content-between">
          <a href="browse_menu.php" class="btn btn-outline-primary">Add More Items</a>
          <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </div>
      <?php endif; ?>
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
