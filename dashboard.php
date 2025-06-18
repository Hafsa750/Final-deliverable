<?php
  include("../db.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard - Restaurant</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
            max-width: 500px;
            text-align: center;
        }
        .card h2 {
            margin-bottom: 10px;
            color: #333;
        }
        .card p {
            color: #666;
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
          <li><a href="dashboard.php" class="active">Dashboard</a></li>
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
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">


              <!-- Welcome Message -->
                  <div class="card">
                      <h2>Welcome <span style = 'color: blue;'><?php echo $sessionName; ?></span></h2>
                      <p style="color: green;">You have logged in successfully.</p>
                  </div>
              <!-- Logic Ends -->

              <br>
              <br>
              <br>
              <br>
              <br>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Page Title -->

    

  </main>

  <?php
    if ($logged_in) {
      include("../include/dashboard_footer.php");
    }
  ?>
  

</body>

</html>