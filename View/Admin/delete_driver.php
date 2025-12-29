<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

if (isset($_GET['id'])) {
    $driver_id = $_GET['id'];

    // Start a transaction to ensure data integrity
    mysqli_begin_transaction($db);

    try {
        // Step 1: Move the data to the archive table
        $sql_archive = "INSERT INTO archive_driver_shuttle_info (driver_id, driver_first_name, driver_last_name, email, driver_status, available_seats, vehicle_name , vehicle_type)
                SELECT 
                    for_driver_registration_tbl.driver_id, 
                    for_driver_registration_tbl.driver_first_name, 
                    for_driver_registration_tbl.driver_last_name, 
                    for_driver_registration_tbl.email,
                    for_driver_registration_tbl.driver_status, 
                    create_shuttle_tbl.available_seats, 
                    create_shuttle_tbl.vehicle_name,
                    create_shuttle_tbl.vehicle_type
                FROM 
                    for_driver_registration_tbl
                INNER JOIN 
                    create_shuttle_tbl ON create_shuttle_tbl.driver_id = for_driver_registration_tbl.driver_id
                WHERE 
                    for_driver_registration_tbl.driver_id = ?";

        
        $stmt = $db->prepare($sql_archive);
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();

        // Step 2: Delete the record from the original tables
        $sql_delete = "DELETE FROM for_driver_registration_tbl WHERE driver_id = ?";
        $stmt_delete = $db->prepare($sql_delete);
        $stmt_delete->bind_param("i", $driver_id);
        $stmt_delete->execute();

        $sql_delete_shuttle = "DELETE FROM create_shuttle_tbl WHERE driver_id = ?";
        $stmt_delete_shuttle = $db->prepare($sql_delete_shuttle);
        $stmt_delete_shuttle->bind_param("i", $driver_id);
        $stmt_delete_shuttle->execute();

        // Commit the transaction
        mysqli_commit($db);

        // Redirect back to the dashboard
        echo '<script language="javascript">
              alert("Delete successfully! If you want to restore the data, please go to the archive.");
              window.location="driver_dashboard.php";
          </script>';
        //header("Location: driver_dashboard.php?success=1");
    } catch (Exception $e) {
     
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Driver</title>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script language="javascript">
// SweetAlert2 confirmation prompt
Swal.fire({
    title: "Are you sure?",
    text: "Do you really want to delete this driver and all related records?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "No, keep it"
}).then((result) => {
    if (result.isConfirmed) {
        // If confirmed, proceed with deletion via PHP
        window.location = "delete_driver_confirm.php?id=<?php echo $id; ?>"; // Redirect to a separate file to handle the deletion
    } else {
        // If canceled, redirect back to the driver dashboard
        window.location = "driver_dashboard.php";
    }
});
</script>

</body>
</html>
