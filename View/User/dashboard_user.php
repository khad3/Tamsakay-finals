<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_user.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

// Initialize variables
$is_user_in_log = false;
$user_id = intval($_SESSION['user_id']); // Get user_id from session
$username = "User "; // Default value

// Fetch the username from for_user_registration_tbl
$username_query = "SELECT first_name FROM for_user_registration_tbl WHERE user_id = ?";
if ($stmt = $db->prepare($username_query)) {
    $stmt->bind_param("i", $user_id); // Bind user ID
    $stmt->execute();
    $stmt->bind_result($fetched_username); // Fetch username
    if ($stmt->fetch()) {
        $first_name = $fetched_username; // Assign fetched username to variable
    }
    $stmt->close();
} else {
    echo "Error fetching username.";
}

$user_location = null; // Initialize $user_location
$valid_locations = ['HED', 'BED', 'MAINGATE']; // Valid locations

// Fetch the user's location from passenger_logs_hed_tbl
$sql = "SELECT location FROM passenger_logs_hed_tbl WHERE user_id = ?";
if ($stmt = $db->prepare($sql)) {
    $stmt->bind_param("i", $user_id); // Bind user ID
    $stmt->execute();
    $stmt->bind_result($location); // Fetch location
    if ($stmt->fetch()) {
        $is_user_in_log = true; // User is in log
        $user_location = $location; // Assign location
    }
    $stmt->close();
} else {
    echo "Error fetching user location.";
}

// Check if user_id exists in passenger_logs_hed_tbl
$sql = "SELECT COUNT(*) as count FROM passenger_logs_hed_tbl WHERE user_id = ?";
if ($stmt = $db->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $is_user_in_log = true; // User is in log
    }
    $stmt->close();
} else {
    echo "Error checking user log.";
}

// Initialize driver name and status
$driver_name = "Unknown Driver";
$driver_status = "No status set";
$vehicle_type = "Not available"; // Default value

// Ensure driver ID is set in session
if (isset($_SESSION['driver_id'])) {
    $driver_id = intval($_SESSION['driver_id']); // Get driver_id from session

    // Fetch driver's first name from for_driver_registration_tbl
    $driver_name_query = "SELECT driver_first_name FROM for_driver_registration_tbl WHERE driver_id = ?";
    if ($stmt = $db->prepare($driver_name_query)) {
        $stmt->bind_param("i", $driver_id); // Bind driver ID
        $stmt->execute();
        $stmt->bind_result($driver_first_name); // Fetch driver first name
        if ($stmt->fetch()) {
            $driver_name = $driver_first_name; // Assign name to variable
        }
        $stmt->close();
    } else {
        echo "Error fetching driver name.";
    }

    // Fetch driver's status from driver_status table
    $status_query = "SELECT status_driver FROM driver_status WHERE driver_id = ?";
    if ($stmt = $db->prepare($status_query)) {
        $stmt->bind_param("i", $driver_id); // Bind driver ID
        $stmt->execute();
        $stmt->bind_result($status_driver); // Fetch status
        if ($stmt->fetch()) {
            $driver_status = $status_driver; // Assign status to variable
        }
        $stmt->close();
    } else {
        echo "Error fetching driver status.";
    }

    // Fetch the type of shuttle assigned to the driver
    $shuttle_query = "SELECT vehicle_type FROM create_shuttle_tbl WHERE driver_id = ?";
    $vehicle_type = "Not available"; // Default value
    if ($stmt = $db->prepare($shuttle_query)) {
        $stmt->bind_param("i", $driver_id); // Bind driver ID
        $stmt->execute();
        $stmt->bind_result($fetched_vehicle_type); // Fetch result
        if ($stmt->fetch()) {
            $vehicle_type = $fetched_vehicle_type; // Assign fetched value
        } else {
            $vehicle_type = "Not assigned"; // Fallback value
        }
        $stmt->close();
    } else {
        echo "Error fetching shuttle information.";
    }

    // Fetch the picture URL for the driver
    $driver_pic_query = "SELECT picture_url FROM driver_profile_pic WHERE driver_id = ?";
    $driver_picture_url = "/Tamsakay/View/Driver/settings_driver/pfp/driver_tamtam.png"; // Default fallback image
    if ($stmt = $db->prepare($driver_pic_query)) {
        $stmt->bind_param("i", $driver_id); // Bind driver ID
        $stmt->execute();
        $stmt->bind_result($picture_url); // Fetch picture URL
        if ($stmt->fetch()) {
            $driver_picture_url = $picture_url; // Assign fetched URL
        }
        $stmt->close();
    } else {
        echo "Error fetching driver picture.";
    }
}
?>

