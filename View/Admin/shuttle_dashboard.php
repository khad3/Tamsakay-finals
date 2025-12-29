<?php 

include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/login_admin_controller.php';

//include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/active.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

$sql = "SELECT 
          
          *

        FROM 
          create_shuttle_tbl";

$execute = mysqli_query($db , $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shuttle Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>


<div class="container mt-5">
<a href="create_shuttle_info.php" class="btns btn-primary btn-sm">CREATE </a>
    <h2 class="mb-4">Shuttle Dashboard</h2>
    <table class="table table-hover table-striped">
   
        <thead class="thead-dark">
       
            <tr>
            
           
                <th scope="col">Shuttle #</th>
                <th scope="col">Vehicle name</th>
                <th scope="col">Vehicle type</th>
                <th scope="col">Plate no.</th>
                <th scope="col">Availble seat</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>

        <?php 
        
        $counter = 1;
        while($rows = mysqli_fetch_assoc($execute)) { 
            $shuttle_id = $rows['shuttle_id'];
            $vehicle_name = $rows['vehicle_name'];
            $vehicle_type = $rows['vehicle_type'];
            $plate_no = $rows['plate_no'];
            $available_seats = $rows['available_seats'];
        
        ?>

            <tr>
                <td>Shuttle #<?php echo $counter++; ?></td>
                <td><?php echo $vehicle_name; ?></td>
                <td><?php echo $vehicle_type; ?></td>
                <td><?php echo $plate_no; ?></td>
                <td><?php echo $available_seats; ?></td>
                <td>
                    <a href="shuttle_edit.php?id=<?php echo $shuttle_id; ?>" class="btn btn-success btn-sm">Edit</a>
                    <a href="shuttle_delete.php?id=<?php echo $shuttle_id; ?>" class="btn btn-danger btn-sm">Delete</a>
                    <a href="assign_driver.php?id=<?php echo $shuttle_id; ?>" class="btn btn-warning btn-sm">Assign</a>
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
