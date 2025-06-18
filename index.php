<?php
  include("db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Restaurant Chatbot</title>
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
    .nav-cart .bi-cart3 {
      font-size: 1.6rem;
    }
  </style>
</head>
<body class="index-page">
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <h1 class="sitename"><span>Restaurant</span> Chatbot</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active">Home</a></li>
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

  <main class="main">
    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">
      <div class="container position-relative" data-aos="fade-down" data-aos-delay="100">
        <div class="row gy-5">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h2>Welcome to Restaurant Chatbot</h2>
            <p>
              Interact with our AI-powered assistant to browse the menu, make reservations, place orders, or get support—right here on our website.
            </p>
            <div class="d-flex">
              <a href="login.php" class="btn-get-started">Get Started</a>
            </div>
          </div>
          <div class="col-lg-6 order-1 order-lg-2">
            <img src="assets/img/cover.png" class="img-fluid" alt="Restaurant Chatbot">
          </div>
        </div>
      </div>
      <div class="icon-boxes position-relative" data-aos="fade-up" data-aos-delay="200">
        <div class="container position-relative">
          <div class="row gy-4 mt-5">
            <div class="col-xl-3 col-md-6">
              <div class="icon-box">
                <div class="icon"><i class="bi bi-card-list"></i></div>
                <h4 class="title"><a href="#" class="stretched-link">Browse Menu</a></h4>
                <p>View appetizers, mains, desserts, and more via chatbot.</p>
              </div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="icon-box">
                <div class="icon"><i class="bi bi-calendar-check"></i></div>
                <h4 class="title"><a href="#" class="stretched-link">Make Reservation</a></h4>
                <p>Book a table quickly by selecting date, time, and party size.</p>
              </div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="icon-box">
                <div class="icon"><i class="bi bi-bag-fill"></i></div>
                <h4 class="title"><a href="#" class="stretched-link">Place Order</a></h4>
                <p>Order your favorite dishes for dine-in or delivery.</p>
              </div>
            </div>
            <div class="col-xl-3 col-md-6">
              <div class="icon-box">
                <div class="icon"><i class="bi bi-question-circle"></i></div>
                <h4 class="title"><a href="#" class="stretched-link">Customer Support</a></h4>
                <p>Get instant answers to FAQs or connect with support staff.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Hero Section -->
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
