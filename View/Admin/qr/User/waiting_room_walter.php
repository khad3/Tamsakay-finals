<?php
session_start();

// Database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

// Check if the user is logged in or has a valid session
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']); // Get the user_id from the session

    // Fetch the user's location from the passenger_logs_hed_tbl
    $location_query = "SELECT location FROM passenger_logs_hed_tbl WHERE user_id = ?";
    $stmt = $db->prepare($location_query);

    $user_location = null;
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($location);
        if ($stmt->fetch()) {
            $user_location = strtolower(trim($location)); // Normalize to lowercase for comparison
        }
        $stmt->close();
    }

    // Check if the user's location is walter
    if ($user_location === 'waltermart') {
        // Add the current user to the waiting_room table if not already added
        $add_user_sql = "INSERT INTO passenger_logs_hed_tbl (user_id) VALUES (?) 
                         ON DUPLICATE KEY UPDATE user_id = user_id"; // Prevent duplication
        $stmt = $db->prepare($add_user_sql);

        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error adding user to waiting room: " . $db->error);
        }

        // Query to fetch all unique active users and their profile pictures
        $fetch_users_sql = "
        SELECT 
            COALESCE(profile_pic.pic_url, '/Tamsakay/View/User/settings/pfp/tamtam.png') AS pic_url
        FROM passenger_logs_hed_tbl
        LEFT JOIN profile_pic ON passenger_logs_hed_tbl.user_id = profile_pic.user_id
        WHERE passenger_logs_hed_tbl.location = 'WALTERMART'";
    $result = $db->query($fetch_users_sql);

        $profile_pictures = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $profile_pictures[] = $row['pic_url'];
            }
        }

        $user_count = count($profile_pictures);
    } else {
        // If the user's location is not HED, redirect to a different page or show an error
        echo "Access denied. Your location is not WALTERMART.";
        exit();
    }

    // Close the database connection after all operations are complete
    $db->close();
} else {
    // If no session exists, redirect to the login page
    header("Location: /Tamsakay/View/User/login_user.php");
    exit();
}

