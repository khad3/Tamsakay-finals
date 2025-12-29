<?php 

include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/login_admin_controller.php';
//include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/active.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

$sql = "
            

            WITH RankedDrivers AS (
                SELECT 
                    email, 
                    driver_id,
                  driver_first_name, 
                   driver_last_name, 
                    driver_username,
                    driver_status, 
                      
                    ROW_NUMBER() OVER (PARTITION BY email, for_driver_registration_tbl.driver_id ORDER BY driver_id) AS rn
                FROM 
                    for_driver_registration_tbl
              
            )

            select * from RankedDrivers
          ";

       
          
        

$execute = mysqli_query($db , $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>


<div class="container mt-5">

    <h2 class="mb-4">Driver Registration Dashboard (DRD)</h2>
    <table class="table table-hover table-striped">
   
        <thead class="thead-dark">
       
            <tr>
            
           
                <th scope="col">Driver #</th>
                <!-- <th scope="col">Driver Username</th>
                <th scope="col">Driver Email</th> -->
                <th scope="col">Driver firstname</th>
                <th scope="col">Driver lastname</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>

        <?php 
        
        $counter = 1;
        while($rows = mysqli_fetch_assoc($execute)) { 
            $driver_id = $rows['driver_id'];
            $username = $rows['driver_username'];
            $email = $rows['email'];
            $first_name = $rows['driver_first_name'];
            $last_name = $rows['driver_last_name'];
            $status = $rows['driver_status'];
        
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

                  echo "<p><a href='active_register.php?id=".$driver_id."&status=0' class='btn btn-success btn-sm'>Active</a></p>";

                } else if ($status == 0 ) {

                    echo "<p><a href='active_register.php?id=".$driver_id."&status=1' class='btn btn-warning btn-sm'>Waiting . . . </a></p>";

                }
                else {

                  echo "<p><a href='active_register.php?id=".$driver_id."&status=2' class='btn btn-danger btn-sm'>Deactive </a></p>";
                }
                ?> </td>

                <td>
                    <a href="edit_register_driver.php?id=<?php echo $driver_id; ?>" class="btn btn-success btn-sm">Edit</a>
                    <a href="delete_register_driver.php?id=<?php echo $driver_id; ?>" class="btn btn-danger btn-sm">Delete</a>
                    <a href="assign_shuttle.php?id=<?php echo $driver_id; ?>" class="btn btn-warning btn-sm">Assign</a>
                </td>
            </tr>


        <?php 
        }
        ?>

      
        </tbody>
    </table>
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