<!DOCTYPE html> 
<html lang="en"> 
<head> 
  <title>User Dashboard</title> 
  <meta charset="utf-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> 
  <link href="https://feuroosevelt.edu.ph/wp-content/themes/feu_theme/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="https://feuroosevelt.edu.ph/wp-content/themes/feu_theme/assets/plugins/bootstrap341/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://feuroosevelt.edu.ph/wp-content/themes/feu_theme/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="https://feuroosevelt.edu.ph/wp-content/themes/feu_theme/assets/plugins/swiper/css/swiper.css" rel="stylesheet" type="text/css" media="screen" />
    <!-- END PLUGINS -->
    <!-- BEGIN PAGES CSS -->
    <link class="main-stylesheet" href="https://feuroosevelt.edu.ph/wp-content/themes/feu_theme/pages/css/pages.css" rel="stylesheet" type="text/css" />
    <link class="main-stylesheet" href="https://feuroosevelt.edu.ph/wp-content/themes/feu_theme/pages/css/pages-icons.css" rel="stylesheet" type="text/css" />
    <style>
    /* Global Styles */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color:#626F47;
    }

    header {
        background-color: #05683B;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .logo {
        width: 50px;
        margin-right: 15px;
    }

    header h1 {
        color: white;
        font-size: 24px;
        margin: 0;
    }

 /* Navbar for mobile */
.navbar-nav {
    display: flex;
    justify-content: space-around;
    align-items: center;
    width: 90%;
    background-color: #05683B;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(46, 230, 123, 0.45);
}

.navbar-nav a {
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    font-size: 18px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
}

/* Glowing hover effect */
.navbar-nav a:hover {
    background: rgba(20, 255, 236, 0.3);
    border-radius: 5px;
    box-shadow: 0 0 10px #A4B465, 0 0 20px #A4B465;
    color: #ffffff;
}

/* Add a gradient border animation */
.navbar-nav a:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 5px;
    border: 2px solid transparent;
    background: linear-gradient(90deg,rgb(25, 148, 87),rgb(86, 87, 87));
    background-size: 200%;
    z-index: -1;
    transition: all 0.4s ease;
    filter: blur(2px);
}

.navbar-nav a:hover:before {
    background-position: right;
    opacity: 1;
}

/* Navbar toggle button */
.navbar-inverse .navbar-toggle {
    background-color:rgba(67, 196, 99, 0.53);
    border: none;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
    padding: 5px 10px;
}

.navbar-inverse .navbar-toggle:hover {
    background: linear-gradient(45deg,rgb(70, 70, 70),rgba(0, 0, 0, 0.73));
    box-shadow: 0 0 10pxrgb(0, 0, 0), 0 0 rgb(20, 161, 91)ffec;
}

.navbar-inverse .navbar-toggle .icon-bar {
    background-color: white;
    border-radius: 2px;
    height: 4px;
    margin: 5px 0;
    transition: all 0.3s ease-in-out;
}

.navbar-inverse .navbar-toggle:hover .icon-bar {
    background-color: #0d7377;
}

    /* Buttons and content styling */
    .content {
        text-align: center; 
        margin: 20px;
    }

    .content h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }
    .container-fluid>.navbar-collapse, .container-fluid>.navbar-header, .container>.navbar-collapse, .container>.navbar-header {
    margin-right: -15px;
    margin-left: -15px;
    background-color: #A4B465;
    }

    .content button {
        background-color: #A4B465;
        color:rgb(252, 252, 252);
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        cursor: pointer;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
        margin: 10px;
    }

    .content button:hover {
        background-color: #0056b3;
    }

    /* Status Box Styling */
    .status-driver {
        margin-top: 30px;
        padding: 30px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 12px;
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
        width: 70%;
        margin: 30px auto;
        text-align: center;
    }

    .status-driver img {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 3px solid #007bff;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
        margin-bottom: 15px;
    }

    .status-driver h1,
    .status-driver h3 {
        margin: 10px 0;
        color: #444;
    }

    .status-driver h1 span,
    .status-driver h3 span {
        color: #007bff;
    }

    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            align-items: flex-start;
            display:none;
        }

        .navbar-nav {
            display: block;
            width: 100%;
        }

        .navbar-nav a {
            display: block;
            padding: 12px;
            font-size: 16px;
            text-align: left;
        }

        .content button {
            width: 100%;
            padding: 14px 0;
            font-size: 16px;
        }

        .status-driver {
            width: 90%;
            padding: 20px;
        }

        .status-driver img {
            width: 150px;
            height: 150px;
        }

        .btn {
          
            color: #ffffff;
        }
    }

    @media (max-width: 480px) {
        .content h2 {
            font-size: 20px;
        }

        .content button {
            font-size: 16px;
            padding: 12px 16px;
        }

        .status-driver h1 {
            font-size: 22px;
        }

        .status-driver h3 {
            font-size: 20px;
        }

        .btn {
          
          color: #ffffff;
      }
    }