// Handle leaving the waiting room
if (isset($_POST['leave_waiting_room'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php'; // Reconnect to DB

    $delete_user_sql = "DELETE FROM passenger_logs_hed_tbl WHERE user_id = ?";
    $stmt = $db->prepare($delete_user_sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Optionally, you can redirect or show a message after leaving
        header("Location: /Tamsakay/View/User/dashboard_user.php"); // Redirect after leaving
        exit();
    } else {
        die("Error leaving waiting room: " . $db->error);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Room</title>
    <style>
        /* Reset default margins and paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            width: 100%;
            font-family: 'Arial', sans-serif;
            overflow: hidden; /* Prevent scrolling */
        }

        /* Background with full coverage */
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://waltermart.com.ph/wp-content/uploads/2024/10/silang-facade2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: rgba(209, 169, 79, 0.8); /* Gold color for text */
            text-align: center;
        }

        header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: rgba(209, 169, 79, 0.8); /* Gold color */
            text-shadow: 1px 1px 2px black;
        }

        /* Solar system container */
        .solar-system {
            position: relative;
            width: 900px;
            height: 900px;
            margin: auto;
            border-radius: 00%;
            overflow: clip;
        }

        /* Center circle with user count */
        .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(209, 169, 79, 0.8); /* Gold with transparency */
            color: white;
            width: 80px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 40px 70px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }

        /* Orbiting profile pictures */
        .orbiting {
            position: absolute;
            width: 1000%;
            height: 1000%;
            animation: spin 10s linear infinite;
        }

        .orbiting img {
            position: absolute;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #FFD700; /* Gold border */
            object-fit: cover;
            transform: rotate(180deg); /* Keeps profile pictures upright */
        }


        /* Buttons section */
        .button-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .button {
            width: 80%;
            max-width: 300px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: rgba(209, 169, 79, 0.8); /* Gold background */
            border: none;
            border-radius: 5px;
            text-align: center;
            text-shadow: 1px 1px 2px black;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4);
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .button:hover {
            background-color: #E5BF55; /* Slightly darker gold */
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.6);
            transform: scale(1.05);
        }

        footer {
            margin-top: 20px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.8);
            color: #FFD700;
            padding: 10px;
            width: 100%;
            font-size: 14px;
            text-shadow: 1px 1px 2px black;
        }

        .button_leave {
                font-size: 14px;
                padding: 8px 16px;
                background-color: red;
            }

            .button_games {
                font-size: 14px;
                padding: 8px 16px;
                background-color: green;
                color: aliceblue;
            }

        /* iPhone 11-specific viewport responsiveness */
        @media (max-width: 390px) and (max-height: 510px) {
            .solar-system {
                width: 390px;
                height: 510px;
            }

            .center {
                width: 60px;
                height: 60px;
                font-size: 20px;
            }

            .orbiting img {
                width: 40px;
                height: 40px;
            }

            .button {
                font-size: 14px;
            }

            .button_leave {
                font-size: 14px;
                padding: 8px 16px;
                background-color: red;
                color: aliceblue;
            }

            .button_games {
                font-size: 14px;
                padding: 8px 16px;
                background-color: green;
                color: aliceblue;
            }
        }

        /* General responsiveness for other devices */
        @media (max-width: 768px) {
            .solar-system {
                width: 390px;
                height: 510px;
            }

            .center {
                width: 50px;
                height: 50px;
                font-size: 18px;
            }

            .orbiting img {
                width: 35px;
                height: 35px;
            }

            .button {
                font-size: 12px;
                padding: 8px 16px;
            }
            .button_leave {
                font-size: 14px;
                padding: 8px 16px;
                background-color: red;
                color: aliceblue;
            }

            .button_games {
                font-size: 14px;
                padding: 8px 16px;
                background-color: green;
                color: aliceblue;
            }
        }

        @media (max-width: 480px) {
            .button {
                width: 90%;
            }

            .button_leave {
                font-size: 14px;
                padding: 8px 16px;
                background-color: red;
                color: aliceblue;
            }

            .button_games {
                font-size: 14px;
                padding: 8px 16px;
                background-color: green;
                color: aliceblue;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to the Waiting Room Waltermart</h1>
</header>

<div class="solar-system">
    <div class="center" id="user-count"><?php echo $user_count; ?></div>
    <div class="orbiting" id="orbit-container">
        <!-- Profile pictures will be added dynamically here -->
    </div>
</div>

<script>
    // JavaScript to dynamically position profile pictures
    const profilePictures = Array.from(new Set(<?php echo json_encode($profile_pictures); ?>)); // Ensure uniqueness
    const orbitContainer = document.getElementById('orbit-container');

    const positions = [
        { left: '50px', top: '50px' },
        { left: '200px', top: '30px' },
        { left: '350px', top: '50px' },
        { left: '30px', top: '200px' },
        { left: '200px', top: '200px' },
        { left: '350px', top: '200px' },
        { left: '50px', top: '350px' },
        { left: '200px', top: '350px' },
        { left: '350px', top: '350px' }
    ];

    profilePictures.forEach((pic, index) => {
        const img = document.createElement('img');
        img.src = pic;
        img.style.left = positions[index % positions.length].left; // Use modulo to cycle through positions
        img.style.top = positions[index % positions.length].top;
        img.style.animation = 'float 3s ease-in-out infinite'; // Add floating animation

        orbitContainer.appendChild(img);
    });
</script>

<style>
    /* Add this to your existing CSS */
    @keyframes float {
        0% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0); }
    }
</style>

<div class="button-container">
    <button class="button_games" onclick="window.location.href='/Tamsakay/View/User/gamehed.php'">Play Mini Game</button>
    <form method="post" style="display: inline;">
        <button type="submit" name="leave_waiting_room" class="button_leave">Leave Waiting Room</button>
    </form>
    <button class="button" onclick="window.location.href='/Tamsakay/View/User/dashboard_user.php'">Return to Dashboard</button>
</div>

<footer>
    &copy; 2024 Tamsakay. All rights reserved.
</footer>

</body>
</html>
