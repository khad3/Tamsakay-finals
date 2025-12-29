<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/create.css">
    <title>Assigning Driver</title>
</head>
<body>
    <div class="container-fluid bg-dark text-light py-3">
        <header class="text-center">
            <h1 class="display-6">Assigning Driver</h1>
        </header>
    </div>
    <section class="container my-2 bg-dark w-50 text-light p-2">
        <?php
        // Include database connection file
        include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php'; 

        // Check if connection is established
        if (!isset($db)) {
            die("Database connection variable is not set.");
        }

        // Initialize variables
        $vehiclename = '';
        $vehicletype = '';
        $plateno = '';
        $availableseats = '';
        $shuttle_id = ''; // Initialize shuttle ID variable

        // Check if shuttle_id is passed in URL
        if (isset($_GET['id'])) {
            $shuttle_id = $_GET['id'];

            // Fetch the shuttle details based on shuttle_id
            $shuttle_query = "SELECT * FROM create_shuttle_tbl WHERE shuttle_id=?";
            if ($stmt = $db->prepare($shuttle_query)) {
                $stmt->bind_param("i", $shuttle_id);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if shuttle exists
                if ($result->num_rows == 1) {
                    $shuttle = $result->fetch_assoc();
                    $vehiclename = $shuttle['vehicle_name'];
                    $vehicletype = $shuttle['vehicle_type'];
                    $plateno = $shuttle['plate_no'];
                    $availableseats = $shuttle['available_seats'];
                }
                $stmt->close();
            }
        }

        //fetch if the driver already has a shuttle
        $drivers = [];
        $driver_query = "SELECT driver_id, driver_first_name , driver_last_name FROM for_driver_registration_tbl"; 
        $result = $db->query($driver_query);

        if ($result && $result->num_rows > 1) {
            while ($row = $result->fetch_assoc()) {
                $drivers_has_shuttle[] = $row; 
            }
        }

        // Fetch drivers from the database
        $drivers = [];
        $driver_query = "
            SELECT d.driver_id, d.driver_first_name, d.driver_last_name, 
                   IF(s.driver_id IS NOT NULL, 1, 0) AS has_shuttle
            FROM for_driver_registration_tbl d
            LEFT JOIN create_shuttle_tbl s ON d.driver_id = s.driver_id
        "; 
        
        $result = $db->query($driver_query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $drivers[] = $row;
                
                // Display driver name, disabled if already has a shuttle
                if ($row['has_shuttle']) {
                   // echo '<option value="' . $row['driver_id'] . '" disabled>' . 
                          //  $row['driver_first_name'] . ' ' . $row['driver_last_name'] . 
                       //  ' (Already Assigned)</option>';
                } else {
                   // echo '<option value="' . $row['driver_id'] . '">' . 
                       //     $row['driver_first_name'] . ' ' . $row['driver_last_name'] . 
                       //  '</option>';
                }
            }
        }
        
        
        

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create'])) {
            // Get form data
            $vehicle_name = $_POST['vehicle_name'];
            $vehicle_type = $_POST['vehicle_type'];
            $plate_no = $_POST['plate_no'];
            $available_seat = $_POST['available_seat'];
            $driver_id = $_POST['driver_id']; 

            // Validate inputs
            if (empty($vehicle_name) || empty($vehicle_type) || empty($plate_no) || empty($available_seat) || empty($driver_id)) {
                echo '<div class="alert alert-danger" role="alert">All fields are required!</div>';
            } else {
                // Prepare the SQL update query
                $update_query = "UPDATE create_shuttle_tbl SET vehicle_type=?, plate_no=?, available_seats=?, driver_id=? WHERE shuttle_id=?";

                
                // Prepare and bind
                if ($stmt = $db->prepare($update_query)) {
                    $stmt->bind_param("ssiii", $vehicle_type, $plate_no, $available_seat, $driver_id, $shuttle_id);
                    
                   // Execute the statement
            if ($stmt->execute()) {
                // Successful update: Redirect to driver_dashboard.php
                 //for successfull insert the data
     echo '<script language="javascript">
     alert("Assign Driver Successfully!");
    window.location = "driver_dashboard.php"; 
    </script>';
                exit(); // Terminate script after redirect
            } else {
                echo '<div class="alert alert-danger" role="alert">Error updating details. Please try again.</div>';
            }

            // Close statement
            $stmt->close();
        } else {
            echo '<div class="alert alert-danger" role="alert">Database error. Please try again later.</div>';
        }
            }
        }
        ?>

        <form class="row g-3 p-3" method="POST">
            <h1>Assign a Driver to this Shuttle</h1>
            <div class="col-md-4">
                <label for="validationDefault01" class="form-label">Vehicle name</label>
                <input type="text" class="form-control" id="validationDefault01" value="<?php echo htmlspecialchars($vehiclename); ?>" name="vehicle_name" required>
            </div>
            <br>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="vehicleType">Vehicle Type</label>
                    <select class="form-control" id="vehicleType" name="vehicle_type" required>
                        <option value="">Please select type of vehicle</option>
                        <option value="VAN" <?php echo ($vehicletype == "VAN") ? 'selected' : ''; ?>>VAN</option>
                        <option value="BUS" <?php echo ($vehicletype == "BUS") ? 'selected' : ''; ?>>BUS</option>
                        <option value="L3" <?php echo ($vehicletype == "L3") ? 'selected' : ''; ?>>L3</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label for="inputEmail4" class="form-label">Plate no.</label>
                <input type="text" class="form-control" id="inputEmail4" name="plate_no" value="<?php echo htmlspecialchars($plateno); ?>" required>
            </div>
            <div class="col-md-4">
                <label for="inputEmail4" class="form-label">Available seats</label>
                <input type="number" class="form-control" id="inputEmail4" name="available_seat" value="<?php echo htmlspecialchars($availableseats); ?>" required>
            </div>
            <div class="col-md-4">
    <label for="driverSelect" class="form-label">Assign Driver</label>
    <select class="form-control" id="driverSelect" name="driver_id" required>
        <option value="">Select Driver</option>
        <?php foreach ($drivers as $driver): ?>
            <option value="<?php echo $driver['driver_id']; ?>" 
                <?php echo ($driver['has_shuttle']) ? 'disabled' : ''; ?>>
                <?php 
                    echo htmlspecialchars($driver['driver_first_name'] . ' ' . $driver['driver_last_name']); 
                    echo ($driver['has_shuttle']) ? ' (Already Assigned)' : ''; 
                ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>


            <div class="col-12">
                <center><button type="submit" class="btn btn-success" name="create">UPDATE</button></center>
            </div>
        </form>
    </section>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
