<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php'; // Database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die("User must be logged in to scan.");
}

// Ensure user_id is available in the session
if (!isset($_SESSION['user_id'])) {
    die("Loko ayoko na.");
}

$user_id_from_session = $_SESSION['user_id']; // Retrieve the logged-in user's ID from the session

// Ensure database connection
if (!$db) {
    die("Database connection failed."); 
}

try {
    // Insert user data into passenger_logs_hed_tbl when they scan
    $stmt = $db->prepare("INSERT INTO passenger_logs_hed_tbl (user_id, location, scan_time) VALUES (?, 'WALTERMART', NOW())");
    $stmt->bind_param("i", $user_id_from_session); // Use the logged-in user_id from the session
    $stmt->execute(); // Execute the insert

    // Redirect after scan is logged
    echo '<script language="javascript">
            alert("Scan logged successfully.");
            window.location.href = "User/waiting_room_walter.php"; // Replace with actual dashboard URL
          </script>';
} catch (mysqli_sql_exception $e) {
    // Handle any SQL errors
    echo '<script language="javascript">
            alert("You are already scanned! Please patiently waiting the driver.");
            window.location.href = "http://localhost/Tamsakay/View/User/dashboard_user.php"; // Replace with actual dashboard URL
          </script>';
}

// Close the database connection
$stmt->close();
$db->close();
?>
