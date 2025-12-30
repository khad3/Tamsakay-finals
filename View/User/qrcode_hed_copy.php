<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . '/tamsakay-finals/qr_code/phpqrcode/qrlib.php'; 

// Set the path where the QR code image will be saved
$path = $_SERVER['DOCUMENT_ROOT'] . '/tamsakay-finals/qr_code/phpqrcode/img/';
$qrcodePath = $path . time() . ".png";

// Set a user ID directly for demonstration purposes
$user_id = $_GET['id'] ?? null; // Use null if 'id' is not set

if ($user_id === null) {
    die("User ID is not provided."); // Exit if no user ID
}

// Create the QR code content with the user's ID
$qrContent = "http://localhost/tamsakay-finals/View/User/insert_testing.php?id=" . urlencode($user_id);

// Start output buffering to capture any errors
ob_start();
QRcode::png($qrContent, $qrcodePath, 'H', 4, 4);
$errorOutput = ob_get_contents(); // Capture any output or errors
ob_end_clean();

if ($errorOutput) {
    // Output any error messages
    echo "Error generating QR code: " . nl2br(htmlspecialchars($errorOutput));
} else {
    // If the QR code is generated successfully, display it
    echo "<img src='/tamsakay-finals/qr_code/phpqrcode/img/" . basename($qrcodePath) . "' alt='Generated QR Code'>";
}
?>
