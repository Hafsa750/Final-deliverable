<?php 
include("db.php");

$name      = $email = $phone = $password = "";
$error_message   = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize POST data
    $name     = htmlspecialchars(trim($_POST['name']));
    $email    = htmlspecialchars(trim($_POST['email']));
    $phone    = htmlspecialchars(trim($_POST['phone']));
    $password = $_POST['password'];
    
    // Basic validation: Check if required fields are empty
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Hash the password using bcrypt
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists in the users table
        $checkQuery = "SELECT * FROM `users` WHERE email = '$email'";
        $result = mysqli_query($con, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "Error: The email address '$email' is already registered.";
        } else {
            // Insert new user record with Role set as 'customer'
            $query = "INSERT INTO `users` (name, email, password, phone, role) 
                      VALUES ('$name', '$email', '$password_hash', '$phone', 'customer')";

            if (mysqli_query($con, $query)) {
                $success_message = "Registration successful.";
                // Clear input fields upon successful registration
                $name = $email = $phone = $password = "";
            } else {
                $error_message = "Error: " . mysqli_error($con);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Register - Restaurant Chatbot</title>
  <meta name="description" content="Register for Restaurant Chatbot">
  <meta name="keywords" content="Restaurant, Chatbot, Register">
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
            <li><a href="register.php" class="active">Register</a></li>
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
    <section class="section">
      <div class="container section-title" style="padding-bottom: 0px;" data-aos="fade-down" data-aos-delay="100">
        <h1>Register</h1>
        <h5>Create Your Account</h5>
        <?php
          if (!empty($success_message)) {
              echo "<div class='alert alert-success'>$success_message</div>";
          }
          if (!empty($error_message)) {
              echo "<div class='alert alert-danger'>$error_message</div>";
          }
        ?>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100" style="max-width: 30%;">
        <!-- Registration Form -->
        <form method="POST" action="register.php" class="needs-validation" novalidate>
          <div class="form-group mb-3">
            <label for="name">Full Name</label>
            <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Enter your full name" value="<?php echo $name; ?>" required>
          </div>
          <div class="form-group mb-3">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control form-control-lg" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo $phone; ?>" required>
          </div>
          <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" required>
          </div>
          <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">Register</button>
        </form>
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
