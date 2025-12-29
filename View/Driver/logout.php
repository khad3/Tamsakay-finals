<?php
session_start();

if (isset($_SESSION['driver_id'])) {
    include '../../db.php'; // Adjust path as needed

    $driver_id = $_SESSION['driver_id'];
    $status = "Not Available";
    $online_status = "Offline";

    // ✅ Update both status_driver and Online_status
    $update_query = "UPDATE driver_status SET status_driver = ?, Online_status = ? WHERE driver_id = ?";
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("ssi", $status, $online_status, $driver_id);
    $stmt->execute();
    $stmt->close();
}

// Clean up session
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


// // List of session keys to KEEP (these belong to the user)
// $keep_keys = ['user_id', 'user_logged_in'];

// // Remove everything else (like driver session variables)
// foreach ($_SESSION as $key => $value) {
//     if (!in_array($key, $keep_keys)) {
//         unset($_SESSION[$key]);
//     }
// }

// Redirect to login

session_destroy();

header("Location: login.php");
exit();
?>
