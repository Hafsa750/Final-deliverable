<?php
include("../db.php");

$success_message = "";
$error_message   = "";

// Constants
$ITEMS_PER_PAGE = 50;
$IMAGE_DIR      = "../assets/img/menu_items/";

// Ensure image directory exists
if (!is_dir($IMAGE_DIR)) {
    mkdir($IMAGE_DIR, 0755, true);
}

// FETCH ALL CATEGORIES FOR DROPDOWN
$categories = [];
$catQuery   = "SELECT id, name FROM `menu_categories` ORDER BY name ASC";
$catResult  = mysqli_query($con, $catQuery);
while ($catRow = mysqli_fetch_assoc($catResult)) {
    $categories[] = $catRow;
}

// HANDLE SEARCH PARAMETERS
$search_name     = isset($_GET['search_name']) ? trim($_GET['search_name']) : "";
$search_category = isset($_GET['search_category']) ? intval($_GET['search_category']) : 0;

// BUILD WHERE CLAUSE BASED ON SEARCH
$whereClauses = [];
if ($search_name !== "") {
    $safe_name = mysqli_real_escape_string($con, $search_name);
    $whereClauses[] = "mi.name LIKE '%$safe_name%'";
}
if ($search_category > 0) {
    $whereClauses[] = "mi.category_id = $search_category";
}
$whereSQL = "";
if (count($whereClauses) > 0) {
    $whereSQL = "WHERE " . implode(" AND ", $whereClauses);
}

// HANDLE ADD MENU ITEM
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $category_id = intval($_POST['category_id']);
    $name        = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $price       = trim($_POST['price']);

    // Validate required fields
    if (empty($name) || empty($price) || $category_id <= 0) {
        $error_message = "Category, Name, and Price are required.";
    } elseif (!is_numeric($price) || floatval($price) < 0) {
        $error_message = "Price must be a non-negative number.";
    } elseif (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
        $error_message = "Image file is required.";
    } else {
        // Validate image upload
        $fileTmpPath  = $_FILES['image_file']['tmp_name'];
        $fileName     = $_FILES['image_file']['name'];
        $fileSize     = $_FILES['image_file']['size'];
        $fileType     = $_FILES['image_file']['type'];
        $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt   = ['jpg','jpeg','png','gif','webp'];

        if (!in_array($fileExt, $allowedExt)) {
            $error_message = "Invalid image type. Allowed: " . implode(", ", $allowedExt);
        } else {
            // Verify it's truly an image
            $imgInfo = @getimagesize($fileTmpPath);
            if ($imgInfo === false) {
                $error_message = "Uploaded file is not a valid image.";
            } else {
                // Generate unique file name
                $newFileName = uniqid('item_', true) . "." . $fileExt;
                $destPath    = $IMAGE_DIR . $newFileName;
                if (!move_uploaded_file($fileTmpPath, $destPath)) {
                    $error_message = "Error moving uploaded file.";
                } else {
                    // Insert into DB
                    $insertQ = "INSERT INTO `menu_items` (category_id, name, description, price, image_url)
                                VALUES ($category_id, '$name', '$description', '$price', '$newFileName')";
                    if (mysqli_query($con, $insertQ)) {
                        $success_message = "Menu item added successfully.";
                    } else {
                        unlink($destPath);
                        $error_message = "Error adding menu item: " . mysqli_error($con);
                    }
                }
            }
        }
    }
}

// HANDLE UPDATE MENU ITEM
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_item'])) {
    $item_id     = intval($_POST['item_id']);
    $category_id = intval($_POST['category_id']);
    $name        = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $price       = trim($_POST['price']);

    if ($item_id <= 0 || empty($name) || empty($price) || $category_id <= 0) {
        $error_message = "Category, Name, and Price are required.";
    } elseif (!is_numeric($price) || floatval($price) < 0) {
        $error_message = "Price must be a non-negative number.";
    } else {
        // Fetch existing item to get old image filename
        $oldQuery    = "SELECT image_url FROM `menu_items` WHERE id = $item_id";
        $oldResult   = mysqli_query($con, $oldQuery);
        $oldRow      = mysqli_fetch_assoc($oldResult);
        $oldImage    = $oldRow['image_url'];

        $newFileName = $oldImage;
        $removeOld   = false;

        // If a new file is uploaded
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $fileTmpPath  = $_FILES['image_file']['tmp_name'];
            $fileName     = $_FILES['image_file']['name'];
            $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExt   = ['jpg','jpeg','png','gif','webp'];

            if (!in_array($fileExt, $allowedExt)) {
                $error_message = "Invalid image type. Allowed: " . implode(", ", $allowedExt);
            } else {
                $imgInfo = @getimagesize($fileTmpPath);
                if ($imgInfo === false) {
                    $error_message = "Uploaded file is not a valid image.";
                } else {
                    $newFileName = uniqid('item_', true) . "." . $fileExt;
                    $destPath    = $IMAGE_DIR . $newFileName;
                    if (!move_uploaded_file($fileTmpPath, $destPath)) {
                        $error_message = "Error moving uploaded file.";
                    } else {
                        $removeOld = true;
                    }
                }
            }
        }

        if ($error_message === "") {
            // Update record
            $updateQ = "UPDATE `menu_items`
                        SET category_id = $category_id,
                            name = '$name',
                            description = '$description',
                            price = '$price',
                            image_url = '$newFileName'
                        WHERE id = $item_id";
            if (mysqli_query($con, $updateQ)) {
                if ($removeOld && !empty($oldImage)) {
                    $oldPath = $IMAGE_DIR . $oldImage;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $success_message = "Menu item updated successfully.";
            } else {
                // If new image was moved but DB update failed, remove new image
                if ($removeOld) {
                    unlink($destPath);
                }
                $error_message = "Error updating menu item: " . mysqli_error($con);
            }
        }
    }
}

