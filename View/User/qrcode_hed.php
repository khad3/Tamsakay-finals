<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/tamsakay-finals/qr_code/phpqrcode/qrlib.php'; 

// Set the path where the QR code image will be saved
$path = $_SERVER['DOCUMENT_ROOT'] . '/tamsakay-finals/qr_code/phpqrcode/img/';
$qrcodePath = $path . time() . ".png";

// Create some test QR code content
$qrContent = "http://localhost/tamsakay-finals/View/User/insert.php";

// Generate the QR code and directly output it to the browser
QRcode::png($qrContent, $qrcodePath, 'H', 4, 4);
echo "<img src='/tamsakay-finals/qr_code/phpqrcode/img/" . basename($qrcodePath) . "' alt='Generated QR Code'>";
?>

