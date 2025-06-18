<?php
include("db.php");           // starts session, defines $sessionID, $sessionName, $con

// Redirect guests
if (empty($sessionID)) {
    header("Location: login.php");
    exit;
}

// Fetch user phone for prefilling
$userQ = "SELECT phone FROM users WHERE id = $sessionID LIMIT 1";
$userR = mysqli_query($con, $userQ);
$user  = mysqli_fetch_assoc($userR);

// Fetch reservation settings
$settingsQ = "SELECT max_tables, slot_duration_mins FROM reservation_settings LIMIT 1";
$settingsR = mysqli_query($con, $settingsQ);
$settings = mysqli_fetch_assoc($settingsR);
$max_tables         = (int)$settings['max_tables'];
$slot_duration_mins = (int)$settings['slot_duration_mins'];

// Initialize
$error_message    = '';
$show_receipt     = false;
$reservation_data = [];

// If redirected after POST, pull receipt data from session
if (!empty($_SESSION['reservation_data'])) {
    $show_receipt     = true;
    $reservation_data = $_SESSION['reservation_data'];
    unset($_SESSION['reservation_data']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date             = $_POST['reservation_date']    ?? '';
    $time             = $_POST['reservation_time']    ?? '';
    $number_of_guests = max(1, intval($_POST['number_of_guests'] ?? 1));
    $occasion         = mysqli_real_escape_string($con, $_POST['occasion'] ?? 'casual');
    $special_requests = mysqli_real_escape_string($con, trim($_POST['special_requests'] ?? ''));
    $contact_phone    = mysqli_real_escape_string($con, $_POST['contact_phone'] ?? '');

    // Validate
    $today = date('Y-m-d');
    if (!$date || !$time) {
        $error_message = "Date and time are required.";
    } elseif ($date < $today) {
        $error_message = "Cannot reserve for a past date.";
    } elseif (!$contact_phone) {
        $error_message = "Please provide a contact phone number.";
    } else {
        // Check slot availability
        $cntQ = "
          SELECT COUNT(*) AS cnt
          FROM reservations
          WHERE reservation_date = '$date'
            AND reservation_time = '$time'
            AND status != 'cancelled'
        ";
        $cnt = (int)mysqli_fetch_assoc(mysqli_query($con, $cntQ))['cnt'];

        if ($cnt >= $max_tables) {
            $error_message = "No tables left at that time. Please pick another slot.";
        } else {
            // Generate reservation code
            $reservation_code = bin2hex(random_bytes(8));

            // Insert reservation
            $insQ = "
              INSERT INTO reservations
                (user_id, reservation_date, reservation_time,
                 number_of_guests, occasion, special_requests,
                 contact_phone, reservation_code)
              VALUES
                ($sessionID, '$date', '$time',
                 $number_of_guests, '$occasion', '$special_requests',
                 '$contact_phone', '$reservation_code')
            ";
            if (mysqli_query($con, $insQ)) {
                // Store receipt data in session, then redirect (PRG)
                $_SESSION['reservation_data'] = [
                    'date'     => $date,
                    'time'     => $time,
                    'guests'   => $number_of_guests,
                    'occasion' => $occasion,
                    'requests' => $special_requests,
                    'phone'    => $contact_phone,
                    'code'     => $reservation_code,
                ];
                header("Location: table_reservation.php");
                exit;
            } else {
                $error_message = "Database error: " . mysqli_error($con);
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
  <title>Table Reservation - Restaurant</title>
  <meta name="description" content="Reserve your table easily.">
  <meta name="keywords" content="Restaurant, Reservations, Table Booking">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Poppins&family=Raleway&display=swap" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    .form-control, .form-select { max-width: 300px; }
    .receipt-card { max-width: 600px; margin: 2rem auto; }
  </style>
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
          <li><a href="table_reservation.php" class="active">Table Reservation</a></li>
          
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

  <main class="main py-5">
    <div class="container">

      <h2 class="mb-4">Reserve a Table</h2>

      <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>

      <?php if ($show_receipt): ?>
        <!-- Professional Receipt -->
        <div class="card receipt-card shadow">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0 text-white"><i class="bi bi-check-circle me-2"></i>Reservation Confirmed</h5>
          </div>
          <div class="card-body">
            <p class="card-text">Thank you, <?php echo htmlspecialchars($sessionName); ?>! Your table has been reserved.</p>
            <ul class="list-group list-group-flush mb-3">
              <li class="list-group-item"><strong>Date:</strong> <?php echo htmlspecialchars($reservation_data['date']); ?></li>
              <li class="list-group-item"><strong>Time:</strong> <?php echo htmlspecialchars($reservation_data['time']); ?></li>
              <li class="list-group-item"><strong>Guests:</strong> <?php echo htmlspecialchars($reservation_data['guests']); ?></li>
              <li class="list-group-item"><strong>Occasion:</strong> <?php echo ucfirst(str_replace('_',' ', $reservation_data['occasion'])); ?></li>
              <?php if ($reservation_data['requests']): ?>
                <li class="list-group-item"><strong>Special Requests:</strong> <?php echo htmlspecialchars($reservation_data['requests']); ?></li>
              <?php endif; ?>
              <li class="list-group-item"><strong>Contact Phone:</strong> <?php echo htmlspecialchars($reservation_data['phone']); ?></li>
              <li class="list-group-item"><strong>Reservation Code:</strong> <?php echo htmlspecialchars($reservation_data['code']); ?></li>
            </ul>
            <a href="user/table_reservations.php" class="btn btn-primary">
              <i class="bi bi-card-list me-2"></i>View Reservations
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- Reservation Form -->
        <form method="POST" class="row g-4">
          <div class="col-md-4">
            <label for="reservation_date" class="form-label">Date</label>
            <input type="date" id="reservation_date" name="reservation_date"
                   class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="col-md-4">
            <label for="reservation_time" class="form-label">Time</label>
            <input type="time" id="reservation_time" name="reservation_time"
                   class="form-control" step="<?php echo $slot_duration_mins * 60; ?>" required>
            <div class="form-text">
              Available: 12:00 PM – 10:00 PM; slots every <?php echo $slot_duration_mins; ?> minutes.
            </div>
          </div>
          <div class="col-md-4">
            <label for="number_of_guests" class="form-label">Number of Guests</label>
            <input type="number" id="number_of_guests" name="number_of_guests"
                   class="form-control" min="1" value="1" required>
          </div>
          <div class="col-md-4">
            <label for="occasion" class="form-label">Occasion</label>
            <select id="occasion" name="occasion" class="form-select">
              <option value="casual">Casual</option>
              <option value="birthday">Birthday</option>
              <option value="anniversary">Anniversary</option>
              <option value="business">Business</option>
              <option value="holiday">Holiday</option>
              <option value="family_reunion">Family Reunion</option>
              <option value="engagement">Engagement</option>
              <option value="proposal">Proposal</option>
              <option value="graduation">Graduation</option>
              <option value="date_night">Date Night</option>
              <option value="corporate_event">Corporate Event</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="contact_phone" class="form-label">Contact Phone</label>
            <input type="tel" id="contact_phone" name="contact_phone"
                   class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
          </div>
          <div class="col-12">
            <label for="special_requests" class="form-label">Special Requests</label>
            <textarea id="special_requests" name="special_requests"
                      class="form-control" rows="3" placeholder="Dietary needs, seating preference…"></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-calendar-plus me-2"></i>Book Now
            </button>
          </div>
        </form>
      <?php endif; ?>

    </div>
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