</style>

</head>

<header>
    <img src="/Tamsakay/Tamsakay Logo.png" alt="Tamsakay Logo" class="logo">
    <h1>Tamsakay</h1>
    <a href="settings/settings_user.php" style="text-decoration: none; display: flex; align-items: center;">
    <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.2788 2.15224C13.9085 2 13.439 2 12.5 2C11.561 2 11.0915 2 10.7212 2.15224C10.2274 2.35523 9.83509 2.74458 9.63056 3.23463C9.53719 3.45834 9.50065 3.7185 9.48635 4.09799C9.46534 4.65568 9.17716 5.17189 8.69017 5.45093C8.20318 5.72996 7.60864 5.71954 7.11149 5.45876C6.77318 5.2813 6.52789 5.18262 6.28599 5.15102C5.75609 5.08178 5.22018 5.22429 4.79616 5.5472C4.47814 5.78938 4.24339 6.1929 3.7739 6.99993C3.30441 7.80697 3.06967 8.21048 3.01735 8.60491C2.94758 9.1308 3.09118 9.66266 3.41655 10.0835C3.56506 10.2756 3.77377 10.437 4.0977 10.639C4.57391 10.936 4.88032 11.4419 4.88029 12C4.88026 12.5581 4.57386 13.0639 4.0977 13.3608C3.77372 13.5629 3.56497 13.7244 3.41645 13.9165C3.09108 14.3373 2.94749 14.8691 3.01725 15.395C3.06957 15.7894 3.30432 16.193 3.7738 17C4.24329 17.807 4.47804 18.2106 4.79606 18.4527C5.22008 18.7756 5.75599 18.9181 6.28589 18.8489C6.52778 18.8173 6.77305 18.7186 7.11133 18.5412C7.60852 18.2804 8.2031 18.27 8.69012 18.549C9.17714 18.8281 9.46533 19.3443 9.48635 19.9021C9.50065 20.2815 9.53719 20.5417 9.63056 20.7654C9.83509 21.2554 10.2274 21.6448 10.7212 21.8478C11.0915 22 11.561 22 12.5 22C13.439 22 13.9085 22 14.2788 21.8478C14.7726 21.6448 15.1649 21.2554 15.3694 20.7654C15.4628 20.5417 15.4994 20.2815 15.5137 19.902C15.5347 19.3443 15.8228 18.8281 16.3098 18.549C16.7968 18.2699 17.3914 18.2804 17.8886 18.5412C18.2269 18.7186 18.4721 18.8172 18.714 18.8488C19.2439 18.9181 19.7798 18.7756 20.2038 18.4527C20.5219 18.2105 20.7566 17.807 21.2261 16.9999C21.6956 16.1929 21.9303 15.7894 21.9827 15.395C22.0524 14.8691 21.9088 14.3372 21.5835 13.9164C21.4349 13.7243 21.2262 13.5628 20.9022 13.3608C20.4261 13.0639 20.1197 12.558 20.1197 11.9999C20.1197 11.4418 20.4261 10.9361 20.9022 10.6392C21.2263 10.4371 21.435 10.2757 21.5836 10.0835C21.9089 9.66273 22.0525 9.13087 21.9828 8.60497C21.9304 8.21055 21.6957 7.80703 21.2262 7C20.7567 6.19297 20.522 5.78945 20.2039 5.54727C19.7799 5.22436 19.244 5.08185 18.7141 5.15109C18.4722 5.18269 18.2269 5.28136 17.8887 5.4588C17.3915 5.71959 16.7969 5.73002 16.3099 5.45096C15.8229 5.17191 15.5347 4.65566 15.5136 4.09794C15.4993 3.71848 15.4628 3.45833 15.3694 3.23463C15.1649 2.74458 14.7726 2.35523 14.2788 2.15224ZM12.5 15C14.1695 15 15.5228 13.6569 15.5228 12C15.5228 10.3431 14.1695 9 12.5 9C10.8305 9 9.47716 10.3431 9.47716 12C9.47716 13.6569 10.8305 15 12.5 15Z" fill="white"/>
    </svg>
    <span style="margin-left: 5px; color:rgb(255, 255, 255); font-weight: bold;">Settings</span>
</a>

</header>

