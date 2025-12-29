<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/create.css">
    <title>Assigning Shuttle</title>
</head>
<body>
    <div class="container-fluid bg-dark text-light py-3">
        <header class="text-center">
            <h1 class="display-6">Assigning Shuttle</h1>
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
        $driver_id = ''; // Initialize driver ID variable
        $driver_first_name = '';
        $driver_last_name = '';
        $shuttle_id = ''; // Initialize shuttle ID variable

        // Check if driver_id is passed in URL
        if (isset($_GET['id'])) {
            $driver_id = $_GET['id'];

            // Fetch the driver details based on driver_id
            $driver_query = "SELECT * FROM for_driver_registration_tbl WHERE driver_id=?";
            if ($stmt = $db->prepare($driver_query)) {
                $stmt->bind_param("i", $driver_id);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if driver exists
                if ($result->num_rows == 1) {
                    $driver = $result->fetch_assoc();
                    $driver_first_name = $driver['driver_first_name'];
                    $driver_last_name = $driver['driver_last_name'];
                }
                $stmt->close();
            }
        }

        //fetch if driver already has a shuttle
        $shuttle_query = "SELECT driver_id FROM create_shuttle_tbl WHERE driver_id = ?";
        if ($stmt = $db->prepare($shuttle_query)) {
            $stmt->bind_param("i", $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo '<div class="alert alert-danger" role="alert">Driver already has a shuttle!</div>';
                exit();
            }
            $stmt->close();
        }

        // Fetch shuttles from the database
        $shuttles = [];
        $shuttle_query = "SELECT shuttle_id, vehicle_name FROM create_shuttle_tbl"; 
        $result = $db->query($shuttle_query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $shuttles[] = $row; 
            }
        }

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign'])) {
            // Get form data
            $shuttle_id = $_POST['shuttle_id']; 

            // Validate inputs
            if (empty($shuttle_id)) {
                echo '<div class="alert alert-danger" role="alert">Please select a shuttle!</div>';
            } else {
                // Prepare the SQL update query
                $update_query = "UPDATE create_shuttle_tbl SET driver_id=? WHERE shuttle_id=?";

                // Prepare and bind
                if ($stmt = $db->prepare($update_query)) {
                    $stmt->bind_param("ii", $driver_id, $shuttle_id);
                    
                    // Execute the statement
                    if ($stmt->execute()) {
                        // Successful update: Redirect to driver_dashboard.php
                        echo '<script language="javascript">
                            alert("Shuttle assigned to driver successfully!");
                            window.location = "driver_dashboard.php"; 
                        </script>';
                        exit(); // Terminate script after redirect
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Error assigning shuttle. Please try again.</div>';
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
            <h1>Assign a Shuttle to Driver: <?php echo htmlspecialchars($driver_first_name . ' ' . $driver_last_name); ?></h1>
            <div class="col-md-4">
                <label for="shuttleSelect" class="form-label">Select Shuttle</label>
                <select class="form-control" id="shuttleSelect" name="shuttle_id" required>
                    <option value="">Select Shuttle</option>
                    <?php foreach ($shuttles as $shuttle): ?>
                        <option value="<?php echo $shuttle['shuttle_id']; ?>"><?php echo htmlspecialchars($shuttle['vehicle_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <center><button type="submit" class="btn btn-success" name="assign">ASSIGN</button></center>
            </div>
        </form>
    </section>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
