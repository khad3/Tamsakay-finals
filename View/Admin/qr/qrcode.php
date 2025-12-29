<?php 


//include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/active.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

$sql = "SELECT 
          
          *

        FROM 
          passenger_logs_hed_tbl";

$execute = mysqli_query($db , $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qrcode Info</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>


<div class="container mt-5">
    <h2 class="mb-4">Qrcode Information</h2>
    <table class="table table-hover table-striped">
   
        <thead class="thead-dark">
       
            <tr>
            
           
               
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>


    
       
        <tr>
        <td><a href="generate_qrcode_hed.php" class="btn btn-success btn-sm">Generate Qrcode HED</a></td>
        <td><a href="view_qr_hed.php" class="btn btn-primary btn-sm">View Qrcode HED</a></td>

        </tr>

        <tr>
        <td><a href="generate_qrcode_bed.php" class="btn btn-success btn-sm">Generate Qrcode BED</a></td>
        <td><a href="view_qr_bed.php" class="btn btn-primary btn-sm">View Qrcode BED</a></td>
    
          
        </tr>
        <tr>
        <td><a href="generate_qrcode_maingate.php" class="btn btn-success btn-sm">Generate Qrcode MAINGATE</a></td>
        <td><a href="view_qr_maingate.php" class="btn btn-primary btn-sm">View Qrcode MAINGATE</a></td>
      
        </tr>

        <tr>
        <td><a href="generate_qrcode_walter.php" class="btn btn-success btn-sm">Generate Qrcode WALTER MART</a></td>
        <td><a href="view_qr_walter.php" class="btn btn-primary btn-sm">View Qrcode WALTER MART</a></td>
      
        </tr>
            
        
    </tr>



      
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