<nav class="navbar navbar-inverse visible-xs">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
          
            <img src="/Tamsakay/Tamsakay Logo.png" alt="Tamsakay Logo" class="logo">
              <a class="navbar-brand" href="#" style="color:rgb(248, 239, 239);"><strong>Tamsakay</strong></a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li class="active"><a href="dashboard_user.php">Dashboard</a></li>
                <li><a href="settings/settings_user.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="content">
    <strong><h2 style = "color:#f9f9f9;">Welcome, <?php echo htmlspecialchars($first_name); ?>! &#128525</h2></strong>
    <a href="scanner.php">
        <button type="button" class="btn btn-primary">
            <b><strong>Scan QR Code</strong></b>
        </button>
    </a>
    
    <!-- Button for Waiting Room HED -->
    <a href="/Tamsakay/View/Admin/qr/User/waiting_room_hed.php">
        <button type="button" class="btn btn-primary" 
            <?php echo ($is_user_in_log && $user_location === 'HED') ? '' : 'disabled'; ?>>
            <b>Go to Waiting Room HED</b>
        </button>
    </a>

    <!-- Button for Waiting Room BED -->
    <a href="/Tamsakay/View/Admin/qr/User/waiting_room_bed.php">
        <button type="button" class="btn btn-primary" 
            <?php echo ($is_user_in_log && $user_location === 'BED') ? '' : 'disabled'; ?>>
            <b>Go to Waiting Room BED</b>
        </button>
    </a>

    <!-- Button for Waiting Room Gate -->
    <a href="/Tamsakay/View/Admin/qr/User/waiting_room_gate.php">
        <button type="button" class="btn btn-primary" 
            <?php echo ($is_user_in_log && $user_location === 'MAINGATE') ? '' : 'disabled'; ?>>
            <b>Go to Waiting Room Gate</b>
        </button>
    </a>

    <a href="/Tamsakay/View/Admin/qr/User/waiting_room_walter.php">
        <button type="button" class="btn btn-primary" 
            <?php echo ($is_user_in_log && $user_location === 'WALTERMART') ? '' : 'disabled'; ?>>
            <b>Go to Waiting Room Waltermart</b>
        </button>
    </a>
</div>

<?php 

$available_drivers = "
    SELECT 
        for_driver_registration_tbl.driver_first_name, 
        for_driver_registration_tbl.driver_last_name, 
        driver_status.Online_status,
        create_shuttle_tbl.vehicle_type, 
        driver_status.status_driver, 
        driver_profile_pic.picture_url AS driver_picture_url
    FROM for_driver_registration_tbl 
    INNER JOIN create_shuttle_tbl ON for_driver_registration_tbl.driver_id = create_shuttle_tbl.driver_id
    INNER JOIN driver_status ON for_driver_registration_tbl.driver_id = driver_status.driver_id
    LEFT JOIN driver_profile_pic ON for_driver_registration_tbl.driver_id = driver_profile_pic.driver_id
    WHERE driver_status.Online_status = 'Online' -- Only show drivers who are online
";

$execute = mysqli_query($db , $available_drivers);

if (!$execute) {
    // Display error if query fails
    echo "Error in query: " . mysqli_error($db);
} else {
    if (mysqli_num_rows($execute) > 0) {
        while ($row = mysqli_fetch_assoc($execute)) {
            $driver_first_name = $row['driver_first_name'];
            $driver_last_name = $row['driver_last_name'];
            $driver_picture_url = $row['driver_picture_url'] ?? '/Tamsakay/View/Driver/settings_driver/pfp/driver_tamtam.png'; // fallback image
            $vehicle_type = $row['vehicle_type'];
            $status_driver = $row['status_driver'];

            echo '<div class="status-driver">
                    <div class="image_ni_driver">
                        <h3>Picture ni Kuya Driver:</h3>
                        <img src="' . htmlspecialchars($driver_picture_url) . '" alt="Driver Picture">
                    </div>
                    <h1 style="text-align: justify; margin-left: 100px;">Driver: <span>' . htmlspecialchars($driver_first_name) . ' ' . htmlspecialchars($driver_last_name) . '</span></h1>
                    <h3 style="text-align: justify; margin-left: 100px;">Status: <strong style="color: #28a745;">' . htmlspecialchars($status_driver) . '</strong></h3>
                    <h3 style="text-align: justify; margin-left: 100px;">Shuttle: <span style="color: #ff7f50;">' . htmlspecialchars($vehicle_type) . '</span></h3>
                </div>';
        }
    } else {
        echo "<center><p>No drivers are currently online. :'(</p></center>";
    }
}

?>




<!-- <div class="status-driver">
    <div class="image_ni_driver">
        <h3>Picture ni Kuya Driver:</h3>
        <img src="<//?php echo htmlspecialchars($driver_picture_url); ?>" alt="Driver Picture">
    </div>

    <h1>Driver: <span><//?php echo htmlspecialchars($driver_name); ?></span></h1>
    <h3>Status: <strong style="color: #28a745;"><//?php echo htmlspecialchars($driver_status); ?></strong></h3>
    <h3>Shuttle: <span style="color: #ff7f50;"></?php echo htmlspecialchars($vehicle_type); ?></span></h3>

</div> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
