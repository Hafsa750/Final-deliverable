<?php
include("../db.php");

$user_id = $_SESSION["sessionID"];
$success_message = "";
$error_message = "";

// Process Profile Details Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile_details'])) {
    $name  = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));

    if (empty($name) || empty($email) || empty($phone)) {
        $error_message = "Name, Email, and Phone are required.";
    } else {
        // Check for duplicate email (ignoring current user)
        $checkQuery = "SELECT * FROM `users` WHERE email = '$email' AND id != '$user_id'";
        $result     = mysqli_query($con, $checkQuery);
        if (mysqli_num_rows($result) > 0) {
            $error_message = "The email address is already in use by another account.";
        } else {
            $updateQuery = "UPDATE `users` 
                            SET name = '$name', email = '$email', phone = '$phone' 
                            WHERE id = '$user_id'";
            if (mysqli_query($con, $updateQuery)) {
                $success_message = "Profile details updated successfully.";
            } else {
                $error_message = "Error updating profile details: " . mysqli_error($con);
            }
        }
    }
}

// Process Password Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $old_password     = $_POST['old_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Please fill in all password fields.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } else {
        // Re-fetch user to get latest password hash
        $user_query  = "SELECT * FROM `users` WHERE id = '$user_id'";
        $user_result = mysqli_query($con, $user_query);
        $user        = mysqli_fetch_assoc($user_result);
        if (!password_verify($old_password, $user['password'])) {
            $error_message = "Old password is incorrect.";
        } else {
            $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $updateQuery   = "UPDATE `users` SET password = '$password_hash' WHERE id = '$user_id'";
            if (mysqli_query($con, $updateQuery)) {
                $success_message = "Password updated successfully.";
            } else {
                $error_message = "Error updating password: " . mysqli_error($con);
            }
        }
    }
}

// Fetch user profile data from `users` table
$user_query  = "SELECT * FROM `users` WHERE id = '$user_id'";
$user_result = mysqli_query($con, $user_query);
$user        = mysqli_fetch_assoc($user_result);

// Determine if edit mode is active
$editMode = false;
if (isset($_GET['edit']) && $_GET['edit'] == '1') {
    $editMode = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Profile - Restaurant Customer</title>
  <meta name="description" content="Update your profile for Restaurant Chatbot">
  <meta name="keywords" content="Restaurant Chatbot, Profile">
  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">
  <style>
      .card {
          background-color: #ffffff;
          border-radius: 10px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          padding: 20px;
          margin: 20px auto;
          max-width: 650px;
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
          <li><a href="orders.php">Orders</a></li>
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
      <div class="heading" style="padding-bottom: 0px;">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">
              <h1>Profile</h1>
              <p>Update your profile information</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Display success or error messages -->
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <?php
      if (!empty($success_message)) {
          echo "<div class='alert alert-success'>$success_message</div>";
      }
      if (!empty($error_message)) {
          echo "<div class='alert alert-danger'>$error_message</div>";
      }
      ?>
      
      <!-- Profile Details Section -->
      <?php if (!$editMode): ?>
      <div class="card">
        <h2>Profile Details</h2>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <a href="profile.php?edit=1" class="btn btn-primary">Edit Profile</a>
      </div>
      <?php else: ?>
      <div class="card">
        <h2>Edit Profile Details</h2>
        <form method="POST" action="profile.php" class="needs-validation" novalidate>
          <div class="form-group mb-3">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
          </div>
          <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>
          <div class="form-group mb-3">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
          </div>
          <button type="submit" name="update_profile_details" class="btn btn-primary btn-block">Update Profile</button>
          <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
      </div>
      <?php endif; ?>

      <!-- Password Change Section -->
      <div class="card">
        <h2>Change Password</h2>
        <form method="POST" action="profile.php" class="needs-validation" novalidate>
          <div class="form-group mb-3">
            <label for="old_password">Old Password</label>
            <input type="password" class="form-control" id="old_password" name="old_password" required>
          </div>
          <div class="form-group mb-3">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
          </div>
          <div class="form-group mb-3">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
          </div>
          <button type="submit" name="update_password" class="btn btn-primary btn-block">Update Password</button>
        </form>
      </div>
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
