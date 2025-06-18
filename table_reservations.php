<?php
include("../db.php");

// Require login
if (empty($sessionID)) {
    header("Location: ../login.php");
    exit;
}

// Pagination setup
$RES_PER_PAGE = 20;
$page         = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset       = ($page - 1) * $RES_PER_PAGE;

// Count total reservations for this user
$countQ      = "SELECT COUNT(*) AS cnt FROM reservations WHERE user_id = $sessionID";
$countR      = mysqli_query($con, $countQ);
$totalRes    = mysqli_fetch_assoc($countR)['cnt'];
$totalPages  = ceil($totalRes / $RES_PER_PAGE);

// Fetch paginated reservations
$resQ = "
  SELECT *
  FROM reservations
  WHERE user_id = $sessionID
  ORDER BY reservation_date DESC, reservation_time DESC
  LIMIT $RES_PER_PAGE OFFSET $offset
";
$resR = mysqli_query($con, $resQ);
$reservations = [];
while ($r = mysqli_fetch_assoc($resR)) {
    $reservations[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>My Table Reservations - Restaurant</title>
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
  <style>
    .reservation-card { margin-bottom: 1.5rem; }
    .reservation-card .card-body { padding: 1.5rem; }
    .list-group-item strong { width: 140px; display: inline-block; }
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
      <div class="heading">
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8">
              <h2 class="m-3">My Table Reservations</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reservations List -->
    <div class="container-fluid px-3 px-md-5">

      <!-- Reserve Table Button -->
      <div class="text-center mb-4">
        <a href="../table_reservation.php" class="btn btn-success">
          <i class="bi bi-calendar-plus me-1"></i>Reserve Table
        </a>
      </div>

      <?php if (empty($reservations)): ?>
        <div class="alert alert-info text-center">You have no table reservations yet.</div>
      <?php else: ?>
        <?php foreach ($reservations as $res): ?>
          <div class="card reservation-card shadow-sm" data-aos="fade-up">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-3">
                <div>
                  <h5>Reservation #<?php echo htmlspecialchars($res['id']); ?></h5>
                  <small class="text-muted">
                    <?php 
                      echo date('d-m-Y', strtotime($res['reservation_date'])) 
                           . ' at ' 
                           . date('H:i', strtotime($res['reservation_time']));
                    ?>
                  </small>
                </div>
                <div class="text-end">
                  <span class="badge 
                    <?php 
                      switch($res['status']) {
                        case 'confirmed': echo 'bg-success'; break;
                        case 'cancelled': echo 'bg-danger'; break;
                        default: echo 'bg-warning text-dark';
                      }
                    ?>">
                    <?php echo ucfirst($res['status']); ?>
                  </span>
                </div>
              </div>
              <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item">
                  <strong>Guests:</strong> <?php echo htmlspecialchars($res['number_of_guests']); ?>
                </li>
                <li class="list-group-item">
                  <strong>Occasion:</strong> <?php echo ucfirst(str_replace('_',' ',$res['occasion'])); ?>
                </li>
                <?php if (!empty($res['special_requests'])): ?>
                  <li class="list-group-item">
                    <strong>Requests:</strong> <?php echo htmlspecialchars($res['special_requests']); ?>
                  </li>
                <?php endif; ?>
                <li class="list-group-item">
                  <strong>Contact Phone:</strong> <?php echo htmlspecialchars($res['contact_phone']); ?>
                </li>
                <li class="list-group-item">
                  <strong>Code:</strong> <?php echo htmlspecialchars($res['reservation_code']); ?>
                </li>
              </ul>
              <?php if ($res['status'] === 'pending'): ?>
                <a href="cancel_reservation.php?id=<?php echo $res['id']; ?>" 
                   class="btn btn-outline-danger btn-sm">
                  <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <nav aria-label="Reservations pagination">
            <ul class="pagination justify-content-center">
              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?php if ($p === $page) echo 'active'; ?>">
                  <a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>
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
</body>
</html>
