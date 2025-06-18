<?php
include("db.php");

// Handle password reset request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);

    // Check if the email exists in the users table
    $user_query = "SELECT UserID FROM `users` WHERE Email='$email'";
    $user_result = mysqli_query($con, $user_query);

    if (mysqli_num_rows($user_result) > 0) {
        $user = mysqli_fetch_assoc($user_result);
        $user_id = $user['UserID'];

        // Generate a unique reset token
        $reset_token = bin2hex(random_bytes(32));
        // For demonstration, set token expiry 1 hour from now
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $message = "<div class='card p-4 text-center'>
                        <h5 class='card-title'>Reset Password</h5>
                        <p class='card-text' style='color: green;'>If the provided email is correct, you will receive an email with password reset instructions.</p>
                        <p>For testing purposes, use the following link to reset your password:</p>
                        <a href='#' class='btn btn-primary'>Reset Password Link</a>
                    </div>";
    } else {
        // Email not found
        $message = "<div class='card mt-4 p-4 text-center'>
                        <h5 class='card-title'>Reset Password</h5>
                        <p class='card-text' style='color: red;'>The email address provided is not registered in our system.</p>
                    </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Restaurant - Reset Password</title>
  <meta name="description" content="Reset your password for the Restaurant.">
  <meta name="keywords" content="Restaurant, Reset Password, Virtual University, Sports">
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
            <li><a href="login.php" class="active">Login</a></li>
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
    <section class="section">
        <div class="container section-title" data-aos="fade-up">
            <h1>Reset Password</h1>
            <p>Enter your registered email address to receive password reset instructions.</p>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100" style="max-width: 40%;">
            <!-- Display Message -->
            <?php if (isset($message)) echo $message; ?>
            <!-- Password Reset Form -->
            <form method="POST" action="reset_password.php" class="needs-validation" novalidate>
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Enter your registered email" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Send Reset Instructions</button>
            </form>
            <div class="mt-3">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </section>
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
