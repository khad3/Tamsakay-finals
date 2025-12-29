<?php
session_start();

// Unset driver-specific session variables
unset($_SESSION['driver_logged_in']);
unset($_SESSION['driver_id']);


// If it's desired to kill the session cookie for the driver session as well, but keep other cookies (if needed)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

// Optionally, you can unset other session variables if necessary
// unset($_SESSION['other_session_variable']); // Uncomment if you want to clear other variables

// Redirect to the login page (or wherever you need)
header("Location: landingpage_user.php"); // Change 'login_user.php' to your actual login page
exit();
?>