// HANDLE DELETE MENU ITEM
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Fetch image filename
    $imgQuery   = "SELECT image_url FROM `menu_items` WHERE id = $delete_id";
    $imgResult  = mysqli_query($con, $imgQuery);
    $imgRow     = mysqli_fetch_assoc($imgResult);
    $oldImage   = $imgRow['image_url'];

    $deleteQ    = "DELETE FROM `menu_items` WHERE id = $delete_id";
    if (mysqli_query($con, $deleteQ)) {
        if (!empty($oldImage)) {
            $oldPath = $IMAGE_DIR . $oldImage;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        $success_message = "Menu item deleted successfully.";
    } else {
        $error_message = "Error deleting menu item: " . mysqli_error($con);
    }
}

// PAGINATION
$page       = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset     = ($page - 1) * $ITEMS_PER_PAGE;

// COUNT TOTAL ITEMS FOR PAGINATION
$countQuery = "
    SELECT COUNT(*) AS total
    FROM `menu_items` mi
    JOIN `menu_categories` mc ON mi.category_id = mc.id
    $whereSQL
";
$countResult = mysqli_query($con, $countQuery);
$countRow    = mysqli_fetch_assoc($countResult);
$totalItems  = intval($countRow['total']);
$totalPages  = ceil($totalItems / $ITEMS_PER_PAGE);

// FETCH PAGINATED ITEMS
$itemQuery = "
    SELECT mi.id, mi.name, mi.description, mi.price, mi.image_url, mi.created_at, mi.updated_at,
           mc.name AS category_name, mc.id AS category_id
    FROM `menu_items` mi
    JOIN `menu_categories` mc ON mi.category_id = mc.id
    $whereSQL
    ORDER BY mi.id DESC
    LIMIT $ITEMS_PER_PAGE
    OFFSET $offset
";
$itemResult = mysqli_query($con, $itemQuery);
$items       = [];
while ($itemRow = mysqli_fetch_assoc($itemResult)) {
    $items[] = $itemRow;
}

