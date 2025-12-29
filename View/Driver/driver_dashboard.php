<?php
session_start(); // Start session
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

// Ensure driver ID is set in session
if (!isset($_SESSION['driver_logged_in']) || $_SESSION['driver_logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

if (!isset($_SESSION['driver_id'])) {
    echo 'Driver ID not set in session.';
    exit();
}

$driver_id = $_SESSION['driver_id'];

// Retrieve driver's first name
$retrieve_firstname = "SELECT driver_first_name FROM for_driver_registration_tbl WHERE driver_id = ?";
if ($stmt = $db->prepare($retrieve_firstname)) {
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $driver_first_name = $row['driver_first_name'];
    } else {
        echo '<div class="alert alert-danger" role="alert">Driver not found.</div>';
        exit();
    }
} else {
    echo '<div class="alert alert-danger" role="alert">Error retrieving driver first name.</div>';
    exit();
}

// Include your database connection file first
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

if (isset($_POST['status'])) {
    $status = $_POST['status'];

    // You need to know the current driver_id; this should be set from the session or passed via POST
    $driver_id = $_SESSION['driver_id'] ?? $_POST['driver_id'] ?? null;

    if ($driver_id === null) {
        echo json_encode(["success" => false, "message" => "Driver ID is missing."]);
        exit;
    }

    $insert_query = "
        INSERT INTO driver_status (driver_id, status_driver, Online_status) 
        VALUES (?, ?, 'Online') 
        ON DUPLICATE KEY UPDATE 
            status_driver = VALUES(status_driver),
            Online_status = 'Online'
    ";

    if ($stmt = $db->prepare($insert_query)) {
        $stmt->bind_param("is", $driver_id, $status); // i = int (driver_id), s = string (status)

        if ($stmt->execute()) {
           echo '<script language="javascript">
           alert("Status updated successfully!
                 status: + ' . $status . ' + ");
           window.location="driver_dashboard.php";
          </script>';
          
        // echo json_encode(["success" => true, "message" => "Status updated successfully!", "status" => $status]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating status: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare statement."]);
    }
} else {
   // echo json_encode(["success" => false, "message" => "Status parameter is missing."]);
}

?>


<!DOCTYPE html> 
<html lang="en"> 
<head> 
  <title>Driver Dashboard</title> 
  <meta charset="utf-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> 
  <style>
  body {
    font-family: 'Arial', sans-serif;
    background-color:#F6E96B;
    color: #2c3e50;
    font-size: 18px;
  }

  .navbar-inverse {
    background-color: #2c3e50;
    border: none;
  }

  .status-display {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
  }

  .navbar-inverse .navbar-brand,
  .navbar-inverse .navbar-nav > li > a {
    color: #ecf0f1;
    font-size: 20px;
  }

  .sidenav {
    background-color: #fff;
    height: 100%;
    padding-top: 20px;
    border-right: 1px solid #ddd;
  }

  .sidenav h2 {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 30px;
  }

  .sidenav .nav-pills > li > a {
    font-size: 18px;
    color: #2c3e50;
  }

  .sidenav .nav-pills > li.active > a {
    background-color: #2ecc71;
    color: white;
  }

  .well {
    background-color: #ffffff;
    border-radius: 10px;
    padding: 30px;
    border: 1px solid #ddd;
    margin-bottom: 25px;
    text-align: center;
  }

  .well h2 {
    font-size: 24px;
    margin-bottom: 15px;
    font-weight: bold;
  }

  .btn-info {
    background: linear-gradient(to right, #27ae60, #2ecc71);
    border: none;
    border-radius: 25px;
    padding: 12px 28px;
    font-size: 18px;
    color: white;
    font-weight: bold;
    transition: 0.3s ease-in-out;
  }

  .btn-info:hover {
    background: linear-gradient(to right, #2ecc71, #27ae60);
    transform: scale(1.05);
  }

  .status-btn {
    padding: 14px 30px;
    margin: 10px;
    font-size: 18px;
    font-weight: bold;
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: 0.3s ease;
  }

  .on-break {
    background: linear-gradient(to right, #f39c12, #f1c40f);
  }

  .on-the-way {
    background: linear-gradient(to right, #27ae60, #2ecc71);
  }

  .not-available {
    background: linear-gradient(to right, #e74c3c, #e67e22);
  }

  .status-btn:hover {
    opacity: 0.9;
    transform: scale(1.03);
  }

  h1 {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    margin-top: 20px;
  }

  #status-display {
    text-align: center;
    margin-top: 15px;
    font-size: 20px;
    color: #34495e;
  }

  @media screen and (max-width: 767px) {
    h1 {
    font-size: 46px;
    color:rgb(238, 252, 244);
    margin-top: 20px;
  }

  .row.content {
    height: auto;
  }

  .well {
    margin-top: 10px;
  }

  #status-display {
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    margin: 20px 0;
    color:rgb(230, 248, 238);
  }

  #current-status {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin: 20px 0;
    color:rgb(221, 236, 13);
  }

  #bed-count, #maingate-count, #hed-count {
    font-size: 26px;
    margin-top: 10px;
    color: rgb(0, 0, 0);
  }
}

</style>


</head> 
<body> 
  <nav class="navbar navbar-inverse visible-xs"> 
    <div class="container-fluid"> 
      <div class="navbar-header"> 
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar"> 
          <span class="icon-bar"></span> 
          <span class="icon-bar"></span> 
          <span class="icon-bar"></span>                         
        </button> 
        <a class="navbar-brand" href="#">Tamsakay</a> 
      </div> 
      <div class="collapse navbar-collapse" id="myNavbar"> 
        <ul class="nav navbar-nav"> 
          <li class="active"><a href="#">Dashboard</a></li> 
          <li><a href="/Tamsakay/View/Driver/settings_driver/setting_driver.php">Settings</a></li> 
          <li><a href="logout.php">Logout</a></li> 
        </ul> 
      </div> 
    </div> 
  </nav> 

  <div class="container-fluid"> 
    <div class="row content"> 
      <div class="col-sm-3 sidenav hidden-xs"> 
        <h2>Logo</h2> 
        <ul class="nav nav-pills nav-stacked"> 
          <li class="active"><a href="#section1">Dashboard</a></li> 
          <li><a href="/Tamsakay/View/Driver/settings_driver/setting_driver.php">Settings</a></li> 
        </ul><br> 
      </div> 

      <?php 

include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php'; 



// Query to count passengers per location 

$SELECT = "SELECT location, COUNT(*) as total_passenger 

           FROM passenger_logs_hed_tbl 

           WHERE location IN ('HED', 'BED', 'MAINGATE' , 'WALTERMART') 

           GROUP BY location"; 



$execute = mysqli_query($db, $SELECT); 



// Initialize location counts 

$passengerCounts = ['HED' => 0, 'BED' => 0, 'MAINGATE' => 0 , 'WALTERMART' => 0]; 



// Fetch results and populate the counts 

while ($row = mysqli_fetch_assoc($execute)) { 

    $location = $row['location']; 

    $passengerCounts[$location] = $row['total_passenger']; 

} 

?> 

      <div class="col-sm-9" > 
        <div class="well" style="background-color: #27ae60;"> 
          <h2 >HED</h2> 
          <button class="btn btn-info" onclick="dropOff('HED')">Drop Off</button> 
          <p id="hed-count"><?php echo $passengerCounts['HED']; ?> students</p> 
        </div> 

        <div class="row"> 
          <div class="col-sm-3"> 
            <div class="well" style="background-color:rgb(22, 197, 209);"> 
              <h2>BED</h2> 
              <button class="btn btn-info" onclick="dropOff('BED')">Drop Off</button> 
              <p id="bed-count"><?php echo $passengerCounts['BED']; ?> students</p>  
            </div> 
          </div> 

          <div class="col-sm-3"> 
            <div class="well" style="background-color:rgb(247, 244, 206);"> 
              <h2>MAIN GATE</h2> 
              <button class="btn btn-info" onclick="dropOff('MAINGATE')">Drop Off</button> 
              <p id="maingate-count"><?php echo $passengerCounts['MAINGATE']; ?> students</p>  
            </div> 
          </div> 
        </div> 

        <div class="col-sm-3"> 
            <div class="well" style="background-color:rgb(182, 211, 21);"> 
              <h2>WALTER MART</h2> 
              <button class="btn btn-info" onclick="dropOff('WALTERMART')">Drop Off</button> 
              <p id="walter-count"><?php echo $passengerCounts['WALTERMART']; ?> students</p>  
            </div> 
          </div> 
        </div> 
      </div>

      <script> 
        function dropOff(location) { 
          $.ajax({ 
              type: "POST", 
              url: "drop_off.php", 
              data: { location: location }, 
              dataType: "json", // Ensure the response is parsed as JSON 
              success: function(response) { 
                  console.log(response); // Log the response for debugging 
                  if (response.success) { 
                      document.getElementById(location.toLowerCase() + '-count').innerText = "0 students"; 
                      alert(response.message); 
                  } else { 
                      alert("Error: " + response.message); 
                  } 
              }, 
              error: function(jqXHR, textStatus, errorThrown) { 
                  console.log("AJAX error:", textStatus, errorThrown); // Log any AJAX errors 
                  alert("Failed to update drop-off. Please try again."); 
              } 
          }); 
        } 
      </script> 

      <h1 style = "color :rgb(37, 114, 38);">Hi, <?php echo $driver_first_name; ?> ! &#128525</h1>
   

      <form method="POST"> 
        <center> 
          <button type="submit" name="status" value="On Break" class="status-btn on-break">On Break</button> 
          <button type="submit" name="status" value="On the Way HED" class="status-btn on-the-way">On the Way <strong>HED</strong></button> 
          <button type="submit" name="status" value="On the Way MAINGATE" class="status-btn on-the-way">On the Way  <strong>MAINGATE</strong></button>
          <button type="submit" name="status" value="On the Way WALTERMART" class="status-btn on-the-way">On the Way  <strong>WALTERMART</strong></button>  
          <button type="submit" name="status" value="Not Available" class="status-btn not-available">Not Available</button> 
        </center> 
      </form> 

<p id="status-display" style="color:#2c3e50">Current Status: <strong><span id="current-status" style="color:#387F39"><?php echo isset($status) ? $status : "None"; ?></span></strong></p> 
    </div> 
  </div> 
</body> 
</html>
