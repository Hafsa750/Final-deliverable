<?php
include("../db.php");

$success_message = "";
$error_message   = "";

// HANDLE ADD FAQ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_faq'])) {
    $question = htmlspecialchars(trim($_POST['question']));
    $answer   = htmlspecialchars(trim($_POST['answer']));

    if (empty($question) || empty($answer)) {
        $error_message = "Both Question and Answer are required.";
    } else {
        $insertQ = "INSERT INTO `faqs` (question, answer) VALUES ('$question', '$answer')";
        if (mysqli_query($con, $insertQ)) {
            $success_message = "FAQ added successfully.";
        } else {
            $error_message = "Error adding FAQ: " . mysqli_error($con);
        }
    }
}

// HANDLE UPDATE FAQ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_faq'])) {
    $faq_id   = intval($_POST['faq_id']);
    $question = htmlspecialchars(trim($_POST['question']));
    $answer   = htmlspecialchars(trim($_POST['answer']));

    if ($faq_id <= 0 || empty($question) || empty($answer)) {
        $error_message = "Both Question and Answer are required.";
    } else {
        $updateQ = "UPDATE `faqs` 
                    SET question = '$question', answer = '$answer' 
                    WHERE id = $faq_id";
        if (mysqli_query($con, $updateQ)) {
            $success_message = "FAQ updated successfully.";
        } else {
            $error_message = "Error updating FAQ: " . mysqli_error($con);
        }
    }
}

// HANDLE DELETE FAQ
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id > 0) {
        $deleteQ = "DELETE FROM `faqs` WHERE id = $delete_id";
        if (mysqli_query($con, $deleteQ)) {
            $success_message = "FAQ deleted successfully.";
        } else {
            $error_message = "Error deleting FAQ: " . mysqli_error($con);
        }
    }
}

// FETCH ALL FAQs
$faqQuery  = "SELECT * FROM `faqs` ORDER BY id DESC";
$faqResult = mysqli_query($con, $faqQuery);
$faqs      = [];
while ($row = mysqli_fetch_assoc($faqResult)) {
    $faqs[] = $row;
}

// DETERMINE IF EDIT MODE
$edit_id   = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_faq  = null;
if ($edit_id) {
    foreach ($faqs as $f) {
        if ($f['id'] == $edit_id) {
            $edit_faq = $f;
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
  <title>Manage FAQs - Restaurant Admin</title>
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
    .accordion-button:focus {
      box-shadow: none;
    }
    .faq-card .card-body {
      padding: 1rem;
    }
    .card-footer .small {
      font-size: 0.85rem;
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
          <li><a href="faqs.php" class="active">FAQs</a></li>
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

              <h1 class="m-3">Manage FAQs</h1>

              <!-- Success / Error Messages -->
              <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
              <?php endif; ?>
              <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
              <?php endif; ?>

              <!-- ADD FAQ BUTTON -->
              <?php if (!$edit_faq): ?>
                <div class="mb-3">
                  <button id="showAddFormBtn" class="btn btn-primary">Add New FAQ</button>
                </div>
              <?php endif; ?>

              <!-- ADD FAQ FORM (HIDDEN BY DEFAULT) -->
              <div id="addFaqForm" class="card p-4 form-container" style="display: none;">
                <h4>Add New FAQ</h4>
                <form method="POST" action="faqs.php">
                  <div class="mb-3">
                    <label for="question" class="form-label">Question</label>
                    <input type="text" class="form-control" id="question" name="question" required>
                  </div>
                  <div class="mb-3">
                    <label for="answer" class="form-label">Answer</label>
                    <textarea class="form-control" id="answer" name="answer" rows="4" required></textarea>
                  </div>
                  <button type="submit" name="add_faq" class="btn btn-success me-2" onclick="return confirm('Add this FAQ?');">Save</button>
                  <button type="button" id="cancelAddBtn" class="btn btn-secondary">Cancel</button>
                </form>
              </div>

              <!-- EDIT FAQ FORM -->
              <?php if ($edit_faq): ?>
                <div id="editFaqForm" class="card p-4 form-container">
                  <h4>Edit FAQ (ID: <?php echo $edit_faq['id']; ?>)</h4>
                  <form method="POST" action="faqs.php?edit_id=<?php echo $edit_faq['id']; ?>">
                    <input type="hidden" name="faq_id" value="<?php echo $edit_faq['id']; ?>">
                    <div class="mb-3">
                      <label for="edit_question" class="form-label">Question</label>
                      <input type="text" class="form-control" id="edit_question" name="question" value="<?php echo htmlspecialchars($edit_faq['question']); ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_answer" class="form-label">Answer</label>
                      <textarea class="form-control" id="edit_answer" name="answer" rows="4" required><?php echo htmlspecialchars($edit_faq['answer']); ?></textarea>
                    </div>
                    <button type="submit" name="update_faq" class="btn btn-warning me-2" onclick="return confirm('Save changes to this FAQ?');">Update</button>
                    <a href="faqs.php" class="btn btn-secondary">Cancel</a>
                  </form>
                </div>
              <?php endif; ?>

            </div> <!-- end .col-lg-8 -->
          </div> <!-- end .row -->
        </div> <!-- end .container -->
      </div> <!-- end .heading -->
    </div> <!-- end .page-title -->

    <!-- FAQ LIST: FULL WIDTH ACCORDION -->
    <div class="container-fluid mt-1 p-5">
      <?php if (count($faqs) > 0): ?>
        <div class="accordion" id="faqAccordion">
          <?php foreach ($faqs as $index => $f): ?>
            <div class="accordion-item faq-card mb-3">
              <h2 class="accordion-header" id="heading-<?php echo $f['id']; ?>">
                <button class="accordion-button <?php echo ($index !== 0) ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $f['id']; ?>" aria-expanded="<?php echo ($index === 0) ? 'true' : 'false'; ?>" aria-controls="collapse-<?php echo $f['id']; ?>">
                  <?php echo htmlspecialchars($f['question']); ?>
                </button>
              </h2>
              <div id="collapse-<?php echo $f['id']; ?>" class="accordion-collapse collapse <?php echo ($index === 0) ? 'show' : ''; ?>" aria-labelledby="heading-<?php echo $f['id']; ?>" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  <p><?php echo nl2br(htmlspecialchars($f['answer'])); ?></p>
                  <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                      Created: <?php echo date('d-m-Y H:i:s', strtotime($f['created_at'])); ?><br>
                      Updated: <?php echo date('d-m-Y H:i:s', strtotime($f['updated_at'])); ?>
                    </div>
                    <div>
                      <a href="faqs.php?edit_id=<?php echo $f['id']; ?>" class="btn btn-sm btn-primary me-1">Edit</a>
                      <a href="faqs.php?delete_id=<?php echo $f['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this FAQ?');">Delete</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-center">No FAQs found.</p>
      <?php endif; ?>
    </div> <!-- end .container-fluid -->

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
      const addForm      = document.getElementById('addFaqForm');
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
      <?php if ($edit_faq): ?>
        document.getElementById('editFaqForm').style.display = 'block';
        if (showAddBtn) showAddBtn.style.display = 'none';
      <?php endif; ?>
    });
  </script>
</body>
</html>
