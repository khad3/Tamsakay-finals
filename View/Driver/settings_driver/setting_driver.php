<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Default profile picture URL
$default_pic_url = "/Tamsakay/View/Driver/settings_driver/pfp/driver_tamtam.png";
$profile_pic_url = $default_pic_url;
$message = '';

// Ensure the user is logged in
if (isset($_SESSION['driver_id'])) {
    $driver_id = $_SESSION['driver_id'];

    // Retrieve the driver's current profile picture
    try {
        $stmt = $db->prepare("SELECT picture_url FROM driver_profile_pic WHERE driver_id = ? LIMIT 1");
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $stmt->bind_result($pic_url);
        if ($stmt->fetch() && !empty($pic_url)) {
            $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $pic_url;
            if (file_exists($absolute_path)) {
                $profile_pic_url = htmlspecialchars($pic_url);
            } else {
                error_log("Profile picture file not found: " . $absolute_path);
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        echo "Error retrieving profile picture: " . $e->getMessage();
    }

    // Handle profile picture upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES["profile_pic"])) {
        $target_dir = "/Tamsakay/View/Driver/settings_driver/pfp/";
        $target_file = $target_dir . uniqid() . '-' . basename($_FILES["profile_pic"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($check = getimagesize($_FILES["profile_pic"]["tmp_name"])) {
            $uploadOk = 1;
        } else {
            echo "<p>File is not an image.</p>";
            $uploadOk = 0;
        }

        if ($_FILES["profile_pic"]["size"] > 2000000) {
            echo "<p>File is too large.</p>";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "<p>Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
            $uploadOk = 0;
        }

        if ($uploadOk) {
            try {
                $stmt = $db->prepare("SELECT picture_url FROM driver_profile_pic WHERE driver_id = ? LIMIT 1");
                $stmt->bind_param("i", $driver_id);
                $stmt->execute();
                $stmt->bind_result($current_pic_url);
                if ($stmt->fetch() && !empty($current_pic_url)) {
                    $current_pic_absolute_path = $_SERVER['DOCUMENT_ROOT'] . $current_pic_url;
                    if (file_exists($current_pic_absolute_path)) {
                        unlink($current_pic_absolute_path);
                    }
                }
                $stmt->close();

                $stmt = $db->prepare("DELETE FROM driver_profile_pic WHERE driver_id = ?");
                $stmt->bind_param("i", $driver_id);
                $stmt->execute();
                $stmt->close();

                $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $target_file;
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $absolute_path)) {
                    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolute_path);
                    $stmt = $db->prepare("INSERT INTO driver_profile_pic (driver_id, picture_url) VALUES (?, ?)");
                    $stmt->bind_param("is", $driver_id, $relative_path);
                    if ($stmt->execute()) {
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    } else {
                        echo "<p>Error saving profile picture to database.</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p>Error uploading your file.</p>";
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    // Handle Change Password
    if (isset($_POST['change_password'])) {
        $new_password = $_POST['new_password'];
        try {
            $stmt = $db->prepare("UPDATE for_driver_registration_tbl SET driver_password = ? WHERE driver_id = ?");
            $stmt->bind_param("si", $new_password, $driver_id);
            if ($stmt->execute()) {
                $message .= "<div class='alert success'>Password updated successfully.</div>";
            } else {
                echo "<p>Error updating password.</p>";
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Handle Change Username
    if (isset($_POST['change_username'])) {
        try {
            $new_username = $_POST['new_username'];
            $stmt = $db->prepare("UPDATE for_driver_registration_tbl SET driver_first_name = ? WHERE driver_id = ?");
            $stmt->bind_param("si", $new_username, $driver_id);
            if ($stmt->execute()) {
                $message .= "<div class='alert success'>Username updated successfully.</div>";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "<p>Error updating username.</p>";
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Handle Delete Profile Picture
    if (isset($_POST['delete_profile_picture'])) {
        try {
            $stmt = $db->prepare("DELETE FROM driver_profile_pic WHERE driver_id = ?");
            $stmt->bind_param("i", $driver_id);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "<p>Error deleting profile picture.</p>";
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "Error deleting profile picture: " . $e->getMessage();
        }
    }
} else {
    echo "Driver is not logged in.";
    exit();
}

//retrieve driver first name and last name

$retrieve_firstname_lastname = "SELECT driver_first_name , driver_last_name  FROM for_driver_registration_tbl WHERE driver_id = ?";
if ($stmt = $db->prepare($retrieve_firstname_lastname)) {
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();  
    $row = $result->fetch_assoc();

    if ($row) {
        $driver_first_name = $row['driver_first_name'];
        $driver_last_name = $row['driver_last_name'];
    } else {
        echo '<div class="alert alert-danger" role="alert">Driver not found.</div>';
        exit();
    }
}

$db->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Settings</title>
   
   <!-- Link to external CSS -->
   <link rel="stylesheet" href="settingsDriver_css.css">
   <script src="darkmode.js"></script>

   <style>
    /* Global Styles */
body {
  font-family: 'Segoe UI', sans-serif;
  background-color:rgb(105, 233, 105);
  margin: 0;
  padding: 0;
  color: #2c3e50;
}

.container {
  max-width: 800px;
  margin: 40px auto;
  padding: 30px;
  background-color:rgb(232, 250, 152);
  border-radius: 16px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

/* Heading */
h2 {
  text-align: center;
  font-size: 32px;
  margin-bottom: 20px;
  color: #27ae60;
  font-weight: bold;
}

h3 {
  color: #2ecc71;
  margin-top: 30px;
}

/* Profile Section */
.profile-section {
  text-align: center;
  margin-bottom: 30px;
}

.profile-picture {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #2ecc71;
  margin-bottom: 15px;
}

.upload-form input[type="file"] {
  margin-top: 10px;
  margin-bottom: 10px;
}

.upload-form button,
.profile-section button,
.change-username-section button,
.back-button button,
.dark-mode-toggle button {
  background: linear-gradient(to right, #27ae60, #2ecc71);
  color: white;
  border: none;
  padding: 10px 22px;
  margin: 5px;
  border-radius: 30px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
}

.upload-form button:hover,
.profile-section button:hover,
.change-username-section button:hover,
.back-button button:hover,
.dark-mode-toggle button:hover {
  transform: scale(1.05);
  background: linear-gradient(to right, #2ecc71, #27ae60);
}

/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
  background-color: #fff;
  margin: 10% auto;
  padding: 30px;
  border: 1px solid #ddd;
  width: 90%;
  max-width: 400px;
  border-radius: 12px;
  text-align: center;
}

.modal-content h3 {
  color: #27ae60;
}

.modal-content input[type="password"],
.change-username-section input[type="text"] {
  width: 90%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
}

.modal-content button {
  width: 100%;
}

.close {
  color: #888;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover {
  color: #e74c3c;
}

/* Change Username Section */
.change-username-section {
  margin-top: 30px;
}

.change-username-section input[type="text"] {
  width: 80%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
}

/* Back Button & Logout */
.back-button,
.dark-mode-toggle {
  text-align: center;
  margin-top: 40px;
}

/* Responsive */
@media (max-width: 600px) {
  .container {
    padding: 20px;
  }

  .profile-picture {
    width: 100px;
    height: 100px;
  }

  .modal-content {
    margin: 20% auto;
  }

  #backButton {
    margin-top: 20px;
    color:rgb(170, 209, 187);
    
  }
}

   </style>
</head>
<body>
   <div class="container">
       <h2>Settings</h2>

       <!-- Display any messages -->
       <?= isset($message) ? htmlspecialchars($message) : ''; ?>

       <!-- Profile Picture Section -->
       <div class="profile-section">
           <img src="<?= htmlspecialchars($profile_pic_url); ?>" class="profile-picture" onerror="this.onerror=null; this.src='/Tamsakay/View/Driver/settings_driver/pfp/tamtam.jpg';" alt="Profile Picture">

        <!-- first name -->

           <h3 style="color: #2ecc71;">Name : <?= htmlspecialchars($driver_first_name);?> &#x1F60E</h3>
           
           <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
               <input type="file" name="profile_pic" accept="image/*" required>
               <button type="submit">Upload</button>
           </form>

           <!-- Button to Open Change Password Modal -->
           <button id="openPasswordModal">Change Password</button>

           <!-- Button to Delete Profile Picture -->
           <form action="" method="POST" style="display:inline;">
               <button type="submit" name="delete_profile_picture">Delete Profile Picture</button>
           </form>
       </div>

       <!-- Change Password Modal -->
       <div id="passwordModal" class="modal">
           <div class="modal-content">
               <span class="close">&times;</span>
               <h3>Change Password</h3>
               <form action="" method="POST">
                   <input type="password" name="new_password" placeholder="New Password" required>
                   <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                   <button type="submit" name="change_password">Change Password</button>
               </form>
           </div>
       </div>

       <!-- Change Username Section -->
       <div class="change-username-section">
           <h3>Change Name</h3>
           <form action="" method="POST">
               <input type="text" name="new_username" placeholder="New Name" required>
               <button type="submit" name="change_username">Change name</button>
           </form>
       </div>

       <!-- Dark Mode Toggle -->


       <!-- Back Button -->
       <div class="back-button">
                 <h3>Directory</h3>
           <a href="/Tamsakay/View/Driver/driver_dashboard.php"><button id = "backButton">Back to Home</button></a>
       </div>
    <div class="dark-mode-toggle">
     
           <a href="/Tamsakay/View/Driver/logout.php">  <button id="darkModeToggle">Logout</button>
       </div>
   </div>

   <script>
       // Modal Script
       const modal = document.getElementById('passwordModal');
       const openModalButton = document.getElementById('openPasswordModal');
       const closeModalButton = document.getElementsByClassName('close')[0];

       openModalButton.onclick = function() {
           modal.style.display = 'block';
       }

       closeModalButton.onclick = function() {
           modal.style.display = 'none';
       }

       window.onclick = function(event) {
           if (event.target == modal) {
               modal.style.display = 'none';
           }
       }
   </script>

</body>
</html>