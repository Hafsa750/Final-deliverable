<?php
include("db.php");

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = intval($_POST['item_id']);
    if (!$logged_in) {
        echo "<script>window.location='login.php';</script>";
        exit;
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]++;
        } else {
            $_SESSION['cart'][$item_id] = 1;
        }
        $_SESSION['success_message'] = "Item added to cart.";
        echo "<script>window.location='browse_menu.php';</script>";
        exit;
    }
}

// Grab any flash message
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

// Fetch categories
$categories = [];
$catQ = "SELECT id, name FROM menu_categories ORDER BY name ASC";
$catR = mysqli_query($con, $catQ);
while ($row = mysqli_fetch_assoc($catR)) {
    $categories[] = $row;
}

// Handle filters
$filter_cat   = isset($_GET['category'])    ? intval($_GET['category'])       : 0;
$search_term  = isset($_GET['search'])      ? trim($_GET['search'])           : '';
$price_min    = isset($_GET['price_min'])   ? floatval($_GET['price_min'])    : 0;
$price_max    = isset($_GET['price_max'])   ? floatval($_GET['price_max'])    : 0;
$sort_option  = isset($_GET['sort'])        ? $_GET['sort']                   : '';

$where = [];
if ($filter_cat > 0) {
    $where[] = "mi.category_id = $filter_cat";
}
if ($search_term !== '') {
    $safe = mysqli_real_escape_string($con, $search_term);
    $where[] = "(mi.name LIKE '%$safe%' OR mi.description LIKE '%$safe%')";
}
if ($price_min > 0) {
    $where[] = "mi.price >= $price_min";
}
if ($price_max > 0 && $price_max >= $price_min) {
    $where[] = "mi.price <= $price_max";
}
$where_sql = $where ? "WHERE " . implode(' AND ', $where) : '';

// Determine ORDER BY
$order_by = "mi.name ASC";
if ($sort_option === 'price_asc') {
    $order_by = "mi.price ASC";
} elseif ($sort_option === 'price_desc') {
    $order_by = "mi.price DESC";
}

// Fetch items
$itemQ = "
  SELECT mi.*, mc.name AS category_name
  FROM menu_items mi
  JOIN menu_categories mc ON mi.category_id = mc.id
  $where_sql
  ORDER BY $order_by
";
$itemR = mysqli_query($con, $itemQ);
$items = [];
while ($row = mysqli_fetch_assoc($itemR)) {
    $items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Browse Menu - Restaurant</title>
  <meta name="description" content="Our AI-powered chatbot helps you browse the menu, make reservations, place orders, and get support—all in one place.">
  <meta name="keywords" content="Restaurant, Chatbot, AI, Reservations, Menu, Orders, Customer Support">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Poppins&family=Raleway&display=swap" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    .menu-card img {
      width: 100%;
      height: auto;
      max-height: 180px;
      object-fit: contain;
    }
    .filter-form .form-control,
    .filter-form .form-select {
      max-width: 200px;
    }
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
          <li><a href="browse_menu.php" class="active">Browse Menu</a></li>
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
              <a href="cart.php" title="Cart"><i class="bi bi-cart3"></i></a>
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

  <main class="main py-4">
    <div class="container">

      <!-- Reservation Button -->
      <div class="d-flex justify-content-end mb-4">
        <a href="<?php echo $reservation_link; ?>" class="btn btn-success btn-lg">
          <i class="bi bi-calendar2-plus me-2"></i>Reservation Table
        </a>
      </div>

      <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
      <?php endif; ?>

      <!-- Filters -->
      <form method="GET" class="row g-3 align-items-end mb-4 filter-form">
        <div class="col-auto">
          <label for="category" class="form-label">Category</label>
          <select name="category" id="category" class="form-select">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?php echo $c['id']; ?>" <?php if ($c['id']==$filter_cat) echo 'selected'; ?>>
                <?php echo htmlspecialchars($c['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <label for="search" class="form-label">Search</label>
          <input type="text" name="search" id="search" class="form-control" 
                 value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Name or description">
        </div>
        <div class="col-auto">
          <label for="price_min" class="form-label">Min Price</label>
          <input type="number" step="1" min="0" name="price_min" id="price_min" class="form-control" 
                 value="<?php echo $price_min ?: ''; ?>" placeholder="0">
        </div>
        <div class="col-auto">
          <label for="price_max" class="form-label">Max Price</label>
          <input type="number" step="1" min="0" name="price_max" id="price_max" class="form-control" 
                 value="<?php echo $price_max ?: ''; ?>" placeholder="0">
        </div>
        <div class="col-auto">
          <label for="sort" class="form-label">Sort By</label>
          <select name="sort" id="sort" class="form-select">
            <option value="">Name A–Z</option>
            <option value="price_asc" <?php if ($sort_option==='price_asc') echo 'selected'; ?>>Price: Low to High</option>
            <option value="price_desc" <?php if ($sort_option==='price_desc') echo 'selected'; ?>>Price: High to Low</option>
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="browse_menu.php" class="btn btn-secondary">Clear</a>
        </div>
      </form>

      <!-- Menu Items Grid -->
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
        <?php if (count($items)): ?>
          <?php foreach ($items as $itm): ?>
            <div class="col">
              <div class="card h-100 menu-card shadow-sm">
                <?php if ($itm['image_url'] && file_exists("assets/img/menu_items/".$itm['image_url'])): ?>
                  <img src="assets/img/menu_items/<?php echo htmlspecialchars($itm['image_url']); ?>" 
                       class="card-img-top" alt="<?php echo htmlspecialchars($itm['name']); ?>">
                <?php else: ?>
                  <div class="bg-light d-flex align-items-center justify-content-center" style="height:180px;">
                    <span class="text-muted">No Image</span>
                  </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?php echo htmlspecialchars($itm['name']); ?></h5>
                  <p class="card-text mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($itm['category_name']); ?></p>
                  <p class="card-text mb-2"><?php echo nl2br(htmlspecialchars($itm['description'])); ?></p>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="fs-5">Rs. <?php echo number_format($itm['price'],0); ?></span>
                    <form method="POST" class="m-0">
                      <input type="hidden" name="item_id" value="<?php echo $itm['id']; ?>">
                      <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm" 
                              title="Add to cart">
                        <i class="bi bi-cart-plus"></i>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col">
            <p class="text-center">No items found.</p>
          </div>
        <?php endif; ?>
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