// DETERMINE IF EDIT MODE
$edit_id   = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_item = null;
if ($edit_id) {
    foreach ($items as $itm) {
        if ($itm['id'] == $edit_id) {
            $edit_item = $itm;
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
  <title>Manage Menu Items - Restaurant Admin</title>
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
    /* Forms: half width */
    .form-container {
      max-width: 50%;
      margin: 0 auto;
    }
    /* Card image styling: show full aspect ratio without cropping */
    .menu-card img {
      width: 100%;
      height: auto;
      max-height: 200px;
      object-fit: contain;
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
          <li><a href="menu_items.php" class="active">Menu Items</a></li>
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
    <div class="page-title" data-aos="fade">
      <div class="heading pb-0">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">

              <h1 class="m-3">Manage Menu Items</h1>

              <!-- Success / Error Messages -->
              <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
              <?php endif; ?>
              <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
              <?php endif; ?>

              <!-- SEARCH FORM -->
              <form method="GET" action="menu_items.php" class="row g-3 mb-4 form-container">
                <div class="col-md-5">
                  <label for="search_name" class="form-label">Search Name</label>
                  <input type="text" class="form-control" id="search_name" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>">
                </div>
                <div class="col-md-5">
                  <label for="search_category" class="form-label">Category</label>
                  <select class="form-select" id="search_category" name="search_category">
                    <option value="">-- All Categories --</option>
                    <?php foreach ($categories as $cat): ?>
                      <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $search_category) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary me-2">Search</button>
                  <a href="menu_items.php" class="btn btn-secondary">Reset</a>
                </div>
              </form>

              <!-- Add New Item Button -->
              <?php if (!$edit_item): ?>
                <div class="mb-3">
                  <button id="showAddFormBtn" class="btn btn-primary">Add New Item</button>
                </div>
              <?php endif; ?>

              <!-- ADD ITEM FORM (HIDDEN BY DEFAULT) -->
              <div id="addItemForm" class="card p-2 form-container" style="display: none;">
                <h4>Add New Menu Item</h4>
                <form method="POST" action="menu_items.php" enctype="multipart/form-data">
                  <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                      <option value="">-- Select Category --</option>
                      <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="name" class="form-label">Item Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                  <div class="mb-3">
                    <label for="description" class="form-label">Description (optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="price" name="price" required>
                  </div>
                  <div class="mb-3">
                    <label for="image_file" class="form-label">Image File</label>
                    <input class="form-control" type="file" id="image_file" name="image_file" accept="image/*" required>
                  </div>
                  <button type="submit" name="add_item" class="btn btn-success me-2" onclick="return confirm('Add this menu item?');">Save</button>
                  <button type="button" id="cancelAddBtn" class="btn btn-secondary">Cancel</button>
                </form>
              </div>

              <!-- EDIT ITEM FORM -->
              <?php if ($edit_item): ?>
              <div id="editItemForm" class="card p-4 form-container">
                <h4>Edit Menu Item (ID: <?php echo $edit_item['id']; ?>)</h4>
                <form method="POST" action="menu_items.php?edit_id=<?php echo $edit_item['id']; ?>" enctype="multipart/form-data">
                  <input type="hidden" name="item_id" value="<?php echo $edit_item['id']; ?>">
                  <div class="mb-3">
                    <label for="edit_category_id" class="form-label">Category</label>
                    <select class="form-select" id="edit_category_id" name="category_id" required>
                      <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $edit_item['category_id']) echo 'selected'; ?>>
                          <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="edit_name" class="form-label">Item Name</label>
                    <input type="text" class="form-control" id="edit_name" name="name" value="<?php echo htmlspecialchars($edit_item['name']); ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="edit_description" class="form-label">Description (optional)</label>
                    <textarea class="form-control" id="edit_description" name="description" rows="2"><?php echo htmlspecialchars($edit_item['description']); ?></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="edit_price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="edit_price" name="price" value="<?php echo $edit_item['price']; ?>" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <?php if (!empty($edit_item['image_url'])): ?>
                      <img src="../assets/img/menu_items/<?php echo htmlspecialchars($edit_item['image_url']); ?>" alt="" class="img-thumbnail mb-2" style="max-width: 150px;">
                    <?php else: ?>
                      <p>No image</p>
                    <?php endif; ?>
                  </div>
                  <div class="mb-3">
                    <label for="edit_image_file" class="form-label">Replace Image File (optional)</label>
                    <input class="form-control" type="file" id="edit_image_file" name="image_file" accept="image/*">
                  </div>
                  <button type="submit" name="update_item" class="btn btn-warning me-2" onclick="return confirm('Save changes to this menu item?');">Update</button>
                  <a href="menu_items.php" class="btn btn-secondary">Cancel</a>
                </form>
              </div>
              <?php endif; ?>

            </div> <!-- end of .col-lg-8 -->
          </div> <!-- end of .row for heading/forms -->
        </div> <!-- end of .container -->
      </div> <!-- end of .heading -->
    </div> <!-- end of .page-title -->
    <hr>
    <!-- MENU ITEMS CARDS: FULL WIDTH SECTION -->
    <div class="container-fluid p-4">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
        <?php if (count($items) > 0): ?>
          <?php foreach ($items as $itm): ?>
            <div class="col">
              <div class="card h-100 menu-card shadow-sm">
                <?php if (!empty($itm['image_url']) && file_exists($IMAGE_DIR . $itm['image_url'])): ?>
                  <img src="../assets/img/menu_items/<?php echo htmlspecialchars($itm['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($itm['name']); ?>">
                <?php else: ?>
                  <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <span class="text-muted">No Image</span>
                  </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title fw-bolder" style="color: #71C55D;"><?php echo htmlspecialchars($itm['name']); ?></h5>
                  <p class="card-text mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($itm['category_name']); ?></p>
                  <p class="card-text mb-1"><strong>Price:</strong> Rs. <?php echo number_format($itm['price'], 0); ?></p>
                  <p class="card-text mb-2"><?php echo nl2br(htmlspecialchars($itm['description'])); ?></p>
                  <div class="mt-auto">
                    <a href="menu_items.php?edit_id=<?php echo $itm['id']; ?>" class="btn btn-sm btn-primary me-1">Edit</a>
                    <a href="menu_items.php?delete_id=<?php echo $itm['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this menu item?');">Delete</a>
                  </div>
                </div>
                <div class="card-footer text-muted small">
                  Created: <?php echo date('d-m-Y H:i:s', strtotime($itm['created_at'])); ?><br>
                  Updated: <?php echo date('d-m-Y H:i:s', strtotime($itm['updated_at'])); ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col">
            <p class="text-center">No menu items found.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- PAGINATION CONTROLS (FULL WIDTH) -->
      <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination justify-content-center">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <li class="page-item <?php echo ($p == $page) ? 'active' : ''; ?>">
                <a class="page-link" href="menu_items.php?
                  <?php if ($search_name !== "") echo "search_name=" . urlencode($search_name) . "&"; ?>
                  <?php if ($search_category > 0) echo "search_category=" . $search_category . "&"; ?>
                  page=<?php echo $p; ?>">
                  <?php echo $p; ?>
                </a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    </div> <!-- end of container-fluid for cards & pagination -->
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
      const addForm      = document.getElementById('addItemForm');
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
      <?php if ($edit_item): ?>
        document.getElementById('editItemForm').style.display = 'block';
        if (showAddBtn) showAddBtn.style.display = 'none';
      <?php endif; ?>
    });
  </script>
</body>
</html>
