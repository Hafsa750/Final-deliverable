<?php
include("../db.php");

// Redirect to login if not admin
if ($sessionRole !== 'admin') {
    header("Location: login.php");
    exit;
}

$success_message = "";
$error_message   = "";

// HANDLE ADD SETTING
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_setting'])) {
    $max_tables        = intval($_POST['max_tables']);
    $slot_duration_mins = intval($_POST['slot_duration_mins']);

    if ($max_tables <= 0 || $slot_duration_mins <= 0) {
        $error_message = "Both Max Tables and Slot Duration must be positive numbers.";
    } else {
        $insertQ = "INSERT INTO `reservation_settings` (max_tables, slot_duration_mins) VALUES ($max_tables, $slot_duration_mins)";
        if (mysqli_query($con, $insertQ)) {
            $success_message = "Reservation setting added successfully.";
        } else {
            $error_message = "Error adding setting: " . mysqli_error($con);
        }
    }
}

// HANDLE UPDATE SETTING
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_setting'])) {
    $id                 = intval($_POST['setting_id']);
    $max_tables         = intval($_POST['max_tables']);
    $slot_duration_mins = intval($_POST['slot_duration_mins']);

    if ($id <= 0 || $max_tables <= 0 || $slot_duration_mins <= 0) {
        $error_message = "All fields are required and must be valid.";
    } else {
        $updateQ = "UPDATE `reservation_settings`
                    SET max_tables = $max_tables, slot_duration_mins = $slot_duration_mins
                    WHERE id = $id";
        if (mysqli_query($con, $updateQ)) {
            $success_message = "Reservation setting updated successfully.";
        } else {
            $error_message = "Error updating setting: " . mysqli_error($con);
        }
    }
}

// HANDLE DELETE SETTING
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id > 0) {
        $deleteQ = "DELETE FROM `reservation_settings` WHERE id = $delete_id";
        if (mysqli_query($con, $deleteQ)) {
            $success_message = "Reservation setting deleted successfully.";
        } else {
            $error_message = "Error deleting setting: " . mysqli_error($con);
        }
    }
}

// FETCH ALL SETTINGS
$settingQuery  = "SELECT * FROM `reservation_settings` ORDER BY id DESC";
$settingResult = mysqli_query($con, $settingQuery);
$settings      = [];
while ($row = mysqli_fetch_assoc($settingResult)) {
    $settings[] = $row;
}

// DETERMINE EDIT MODE
$edit_id      = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_setting = null;
if ($edit_id) {
    foreach ($settings as $s) {
        if ($s['id'] == $edit_id) {
            $edit_setting = $s;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Manage Reservation Settings - Restaurant Admin</title>
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
          <li><a href="orders.php" class="active">Reservations</a></li>
          <li><a href="faqs.php">FAQs</a></li>
          <li><a href="business.php">Busineses</a></li>
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

              <h1 class="m-3">Manage Reservation Settings</h1>

              <!-- Success / Error Messages -->
              <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
              <?php endif; ?>
              <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
              <?php endif; ?>

              <!-- ADD SETTING BUTTON -->
              <?php if (!$edit_setting): ?>
                <div class="mb-3">
                  <button id="showAddFormBtn" class="btn btn-primary">Add New Setting</button>
                </div>
              <?php endif; ?>

              <!-- ADD SETTING FORM (HIDDEN BY DEFAULT) -->
              <div id="addForm" class="card p-4 form-container" style="display: none;">
                <h4>Add New Setting</h4>
                <form method="POST" action="reservation_settings.php">
                  <div class="mb-3">
                    <label for="max_tables" class="form-label">Max Tables</label>
                    <input type="number" class="form-control" id="max_tables" name="max_tables" required>
                  </div>
                  <div class="mb-3">
                    <label for="slot_duration_mins" class="form-label">Slot Duration (mins)</label>
                    <input type="number" class="form-control" id="slot_duration_mins" name="slot_duration_mins" required>
                  </div>
                  <button type="submit" name="add_setting" class="btn btn-success me-2" onclick="return confirm('Add this setting?');">Save</button>
                  <button type="button" id="cancelAddBtn" class="btn btn-secondary">Cancel</button>
                </form>
              </div>

              <!-- EDIT SETTING FORM -->
              <?php if ($edit_setting): ?>
                <div id="editForm" class="card p-4 form-container">
                  <h4>Edit Setting (ID: <?php echo $edit_setting['id']; ?>)</h4>
                  <form method="POST" action="reservation_settings.php?edit_id=<?php echo $edit_setting['id']; ?>">
                    <input type="hidden" name="setting_id" value="<?php echo $edit_setting['id']; ?>">
                    <div class="mb-3">
                      <label for="edit_max_tables" class="form-label">Max Tables</label>
                      <input type="number" class="form-control" id="edit_max_tables" name="max_tables"
                             value="<?php echo $edit_setting['max_tables']; ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_slot_duration_mins" class="form-label">Slot Duration (mins)</label>
                      <input type="number" class="form-control" id="edit_slot_duration_mins" name="slot_duration_mins"
                             value="<?php echo $edit_setting['slot_duration_mins']; ?>" required>
                    </div>
                    <button type="submit" name="update_setting" class="btn btn-warning me-2" onclick="return confirm('Save changes?');">Update</button>
                    <a href="reservation_settings.php" class="btn btn-secondary">Cancel</a>
                  </form>
                </div>
              <?php endif; ?>

            </div> <!-- .col-lg-8 -->
          </div> <!-- .row -->
        </div> <!-- .container -->
      </div> <!-- .heading -->
    </div> <!-- .page-title -->

    <!-- SETTINGS LIST -->
    <div class="container-fluid mt-1 p-5">
      <?php if (count($settings) > 0): ?>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Max Tables</th>
              <th>Slot Duration (mins)</th>
              <th>Created At</th>
              <th>Updated At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($settings as $s): ?>
              <tr>
                <td><?php echo $s['id']; ?></td>
                <td><?php echo $s['max_tables']; ?></td>
                <td><?php echo $s['slot_duration_mins']; ?></td>
                <td><?php echo date('d-m-Y H:i:s', strtotime($s['created_at'])); ?></td>
                <td><?php echo date('d-m-Y H:i:s', strtotime($s['updated_at'])); ?></td>
                <td>
                  <a href="reservation_settings.php?edit_id=<?php echo $s['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                  <a href="reservation_settings.php?delete_id=<?php echo $s['id']; ?>"
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('Are you sure you want to delete this setting?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-center">No reservation settings found.</p>
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
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const addForm      = document.getElementById('addForm');
      const showAddBtn   = document.getElementById('showAddFormBtn');
      const cancelAddBtn = document.getElementById('cancelAddBtn');

      if (showAddBtn) {
        showAddBtn.addEventListener('click', () => {
          addForm.style.display = 'block';
          showAddBtn.style.display = 'none';
        });
      }
      if (cancelAddBtn) {
        cancelAddBtn.addEventListener('click', () => {
          addForm.style.display = 'none';
          showAddBtn.style.display = 'inline-block';
        });
      }

      // If edit mode is active, display edit form and hide add button
      <?php if ($edit_setting): ?>
        document.getElementById('editForm').style.display = 'block';
        if (showAddBtn) showAddBtn.style.display = 'none';
      <?php endif; ?>
    });
  </script>
</body>
</html>
