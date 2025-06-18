<?php
include("../db.php");

$success_message = "";
$error_message   = "";

// FETCH EXISTING BUSINESS DETAILS (there should be at most one row with id=1)
$business = null;
$fetchQ   = "SELECT * FROM `business_details` WHERE id = 1";
$fetchRes = mysqli_query($con, $fetchQ);
if ($fetchRes && mysqli_num_rows($fetchRes) > 0) {
    $business = mysqli_fetch_assoc($fetchRes);
}

// HANDLE ADD BUSINESS DETAILS
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_business'])) {
    $restaurant_name = htmlspecialchars(trim($_POST['restaurant_name']));
    $address         = htmlspecialchars(trim($_POST['address']));
    $phone           = htmlspecialchars(trim($_POST['phone']));
    $email           = htmlspecialchars(trim($_POST['email']));
    $open_time       = $_POST['open_time'];
    $close_time      = $_POST['close_time'];

    if (empty($restaurant_name) || empty($address) || empty($phone) || empty($email) || empty($open_time) || empty($close_time)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address.";
    } else {
        $insertQ = "INSERT INTO `business_details` 
                    (id, restaurant_name, address, phone, email, open_time, close_time) 
                    VALUES 
                    (1, '$restaurant_name', '$address', '$phone', '$email', '$open_time', '$close_time')";
        if (mysqli_query($con, $insertQ)) {
            $success_message = "Business details added successfully.";
            header("Location: business.php");
            exit;
        } else {
            $error_message = "Error adding business details: " . mysqli_error($con);
        }
    }
}

// HANDLE UPDATE BUSINESS DETAILS
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_business'])) {
    $restaurant_name = htmlspecialchars(trim($_POST['restaurant_name']));
    $address         = htmlspecialchars(trim($_POST['address']));
    $phone           = htmlspecialchars(trim($_POST['phone']));
    $email           = htmlspecialchars(trim($_POST['email']));
    $open_time       = $_POST['open_time'];
    $close_time      = $_POST['close_time'];

    if (empty($restaurant_name) || empty($address) || empty($phone) || empty($email) || empty($open_time) || empty($close_time)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address.";
    } else {
        $updateQ = "UPDATE `business_details` 
                    SET restaurant_name = '$restaurant_name',
                        address = '$address',
                        phone = '$phone',
                        email = '$email',
                        open_time = '$open_time',
                        close_time = '$close_time'
                    WHERE id = 1";
        if (mysqli_query($con, $updateQ)) {
            $success_message = "Business details updated successfully.";
            header("Location: business.php");
            exit;
        } else {
            $error_message = "Error updating business details: " . mysqli_error($con);
        }
    }
}

// HANDLE DELETE BUSINESS DETAILS
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id === 1) {
        $deleteQ = "DELETE FROM `business_details` WHERE id = 1";
        if (mysqli_query($con, $deleteQ)) {
            $success_message = "Business details deleted successfully.";
            header("Location: business.php");
            exit;
        } else {
            $error_message = "Error deleting business details: " . mysqli_error($con);
        }
    }
}

// REFRESH $business AFTER ANY CHANGE
$business = null;
$fetchRes = mysqli_query($con, $fetchQ);
if ($fetchRes && mysqli_num_rows($fetchRes) > 0) {
    $business = mysqli_fetch_assoc($fetchRes);
}

// DETERMINE IF EDIT MODE (via ?edit=1)
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 1 && $business;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Manage Business Details - Restaurant Admin</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
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
    .form-container {
      max-width: 50%;
      margin: 0 auto;
    }
    .detail-card .card-body {
      padding: 1rem;
    }
    .card-footer .small {
      font-size: 0.85rem;
    }
    input[type="time"] {
      width: 100%;
    }
  </style>
