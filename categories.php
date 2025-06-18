<?php 
include("../db.php");

$success_message = "";
$error_message = "";

// ADD CATEGORY
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $name        = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));

    if (empty($name)) {
        $error_message = "Category name is required.";
    } else {
        $checkQuery = "SELECT * FROM `menu_categories` WHERE name = '$name'";
        $result     = mysqli_query($con, $checkQuery);
        if (mysqli_num_rows($result) > 0) {
            $error_message = "Category '$name' already exists.";
        } else {
            $insertQuery = "INSERT INTO `menu_categories` (name, description) VALUES ('$name', '$description')";
            if (mysqli_query($con, $insertQuery)) {
                $success_message = "Category added successfully.";
            } else {
                $error_message = "Error adding category: " . mysqli_error($con);
            }
        }
    }
}

// UPDATE CATEGORY
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_category'])) {
    $cat_id      = intval($_POST['category_id']);
    $new_name    = htmlspecialchars(trim($_POST['name']));
    $new_desc    = htmlspecialchars(trim($_POST['description']));

    if (empty($new_name)) {
        $error_message = "Category name is required.";
    } else {
        $checkQuery = "SELECT * FROM `menu_categories` WHERE name = '$new_name' AND id != $cat_id";
        $result     = mysqli_query($con, $checkQuery);
        if (mysqli_num_rows($result) > 0) {
            $error_message = "Another category with name '$new_name' already exists.";
        } else {
            $updateQuery = "UPDATE `menu_categories` 
                            SET name = '$new_name', description = '$new_desc' 
                            WHERE id = $cat_id";
            if (mysqli_query($con, $updateQuery)) {
                $success_message = "Category updated successfully.";
            } else {
                $error_message = "Error updating category: " . mysqli_error($con);
            }
        }
    }
}

// DELETE CATEGORY
if (isset($_GET['delete_id'])) {
    $delete_id   = intval($_GET['delete_id']);
    $deleteQuery = "DELETE FROM `menu_categories` WHERE id = $delete_id";
    if (mysqli_query($con, $deleteQuery)) {
        $success_message = "Category deleted successfully.";
    } else {
        $error_message = "Error deleting category: " . mysqli_error($con);
    }
}

// FETCH ALL CATEGORIES
$categories = [];
$fetchQuery = "SELECT * FROM `menu_categories` ORDER BY id DESC";
$result     = mysqli_query($con, $fetchQuery);
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// DETERMINE IF EDIT MODE
$edit_id   = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_cat  = null;
if ($edit_id) {
    foreach ($categories as $c) {
        if ($c['id'] == $edit_id) {
            $edit_cat = $c;
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
  <title>Manage Categories - Restaurant Admin</title>
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
    /* Hide add/edit forms by default */
    #addCategoryForm, #editCategoryForm {
      display: none;
      margin-top: 20px;
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
          <li><a href="categories.php" class="active">Categories</a></li>
          <li><a href="menu_items.php">Menu Items</a></li>
          <li><a href="orders.php">Orders</a></li>
          <li><a href="table_reservations.php">Reservations</a></li>
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
    <!-- Page Title -->
    <div class="page-title" data-aos="fade">
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">
              <h1 class="m-3">Manage Categories</h1>

              <!-- Success / Error Messages -->
              <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
              <?php endif; ?>
              <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
              <?php endif; ?>

              <!-- Add New Category Button -->
              <?php if (!$edit_cat): ?>
                <button id="showAddFormBtn" class="btn btn-primary mb-3">Add New Category</button>
              <?php endif; ?>

              <!-- Add Category Form -->
              <div id="addCategoryForm" class="card p-4">
                <h4>Add New Category</h4>
                <form method="POST" action="categories.php">
                  <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                  <div class="mb-3">
                    <label for="description" class="form-label">Description (optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                  </div>
                  <button type="submit" name="add_category" class="btn btn-success" onclick="return confirm('Add this category?');">Save</button>
                  <button type="button" id="cancelAddBtn" class="btn btn-secondary">Cancel</button>
                </form>
              </div>

              <!-- Edit Category Form -->
              <?php if ($edit_cat): ?>
              <div id="editCategoryForm" class="card p-4">
                <h4>Edit Category (ID: <?php echo $edit_cat['id']; ?>)</h4>
                <form method="POST" action="categories.php?edit_id=<?php echo $edit_cat['id']; ?>">
                  <input type="hidden" name="category_id" value="<?php echo $edit_cat['id']; ?>">
                  <div class="mb-3">
                    <label for="edit_name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="edit_name" name="name" value="<?php echo htmlspecialchars($edit_cat['name']); ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="edit_description" class="form-label">Description (optional)</label>
                    <textarea class="form-control" id="edit_description" name="description" rows="2"><?php echo htmlspecialchars($edit_cat['description']); ?></textarea>
                  </div>
                  <button type="submit" name="update_category" class="btn btn-warning" onclick="return confirm('Save changes to this category?');">Update</button>
                  <a href="categories.php" class="btn btn-secondary">Cancel</a>
                </form>
              </div>
              <?php endif; ?>

              <!-- Categories Table -->
              <div class="table-responsive mt-4">
                <table class="table table-striped table-bordered">
                  <thead class="table-dark">
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Created At</th>
                      <th>Updated At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($categories) > 0): ?>
                      <?php foreach ($categories as $cat): ?>
                        <tr>
                          <td><?php echo $cat['id']; ?></td>
                          <td><?php echo htmlspecialchars($cat['name']); ?></td>
                          <td><?php echo htmlspecialchars($cat['description']); ?></td>
                          <td><?php echo $cat['created_at']; ?></td>
                          <td><?php echo $cat['updated_at']; ?></td>
                          <td>
                            <a href="categories.php?edit_id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="categories.php?delete_id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="6" class="text-center">No categories found.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div><!-- End Page Title -->
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
      const addForm = document.getElementById('addCategoryForm');
      const showAddBtn = document.getElementById('showAddFormBtn');
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

      // If edit mode is active, display edit form
      <?php if ($edit_cat): ?>
        document.getElementById('editCategoryForm').style.display = 'block';
        if (showAddBtn) showAddBtn.style.display = 'none';
      <?php endif; ?>
    });
  </script>
</body>
</html>
