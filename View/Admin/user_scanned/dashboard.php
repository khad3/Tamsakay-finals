<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

$select = "
    SELECT location, COUNT(*) as total_passenger
    FROM passenger_logs_hed_tbl
    WHERE location IN ('HED', 'BED', 'MAINGATE', 'WALTERMART')
    GROUP BY location
";
$execute = mysqli_query($db, $select);

// Store data and assign specific colors
$chartData = [];
$colors = [];
while ($row = mysqli_fetch_assoc($execute)) {
    $chartData[] = "['" . $row['location'] . "', " . $row['total_passenger'] . "]";
    
    // Assign a specific color per location
    switch ($row['location']) {
        case 'HED':
            $colors[] = '#4CAF50'; // Green
            break;
        case 'BED':
            $colors[] = '#2196F3'; // Blue
            break;
        case 'MAINGATE':
            $colors[] = '#FFC107'; // Amber
            break;
        case 'WALTERMART':
            $colors[] = '#F44336'; // Red
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Location', 'Total Passengers'],
                <?php echo implode(",", $chartData); ?>
            ]);

            var options = {
                title: 'Total Passengers per Location',
                chartArea: {width: '70%'},
                hAxis: {title: 'Location'},
                vAxis: {minValue: 0, title: 'Total number of students'},
                colors: <?php echo json_encode($colors); ?>
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('column'));
            chart.draw(data, options);
        }
    </script>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/chart.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/js.js"></script>
    <link rel="shortcut icon" href="https://upload.wikimedia.org/wikipedia/en/thumb/7/72/FEU_Tamaraws_official_logo.svg/800px-FEU_Tamaraws_official_logo.svg.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<div id="column" style="width: 900px; height: 500px;"></div>

</body>
</html>
