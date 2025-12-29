<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the ID of the QR code to delete
    $id = intval($_POST['id']); // Ensure the ID is an integer

    // Prepare the SQL DELETE statement
    $sql = "DELETE FROM qrcode_tbl_walter WHERE walter_id = ?";
    
    // Prepare the statement
    if ($stmt = mysqli_prepare($db, $sql)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, 'i', $id);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "QR code deleted successfully.";
        } else {
            echo "Error deleting QR code: " . mysqli_error($db);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($db);
    }
} else {
    echo "Invalid request method.";
}

// Close the database connection
mysqli_close($db);
?>
