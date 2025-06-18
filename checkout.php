<?php
include("db.php");

// 1) Require login
if (empty($sessionID)) {
    header("Location: login.php");
    exit;
}

// 2) Pull cart
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: browse_menu.php");
    exit;
}

// 3) Fetch item details with images
$ids = implode(',', array_map('intval', array_keys($cart)));
$itemQ = "SELECT id, name, price, image_url FROM menu_items WHERE id IN ($ids)";
$itemR = mysqli_query($con, $itemQ);

$items = [];
$total_amount = 0;
while ($row = mysqli_fetch_assoc($itemR)) {
    $id   = $row['id'];
    $qty  = $cart[$id];
    $sub  = $row['price'] * $qty;
    $total_amount += $sub;
    $items[$id] = [
        'name'       => $row['name'],
        'unit_price' => $row['price'],
        'quantity'   => $qty,
        'subtotal'   => $sub,
        'image'      => $row['image_url']
    ];
}

// 4) Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $address = mysqli_real_escape_string($con, trim($_POST['address']));
    $phone   = mysqli_real_escape_string($con, trim($_POST['phone']));

    if (empty($address) || empty($phone)) {
        $error = "Please enter both delivery address and phone.";
    } else {
        // Insert into orders
        $user_id = intval($sessionID);
        $date    = date('Y-m-d H:i:s');
        $insQ    = "INSERT INTO orders (user_id, order_date, status, total_amount) 
                    VALUES ($user_id, '$date', 'pending', $total_amount)";
        if (mysqli_query($con, $insQ)) {
            $order_id = mysqli_insert_id($con);
            // Insert each order_item
            foreach ($items as $mid => $it) {
                $up = $it['unit_price'];
                $qt = $it['quantity'];
                $tp = $it['subtotal'];
                $oiQ = "INSERT INTO order_items 
                        (order_id, menu_item_id, quantity, unit_price, total_price)
                        VALUES ($order_id, $mid, $qt, $up, $tp)";
                mysqli_query($con, $oiQ);
            }
            // Clear cart
            unset($_SESSION['cart']);
            header("Location: order_success.php?order_id=$order_id");
            exit;
        } else {
            $error = "Failed to place order: " . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Checkout – Restaurant</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    .product-img { width: 60px; height:60px; object-fit: contain; }
    .qty-cell { width: 80px; text-align: center; }
    .payment-img { max-width: 200px; margin-bottom: 1rem; }
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
          <li><a href="about.php">About Us</a></li>
          <li><a href="browse_menu.php">Menu</a></li>
          <li><a href="cart.php">Cart <i class="bi bi-cart3"></i></a></li>
          <li><a href="account.php">My Account</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
        <i class="mobile-nav-toggle bi bi-list"></i>
      </nav>
    </div>
  </header>

  <main class="main py-5">
    <div class="container">

      <h2 class="mb-4">Checkout</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Order Summary -->
      <div class="table-responsive mb-4">
        <table class="table align-middle">
          <thead>
            <tr>
              <th></th>
              <th>Item</th>
              <th class="qty-cell">Qty</th>
              <th>Unit Price</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td>
                  <?php if ($it['image'] && file_exists("assets/img/menu_items/{$it['image']}")): ?>
                    <img src="assets/img/menu_items/<?php echo htmlspecialchars($it['image']); ?>" class="product-img" alt="">
                  <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center product-img">
                      <i class="bi bi-image text-muted"></i>
                    </div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($it['name']) ?></td>
                <td class="qty-cell"><?= $it['quantity'] ?></td>
                <td>Rs. <?= number_format($it['unit_price'],0) ?></td>
                <td>Rs. <?= number_format($it['subtotal'],0) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="4" class="text-end">Total:</th>
              <th>Rs. <?= number_format($total_amount,0) ?></th>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Delivery & Payment Form -->
      <form method="POST" class="row g-3">
        <!-- Delivery Details -->
        <div class="col-md-8">
          <label class="form-label" for="address">Delivery Address</label>
          <textarea id="address" name="address" class="form-control" rows="2" required><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="phone">Contact Phone</label>
          <input id="phone" name="phone" type="text" class="form-control" required
            value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
        </div>

        <!-- Payment Section -->
        <div class="col-12 mt-4">
          <h4>Payment Information</h4>
          <img src="assets/img/cards.png" alt="Accepted Cards" class="payment-img">
        </div>
        <div class="col-md-6">
          <label for="card_number" class="form-label">Card Number</label>
          <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456">
        </div>
        <div class="col-md-6">
          <label for="card_name" class="form-label">Name on Card</label>
          <input type="text" id="card_name" name="card_name" class="form-control" placeholder="Cardholder Name">
        </div>
        <div class="col-md-4">
          <label for="expiry" class="form-label">Expiry Date</label>
          <input type="text" id="expiry" name="expiry" class="form-control" placeholder="MM/YY">
        </div>
        <div class="col-md-2">
          <label for="cvv" class="form-label">CVV</label>
          <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123">
        </div>
        <div class="col-12 d-flex justify-content-between mt-4">
          <a href="browse_menu.php" class="btn btn-outline-primary">Add More Items</a>
          <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
        </div>
      </form>

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
