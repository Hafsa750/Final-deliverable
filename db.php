<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "restaurant_chatbot_db");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$sessionID   = $_SESSION["sessionID"] ?? 0;
$sessionRole = $_SESSION["sessionRole"] ?? "";
$sessionName = $_SESSION["sessionName"] ?? "";

$logged_in = !empty($sessionID);
$user_name = $sessionName ?? '';
$reservation_link = $logged_in ? 'table_reservation.php' : 'login.php';

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

// New function to get user by session
function getUserBySession($con) {
    if (!isset($_SESSION['sessionID']) || empty($_SESSION['sessionID'])) {
        return null;
    }
    
    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['sessionID']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}
?>