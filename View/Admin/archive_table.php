<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php'; // Database connection
 
// Retrieve archived data
$sql = "SELECT * FROM archive_driver_shuttle_info";
$result = $db->query($sql);
 
// Check if query was successful
if (!$result) {
    die("Error: " . $db->error);
}
 
// Handle permanent delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
 
    // Delete from archive table
    $delete_sql = "DELETE FROM archive_driver_shuttle_info WHERE archive_id = ?";
    if ($stmt = $db->prepare($delete_sql)) {
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: archive_table.php"); // Redirect to refresh the page
    }
}
 
// Handle retrieval from archive
if (isset($_GET['retrieve_id'])) {
    $retrieve_id = $_GET['retrieve_id'];
 
    // Retrieve archived data
    $retrieve_sql = "SELECT * FROM archive_driver_shuttle_info WHERE archive_id = ?";
    if ($stmt = $db->prepare($retrieve_sql)) {
        $stmt->bind_param("i", $retrieve_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
 
        // Ensure data exists before proceeding
        if ($row) {
            // Check if required fields are not null
            if (empty($row['driver_id']) || empty($row['driver_first_name']) || empty($row['driver_last_name']) || empty($row['driver_status'])) {
                echo "Error: Missing required data!";
                exit;
            }
 
            // Check if the driver already exists in for_driver_registration_tbl
            $check_sql = "SELECT driver_id , driver_first_name , driver_last_name , driver_status  FROM for_driver_registration_tbl WHERE driver_id = ?";
            if ($check_stmt = $db->prepare($check_sql)) {
                $check_stmt->bind_param("i", $row['driver_id']);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
 
                // If the driver exists, update the record
                if ($check_result->num_rows > 0) {
                    $update_sql = "UPDATE for_driver_registration_tbl
                                   SET driver_first_name = ?, driver_last_name = ?, driver_status = ? , email = ?
                                   WHERE driver_id = ?";
                    if ($update_stmt = $db->prepare($update_sql)) {
                        $update_stmt->bind_param("sssis",
                            $row['driver_first_name'],
                            $row['driver_last_name'],
                            $row['driver_status'],
                            $row['email'],
                            $row['driver_id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                } else {
                   // If the driver does not exist, insert new record
$insert_sql_driver = "INSERT INTO for_driver_registration_tbl (driver_id, driver_first_name, driver_last_name, email,  driver_status)
VALUES (?, ?, ?, ? , ?)";
if ($insert_stmt_driver = $db->prepare($insert_sql_driver))
$insert_stmt_driver->bind_param("issss",$row['driver_id'],  $row['driver_first_name'], $row['driver_last_name'], $row['email'] , $row['driver_status'] );
$insert_stmt_driver->execute();
$insert_stmt_driver->close();
}
 
               
           
 
            // Insert back into create_shuttle_tbl
            $insert_sql = "INSERT INTO create_shuttle_tbl (driver_id, available_seats, vehicle_name , vehicle_type)
                           VALUES (?, ?, ? , ?)";
            if ($insert_stmt = $db->prepare($insert_sql)) {
                $insert_stmt->bind_param("iiss", $row['driver_id'], $row['available_seats'], $row['vehicle_name'] , $row['vehicle_type']);
                $insert_stmt->execute();
                $insert_stmt->close();
 
                // Delete from the archive after retrieving
                $delete_sql = "DELETE FROM archive_driver_shuttle_info WHERE archive_id = ?";
                if ($delete_stmt = $db->prepare($delete_sql)) {
                    $delete_stmt->bind_param("i", $retrieve_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
 
                echo '<script language="javascript">
                    alert("Restore successfully!");
                    window.location="archive_table.php"; // Redirect after successful restore
                </script>';
            }
        } else {
            echo "No data found for the given archive ID.";
        }
    }
 
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive Table</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            margin-top: 50px;
        }
        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        .btn-success {
            background-color: #2ecc71;
            border-color: #2ecc71;
        }
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
    </style>
</head>
<body>
 
<div class="container">
    <h2 class="text-center mb-4">Archived Driver and Shuttle Data</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Driver ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Driver Status</th>
                <th>Available Seats</th>
                <th>Vehicle Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['driver_id']) . "</td>";
                    echo "<td>" . htmlspecialchars(trim($row['driver_first_name'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['driver_last_name']) . "</td>";          
                    echo "<td>" . htmlspecialchars($row['driver_status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['available_seats']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['vehicle_name']) . "</td>";
                    echo "<td>
                        <a href='?delete_id=" . $row['archive_id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to permanently delete this record?\")'>Delete</a>
                         <a href='?retrieve_id=" . $row['archive_id'] . "' class='btn btn-success' onclick='return confirm(\"Are you sure you want to retrieve this record?\")'>Retrieve</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No archived records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
 
</body>
</html>