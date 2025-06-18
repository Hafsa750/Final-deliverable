<?php
include("db.php");

$email = $password = $role = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize POST data
    $email = htmlspecialchars(trim($_POST['login_input']));
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Basic validation: Check if all required fields are filled
    if (empty($email) || empty($password) || empty($role)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Query the users table for a matching record with the given email and role
        $query = "SELECT * FROM `users` WHERE email = '$email' AND role = '$role'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify the provided password with the hashed password stored in the DB
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION["sessionID"]   = $user['id'];
                $_SESSION["sessionName"] = $user['name'];
                $_SESSION["sessionRole"] = $user['role'];

                // Redirect to the appropriate dashboard based on role
                if ($role === "admin") {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: user/dashboard.php");
                }
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email, password, or role.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login - Restaurant Chatbot</title>
  <meta name="description" content="Login to your Restaurant Chatbot account">
  <meta name="keywords" content="Restaurant, Chatbot, Login">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
      <div class="container section-title" data-aos="fade-up" style="padding-bottom: 0px;">
        <h1>Login</h1>
        <p>Login to your account</p>
        <?php
          if (!empty($error_message)) {
              echo "<div class='alert alert-danger'>$error_message</div>";
          }
        ?>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100" style="max-width: 30%;">
        <!-- Login Form -->
        <form method="POST" action="login.php" class="needs-validation" novalidate>
          <div class="form-group mb-3">
            <label for="login_input">Email</label>
            <input type="text" class="form-control form-control-lg" id="login_input" name="login_input" placeholder="Enter your email" value="<?php echo $email; ?>" autofocus required>
          </div>
          <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password" required>
          </div>
          <div class="form-group mb-3">
            <label>Role</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="role" id="role_customer" value="customer" <?php if($role==='customer') echo 'checked'; ?> checked required>
              <label class="form-check-label" for="role_customer">Customer</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="role" id="role_admin" value="admin" <?php if($role==='admin') echo 'checked'; ?> required>
              <label class="form-check-label" for="role_admin">Admin</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button>
        </form>
        <div class="mt-3">
          <a href="reset_password.php">Forgot Password?</a>
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
