<?php 

include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/login_admin_controller.php';
//include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/active.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';


$sql = "SELECT 
          for_driver_registration_tbl.driver_id,
 		  for_driver_registration_tbl.email , 
          for_driver_registration_tbl.driver_last_name, 
          for_driver_registration_tbl.driver_first_name,
      
          for_driver_registration_tbl.driver_status, 
          create_shuttle_tbl.available_seats, 
          create_shuttle_tbl.vehicle_name
           
        FROM 
         for_driver_registration_tbl
        INNER JOIN 
         create_shuttle_tbl ON create_shuttle_tbl.driver_id = for_driver_registration_tbl.driver_id;
 ";

$execute = mysqli_query($db , $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="main-content">
<div class="container mt-5">
    <h2 class="mb-4">Driver Dashboard</h2>
    <table class="table table-hover table-striped">
   
        <thead class="thead-dark">
       
            <tr>
            <!--<a href="create_driver_info.php" class="btn btn-primary btn-sm">CREATE </a> -->
                <th scope="col">#</th>
                <!-- <th scope="col">Driver Username</th>
                <th scope="col">Driver Email</th> -->
                <th scope="col">Driver First Name</th>
                <th scope="col">Driver Last Name</th>
                <th scope="col">Status</th>
                <th scope="col">Vehicle</th>
                <th scope="col">Available Seats</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>

        <?php 
        
        $counter = 1;
        while($rows = mysqli_fetch_assoc($execute)) { 
            $driver = $rows['driver_id'];
          //  $username = $rows['driver_username'];
            $email = $rows['email'];
            $first_name = $rows['driver_first_name'];
            $last_name = $rows['driver_last_name'];
            $status = $rows['driver_status'];
            $available_seats = $rows['available_seats'];
            $vehicle_name = $rows['vehicle_name'];




        ?>



            <tr>
                <td>Driver # <?php echo $counter++; ?></td>
                <!-- <td><//?php echo $username; ?></td>
                <td><//?php echo $email; ?></td> -->
                <td><?php echo $first_name; ?></td>
                <td><?php echo $last_name; ?></td>
                <td><?php
                //For checking status
                if($status == 1 ) {

                  echo "<p><a href='active.php?id=".$driver."&status=0' class='btn btn-success btn-sm'>Active</a></p>";

                } else {

                    echo "<p><a href='active.php?id=".$driver."&status=2' class='btn btn-danger btn-sm'>Deactive </a></p>";

                }
                ?> </td>

                <td><?php echo $vehicle_name; ?></td>
                <td><?php echo $available_seats; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $driver; ?>" class="btn btn-success btn-sm">Edit</a>
                    <a href="delete_driver.php?id=<?php echo $driver; ?>" class="btn btn-danger btn-sm">Delete</a>
                    
                </td>
            </tr>


        <?php 
        }
        ?>

      
        </tbody>
    </table>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {
        $('table').DataTable({
            dom: 'Bfrtip',
            buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5']
        });
    });
</script>

</body>
</html>
