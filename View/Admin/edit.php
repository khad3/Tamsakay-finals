
<?php

include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/login_admin_controller.php';
//include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/active.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';
$id = $_GET['id'];
$sql = "SELECT 
          for_driver_registration_tbl.driver_id,
          for_driver_registration_tbl.email,
          for_driver_registration_tbl.driver_first_name, 
          for_driver_registration_tbl.driver_last_name, 
          for_driver_registration_tbl.driver_status, 
          create_shuttle_tbl.available_seats, 
           create_shuttle_tbl.vehicle_name
        FROM 
          create_shuttle_tbl
        INNER JOIN 
          for_driver_registration_tbl ON  create_shuttle_tbl.driver_id =  for_driver_registration_tbl.driver_id
        WHERE create_shuttle_tbl.driver_id = '$id'";

$execute = mysqli_query($db , $sql);

while($rows = mysqli_fetch_assoc($execute)) { 

    $driver = $rows['driver_id'];
    $email = $rows['email'];
    $first_name = $rows['driver_first_name'];
    $last_name = $rows['driver_last_name'];
    $status = $rows['driver_status'];
    $available_seats = $rows['available_seats'];
    $vehicle_name = $rows['vehicle_name'];


}

if(isset($_POST['edit'])) {
  
  //$email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $status = $_POST['status'] ?? 2; // Default to 2 (Inactive) if not submitted yet
    $vehicle_name = $_POST['vehicle_name'];
    $available_seat = $_POST['available_seat'];
   

    $update = "UPDATE for_driver_registration_tbl 
    INNER JOIN  create_shuttle_tbl  ON for_driver_registration_tbl.driver_id = create_shuttle_tbl.driver_id
    SET 
        for_driver_registration_tbl.driver_first_name = '$first_name',
        for_driver_registration_tbl.driver_last_name = '$last_name',
        for_driver_registration_tbl.driver_status = '$status',
        create_shuttle_tbl.vehicle_name = '$vehicle_name',
        create_shuttle_tbl.available_seats = '$available_seat'
    WHERE for_driver_registration_tbl.driver_id = '$id';";


    $execute = mysqli_query($db , $update);
    
    if($execute){ 

        //for successfull insert the data
     echo '<script language="javascript">
     alert("Edit Details Successfully!");
    window.location = "driver_dashboard.php"; 
    </script>';


    } else {

        echo '<script language="javascript">
        alert("Edit Details Unsuccessfully!");
       window.location = "driver_dashboard.php"; 
       </script>';


    }
}





?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="css/create.css">
    <title>Edit</title>
    <style>

        body {
    background-color: #f8f9fa; /* Light background color */
}

.header {
    background-color: #343a40; /* Dark header */
    color: #ffffff; /* White text */
    padding: 20px 0; /* Padding for the header */
}

h1 {
    font-size: 2rem; /* Larger font size for headers */
    margin-bottom: 20px; /* Space below headers */
}

.container {
    border-radius: 8px; /* Rounded corners for the container */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

.form-label {
    font-weight: bold; /* Bold labels for better readability */
}

.btn-success {
    background-color: #28a745; /* Bootstrap success color */
    border-color: #28a745; /* Matching border color */
}

.btn-success:hover {
    background-color: #218838; /* Darker green on hover */
    border-color: #1e7e34; /* Darker border on hover */
}

.input-group-text {
    background-color: #343a40; /* Dark input group text background */
    color: #ffffff; /* White text for input group */
}

    </style>
  </head>
  <body>
   <div class="container-fluid bg-dark text-light py-3">
       <header class="text-center">
           <h1 class="display-6">SHUTTLE SERVICE</h1>
       </header>
   </div>
   <br>
 
   <section class="container my-2 bg-dark w-50 text-light p-2">
    <form class="row g-3 p-3" method = "POST">
        <h1>Driver information</h1>
        <!-- <div class="col-md-4">
            <label for="validationDefault01" class="form-label">Driver Email</label>
            <input type="text" class="form-control" id="validationDefault01" name = "email" value="<//?php echo "$email"?>" required>
          </div> -->
        <div class="col-md-4">
            <label for="validationDefault01" class="form-label">First name</label>
            <input type="text" class="form-control" id="validationDefault01" name = "first_name" value="<?php echo "$first_name"?>" required>
          </div>
          <div class="col-md-4">
            <label for="validationDefault02" class="form-label">Last name</label>
            <input type="text" class="form-control" id="validationDefault02" name = "last_name" value="<?php echo "$last_name"?>" required>
          </div>
          <div class="col-md-4">
  <label for="statusSwitch" class="form-label">Status</label>
  <div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status" 
      value="<?php echo ($status == 1) ? 1 : 2; ?>" 
      <?php echo ($status == 1) ? 'checked' : ''; ?> 
      onchange="updateStatus(this)">
    <label class="form-check-label" id="statusLabel" for="statusSwitch">
      <?php echo ($status == 1) ? 'Active' : 'Inactive'; ?>
    </label>
  </div>
</div>

<script>
  function updateStatus(switchElem) {
    const label = document.getElementById('statusLabel');
    if (switchElem.checked) {
      switchElem.value = 1;
      label.textContent = 'Active';
    } else {
      switchElem.value = 3;
      label.textContent = 'Inactive';
    }
  }
</script>

          <h1>Shuttle information </h1>
        <div class="col-md-6">
          <label for="inputEmail4" class="form-label">Vehicle name</label>
          <input type="text" class="form-control" id="inputEmail4" name = "vehicle_name"  value="<?php echo "$vehicle_name"?> "  required>
        </div>
        <div class="col-md-6">
          <label for="inputPassword4" class="form-label">Available seats</label>
          <input type="text" class="form-control" id="inputPassword4" name = "available_seat"  value="<?php echo "$available_seats"?>"  required>
        </div>
        
        <div class="col-12">
          <center><button type="submit" class="btn btn-success" name = "edit">UPDATE </button> </center>
        </div>
      </form>
   </section>
  </body>
</html>