</head>
<body class="starter-page-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <h1 class="sitename"><span>Restaurant</span> Admin</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="categories.php">Categories</a></li>
          <li><a href="menu_items.php">Menu Items</a></li>
          <li><a href="orders.php">Orders</a></li>
          <li><a href="table_reservations.php">Reservations</a></li>
          <li><a href="faqs.php">FAQs</a></li>
          <li><a href="business.php" class="active">Busineses</a></li>
          <li><a href="support.php">Support</a></li>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="../logout.php">Logout</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="page-title" data-aos="fade">
      <div class="heading pb-1">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">

              <h1 class="m-3">Manage Business Details</h1>

              <!-- Success / Error Messages -->
              <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
              <?php endif; ?>
              <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
              <?php endif; ?>

              <!-- ADD BUSINESS BUTTON (shown only if no record exists) -->
              <?php if (!$business && !$edit_mode): ?>
                <div class="mb-3">
                  <button id="showAddFormBtn" class="btn btn-primary">Add Business Details</button>
                </div>
              <?php endif; ?>

              <!-- ADD BUSINESS FORM (hidden by default) -->
              <?php if (!$business || $edit_mode): ?>
                <div id="addEditForm" class="card p-4 form-container" style="<?php echo ($business && !$edit_mode) ? 'display: none;' : 'display: block;'; ?>">
                  <h4><?php echo $business && $edit_mode ? "Edit Business Details" : "Add Business Details"; ?></h4>
                  <form method="POST" action="business.php<?php echo $business && $edit_mode ? '?edit=1' : ''; ?>">
                    <div class="mb-3">
                      <label for="restaurant_name" class="form-label">Restaurant Name</label>
                      <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" 
                             value="<?php echo $business ? htmlspecialchars($business['restaurant_name']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="address" class="form-label">Address</label>
                      <input type="text" class="form-control" id="address" name="address" 
                             value="<?php echo $business ? htmlspecialchars($business['address']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="phone" class="form-label">Phone</label>
                      <input type="text" class="form-control" id="phone" name="phone" 
                             value="<?php echo $business ? htmlspecialchars($business['phone']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input type="email" class="form-control" id="email" name="email" 
                             value="<?php echo $business ? htmlspecialchars($business['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="open_time" class="form-label">Opening Time</label>
                      <input type="time" class="form-control" id="open_time" name="open_time" 
                             value="<?php echo $business ? htmlspecialchars($business['open_time']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="close_time" class="form-label">Closing Time</label>
                      <input type="time" class="form-control" id="close_time" name="close_time" 
                             value="<?php echo $business ? htmlspecialchars($business['close_time']) : ''; ?>" required>
                    </div>
                    <?php if ($business && $edit_mode): ?>
                      <button type="submit" name="update_business" class="btn btn-warning me-2" onclick="return confirm('Save changes to business details?');">Update</button>
                      <a href="business.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                      <button type="submit" name="add_business" class="btn btn-success me-2" onclick="return confirm('Add business details?');">Save</button>
                      <button type="button" id="cancelAddBtn" class="btn btn-secondary">Cancel</button>
                    <?php endif; ?>
                  </form>
                </div>
              <?php endif; ?>

            </div> <!-- end .col-lg-8 -->
          </div> <!-- end .row -->
        </div> <!-- end .container -->
      </div> <!-- end .heading -->
    </div> <!-- end .page-title -->

    <!-- DISPLAY BUSINESS DETAILS (if exists and not in edit mode) -->
    <?php if ($business && !$edit_mode): ?>
      <div class="container-fluid mt-1 p-5">
        <div class="card detail-card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3"><?php echo htmlspecialchars($business['restaurant_name']); ?></h5>
            <p class="card-text"><strong>Address:</strong> <?php echo htmlspecialchars($business['address']); ?></p>
            <p class="card-text"><strong>Phone:</strong> <?php echo htmlspecialchars($business['phone']); ?></p>
            <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($business['email']); ?></p>
            <p class="card-text">
              <strong>Operating Hours:</strong> 
              <?php echo date('h:i A', strtotime($business['open_time'])); ?> 
              to 
              <?php echo date('h:i A', strtotime($business['close_time'])); ?>
            </p>
            <div class="small text-muted mt-3">
              Created: <?php echo date('d-m-Y H:i:s', strtotime($business['created_at'])); ?><br>
              Updated: <?php echo date('d-m-Y H:i:s', strtotime($business['updated_at'])); ?>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end">
            <a href="business.php?edit=1" class="btn btn-sm btn-primary me-2">Edit</a>
            <a href="business.php?delete_id=1" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete business details?');">Delete</a>
          </div>
        </div>
      </div>
    <?php elseif (!$business && !$edit_mode): ?>
      <div class="container-fluid mt-4">
        <p class="text-center">No business details found.</p>
      </div>
    <?php endif; ?>

  </main>

  <?php
    if ($logged_in) {
      include("../include/dashboard_footer.php");
    } else {
      session_destroy();
      header("refresh: 0; URL = ../login.php");
    }
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const addEditForm    = document.getElementById('addEditForm');
      const showAddBtn     = document.getElementById('showAddFormBtn');
      const cancelAddBtn   = document.getElementById('cancelAddBtn');

      if (showAddBtn) {
        showAddBtn.addEventListener('click', () => {
          addEditForm.style.display = 'block';
          showAddBtn.style.display = 'none';
        });
      }
      if (cancelAddBtn) {
        cancelAddBtn.addEventListener('click', () => {
          addEditForm.style.display = 'none';
          if (showAddBtn) showAddBtn.style.display = 'inline-block';
        });
      }
    });
  </script>
</body>
</html>
