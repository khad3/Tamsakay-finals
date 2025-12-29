<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

// Execute the SQL query and check for errors
$sql = "SELECT * FROM qrcode_tbl_walter";
$execute = mysqli_query($db, $sql);

if (!$execute) {
    die("Query failed: " . mysqli_error($db)); // Display the error if query fails
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Info</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">QR Code Information</h2>
    <table class="table table-hover table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Image QR</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($rows = mysqli_fetch_assoc($execute)) { 
                $image = $rows['qr_code_path']; 
                $relativeImagePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $image); // Convert to relative path
                $id = $rows['walter_id']; // Assuming there's an 'id' column for deletion
            ?>
                <tr>
                    <td><img src="<?php echo $relativeImagePath; ?>" alt="QR Code" style="width:100px; height:100px;"></td>
                    <td>
                        <button class="btn btn-info view-btn" data-id="<?php echo $id; ?>">View</button>
                        <button class="btn btn-danger delete-btn" data-id="<?php echo $id; ?>">Delete</button>
                    </td>
                </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>

    <!-- Modal for Viewing Details -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">QR Code Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- QR Code Image -->
                    <img id="qrCodeImage" src="" alt="" style="width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
// View button click event
$(document).on('click', '.view-btn', function() {
    var id = $(this).data('id');
    var imgSrc = $(this).closest('tr').find('img').attr('src');
    
    $('#qrCodeImage').attr('src', imgSrc); // Set the image source in modal
    $('#viewModal').modal('show'); // Show the modal
});

// Delete button click event
$(document).on('click', '.delete-btn', function() {
    var id = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this QR code?')) {
        $.ajax({
            url: 'delete_walter.php', // Change to your delete script
            type: 'POST',
            data: { id: id },
            success: function(response) {
                alert(response);
                location.reload(); // Reload the page to see changes
            },
            error: function() {
                alert('Error deleting QR code.');
            }
        });
    }
});
</script>

</body>
</html>
