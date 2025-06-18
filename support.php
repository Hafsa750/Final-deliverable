<?php
include("../db.php");

// Ensure user is logged in
$user_id = $_SESSION["sessionID"] ?? 0;
if (!$user_id) {
  header("Location: ../login.php");
  exit;
}

// Handle new request
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['new_message'])) {
  $msg = trim($_POST['message'] ?? '');
  if ($msg !== '') {
    $stmt = $con->prepare("
      INSERT INTO support_messages
        (user_id, message)
      VALUES (?, ?)
    ");
    $stmt->bind_param("is", $user_id, $msg);
    $stmt->execute();
    $stmt->close();
    $success = "Your message has been sent.";
  } else {
    $error = "Please enter a message.";
  }
}

// Fetch all requests by this user
$reqQ = "
  SELECT id, message, status, created_at
  FROM support_messages
  WHERE user_id=? AND parent_id IS NULL
  ORDER BY created_at DESC
";
$reqStmt = $con->prepare($reqQ);
$reqStmt->bind_param("i", $user_id);
$reqStmt->execute();
$reqRs = $reqStmt->get_result();
$requests = $reqRs->fetch_all(MYSQLI_ASSOC);
$reqStmt->close();

// Fetch replies for these requests
$replies = [];
if ($requests) {
  $ids = array_column($requests,'id');
  $in  = implode(',', $ids);
  $repQ = "
    SELECT parent_id, user_id, responder_id, message, created_at
    FROM support_messages
    WHERE parent_id IN ($in)
    ORDER BY created_at ASC
  ";
  $repR = mysqli_query($con, $repQ);
  while ($r = mysqli_fetch_assoc($repR)) {
    $replies[$r['parent_id']][] = $r;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width,initial-scale=1" name="viewport">
  <title>Support – Restaurant Chatbot</title>
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
  <style>
    .card { margin-bottom:1.5rem; }
    .reply { margin-left:2rem; font-size:0.9rem; }
    #newForm { display:none; margin-bottom:2rem; }
  </style>
</head>
<body class="starter-page-page">
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <h1 class="sitename"><span>Restaurant</span> Customer</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="../browse_menu.php">Browse Menu</a></li>
          <li><a href="orders.php">Orders</a></li>
          <li><a href="table_reservations.php">Table Reservations</a></li>
          <li><a href="support.php" class="active">Support</a></li>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="../logout.php">Logout</a></li>
        </ul>
        <i class="mobile-nav-toggle bi bi-list d-xl-none"></i>
      </nav>
    </div>
  </header>

  <main class="main py-5">
    <div class="container">
      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <div class="text-center mb-4">
        <button id="toggleForm" class="btn btn-primary">
          <i class="bi bi-chat-dots me-1"></i>New Message
        </button>
      </div>
      <div id="newForm">
        <form method="POST" class="mb-5">
          <div class="mb-3">
            <label class="form-label">Your Message</label>
            <textarea name="message" class="form-control" rows="4" required></textarea>
          </div>
          <button name="new_message" type="submit" class="btn btn-success">Send</button>
        </form>
      </div>

      <?php if (empty($requests)): ?>
        <div class="alert alert-info text-center">No support messages yet.</div>
      <?php else: ?>
        <?php foreach ($requests as $req): ?>
          <div class="card shadow-sm">
            <div class="card-body">
              <p>
                <strong>Request #<?= $req['id'] ?></strong>
                <span class="badge bg-<?= $req['status']==='open' 
                    ? 'warning text-dark' 
                    : ($req['status']==='in_progress' ? 'info' : 'success') 
                  ?>">
                  <?= ucfirst($req['status']) ?>
                </span>
              </p>
              <p><?= nl2br(htmlspecialchars($req['message'])) ?></p>
              <small class="text-muted">
                Sent: <?= date('d-m-Y H:i', strtotime($req['created_at'])) ?>
              </small>

              <!-- Replies -->
              <?php if (!empty($replies[$req['id']])): ?>
                <?php foreach ($replies[$req['id']] as $r): ?>
                  <div class="reply">
                    <p>
                      <strong>
                        <?= $r['responder_id'] 
                            ? 'You' 
                            : 'Support' ?>:
                      </strong>
                      <?= nl2br(htmlspecialchars($r['message'])) ?>
                    </p>
                    <small class="text-muted">
                      <?= date('d-m-Y H:i', strtotime($r['created_at'])) ?>
                    </small>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
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
