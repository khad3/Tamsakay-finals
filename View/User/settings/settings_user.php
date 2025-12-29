<?php 

session_start(); 

include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php'; 

 

// Enable error reporting for debugging 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

 

// Default profile picture URL 

$default_pic_url = "/Tamsakay/View/User/settings/pfp/tamtam.png"; 

$profile_pic_url = $default_pic_url; 

$message = ''; // Variable to hold success/error messages 

 

// Ensure the user is logged in 

if (isset($_SESSION['user_id'])) { 

    $user_id = $_SESSION['user_id']; 

 

    // Retrieve user details including profile picture, first name, last name, username, and passenger type 

    try { 

        $stmt = $db->prepare(" 

            SELECT ur.first_name, ur.last_name, ur.user_name, ur.passenger_type 

            FROM for_user_registration_tbl ur 

            WHERE ur.user_id = ? LIMIT 1"); 

         

        $stmt->bind_param("i", $user_id); 

        $stmt->execute(); 

        $stmt->bind_result($first_name, $last_name, $username, $passenger_type); 

     

        if ($stmt->fetch()) { 

            // You now have the user details, excluding the profile picture 

            // Process the user data as needed 

        

        } 

         

        $stmt->close(); 

    } catch (Exception $e) { 

        echo "Error retrieving user details: " . $e->getMessage(); 

    } 

     

    // Retrieve the user's current profile picture 

    try { 

        $stmt = $db->prepare("SELECT pic_url FROM profile_pic WHERE user_id = ? LIMIT 1"); 

        $stmt->bind_param("i", $user_id); 

        $stmt->execute(); 

        $stmt->bind_result($pic_url); 

 

        if ($stmt->fetch() && !empty($pic_url)) { 

            $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $pic_url; 

 

            // Check if the profile picture file exists 

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

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES["profile_pic"])) { 

        // Prepare to upload the new image 

        $target_dir = "/Tamsakay/View/User/settings/pfp/"; 

        // Generate a unique filename to prevent overwriting 

        $target_file = $target_dir . uniqid() . '-' . basename($_FILES["profile_pic"]["name"]); 

        $uploadOk = 1; 

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); 

     

        // Check if the file is an image 

        if ($check = getimagesize($_FILES["profile_pic"]["tmp_name"])) { 

            $uploadOk = 1; 

        } else { 

            echo "<p>File is not an image.</p>"; 

            $uploadOk = 0; 

        } 

     

        // Check file size (limit to 2MB) 

        if ($_FILES["profile_pic"]["size"] > 2000000) { 

            echo "<p>File is too large.</p>"; 

            $uploadOk = 0; 

        } 

     

        // Allow specific file formats 

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) { 

            echo "<p>Only JPG, JPEG, PNG, and GIF files are allowed.</p>"; 

            $uploadOk = 0; 

        } 

     

        // Attempt to upload the file 

        if ($uploadOk) { 

            try { 

                // Fetch the current profile picture URL from the database 

                $stmt = $db->prepare("SELECT pic_url FROM profile_pic WHERE user_id = ? LIMIT 1"); 

                $stmt->bind_param("i", $user_id); 

                $stmt->execute(); 

                $stmt->bind_result($current_pic_url); 

                if ($stmt->fetch() && !empty($current_pic_url)) { 

                    $current_pic_absolute_path = $_SERVER['DOCUMENT_ROOT'] . $current_pic_url; 

     

                    // Delete the current profile picture file from the server 

                    if (file_exists($current_pic_absolute_path)) { 

                        unlink($current_pic_absolute_path); // Delete the file 

                    } 

                } 

                $stmt->close(); 

     

                // Delete the existing database record first 

                $stmt = $db->prepare("DELETE FROM profile_pic WHERE user_id = ?"); 

                $stmt->bind_param("i", $user_id); 

                $stmt->execute(); 

                $stmt->close(); 

     

                // Move the new uploaded file to the target directory 

                $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $target_file; 

                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $absolute_path)) { 

                    // Save the new file path in the database (store relative path) 

                    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $absolute_path); 

     

                    // Insert the new profile picture into the database 

                    $stmt = $db->prepare("INSERT INTO profile_pic (user_id, pic_url) VALUES (?, ?)"); 

                    $stmt->bind_param("is", $user_id, $relative_path); 

                    if ($stmt->execute()) { 

                        header("Location: " . $_SERVER['PHP_SELF']); // Refresh the page to show the new picture 

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

     

     

 

    // Handle Change Password without hashing 

    if (isset($_POST['change_password'])) { 

        $new_password = $_POST['new_password']; 

         

        try { 

            // Update password directly without hashing as per request 

            $stmt = $db->prepare("UPDATE for_user_registration_tbl SET password = ? WHERE user_id = ?"); 

            $stmt->bind_param("si", $new_password, $user_id);  

            if ($stmt->execute()) { 

                // Set success message for UI display 

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

            // Update username in database 

            $stmt = $db->prepare("UPDATE for_user_registration_tbl SET user_name = ? WHERE user_id = ?"); 

            $stmt->bind_param("si", $new_username, $user_id); 

            if ($stmt->execute()) { 

                // Set success message for UI display 

                $message .= "<div class='alert success'>Username updated successfully.</div>"; 

                header("Location: " . $_SERVER['PHP_SELF']); // Refresh to show updated username 

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

            // Delete old profile picture from database 

            $stmt = $db->prepare("DELETE FROM profile_pic WHERE user_id = ?"); 

            $stmt->bind_param("i", $user_id); 

             

           if ($stmt->execute()) {  

               header("Location: " . $_SERVER['PHP_SELF']);  

               exit();  

           } else {  

               echo "<p>Error deleting profile picture.</p>";  

           }  

           $stmt->close();  

       } catch (Exception) {  

           echo "Error deleting profile picture: " . $e->getMessage();  

       }  

   }  

} else {  

   echo "User is not logged in.";  

   exit();  

} 

    // Retrieve user details including profile picture, first name, last name, username, and passenger type 

    

// Close the database connection  

$db->close();  

?> 

<!DOCTYPE html> 

<html lang="en"> 

<head> 

  <title>Settings</title> 

  <meta charset="utf-8"> 

  <meta name="viewport" content="width=device-width, initial-scale=1"> 

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> 

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> 

    <!-- Link to external CSS --> 

    <link rel="stylesheet" href="settings_css.css"> 

    <script src="darkmode.js"></script> 

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }
 
        header {
            background-color: #05683B;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
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
 
        /* Centering the Forgot Password box */
        .container {
            background-color: white;
            margin: 100px auto; /* Center the container with some space from the top */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            padding: 20px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; /* Hover animation */
        }
 
        /* Hover effect */
        .container:hover {
            transform: scale(1.02); /* Slightly enlarge */
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2); /* Stronger shadow */
        }
 
        h2 {
            font-size: 22px;
            margin-bottom: 15px;
            color: black;
        }
 
        p {
            font-size: 14px;
            color: #333;
            margin-bottom: 20px;
        }
 
        form {
            margin-top: 20px;
        }
 
        label {
            font-weight: bold;
            display: block;
            text-align: left;
            margin: 10px 0 5px;
        }
 
        input[type="email"],
        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px); /* Adjusted for padding */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
 
        button {
            display: block;
            width: calc(100% - 20px); /* Adjusted for padding */
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #05683B;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out; /* Smooth animation */
        }
 
        button:hover {
            background-color: #FFBF00;
            transform: scale(1.02); /* Slightly enlarge */
        }
 
        .divider {
            margin: 30px 0;
            border-top: none;
            border-top-width: thin;
        }
 
        /* Footer Styling */
        footer {
            background-color: #FFFFFF;
            color: white;
            text-align: center;
            padding: 15px;
            position: absolute;
            bottom: 0;
            width: 100%;
            font-size: 14px;
        }
 
        footer a {
            color: white;
            text-decoration: none;
        }
 
        footer a:hover {
            text-decoration: underline;
        }
 
        /* Media Queries for Responsiveness */
        @media (max-width: 600px) {
            header h1 {
                font-size: 20px; /* Smaller heading on small screens */
            }
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            h2 {
                font-size: 18px;
            }
            p, label, button {
                font-size: 14px;
            }
        }
    </style>

</head> 

<body> 

<!-- <header> 

        <h1>Tamsakay Shuttle Service</h1> 

    </header>  -->

   <div class="container"> 

       <h2>Settings</h2> 

 

       <!-- Display any messages --> 

       <?= isset($message) ? htmlspecialchars($message) : ''; ?> 

 

       <div class="card-body"> 

           <div class="row gx-md-8 gx-xl-12 gy-10"> 

               <div class="col-lg-6">    

                   <div class="profile-section"> 

                       <img src="<?= htmlspecialchars($profile_pic_url); ?>" class="profile-picture"  

                           onerror="this.onerror=null; this.src='/Tamsakay/View/User/settings/pfp/tamtam.jpg';"  

                           alt="Profile Picture"> 

                       <!-- Display user information --> 

<div class="user-info"> 
    
     <p>@<?= htmlspecialchars($username) ?></p> 
     <h2><?= htmlspecialchars($first_name) ?> <?= htmlspecialchars($last_name) ?></h2>        
     <p>Passenger Type: <strong><?= htmlspecialchars($passenger_type) ?></strong></p> 
      

  

</div> 

 

 

                       <!-- Profile Picture Buttons --> 

                       <form action="" method="POST" enctype="multipart/form-data" class="upload-form"> 

                           <input type="file" name="profile_pic" accept="image/*" required> 

                           <button type="submit" style="background-color: #2973B2;">Upload</button> 

                            

                       </form> 

                       <form action="" method="POST" enctype="multipart/form-data" class="upload-form"> 

                       <button type="submit" name="delete_profile_picture" style = "background-color:rgb(177, 46, 37);">Delete Profile Picture</button> 

                       </form> 

                   </div> 

               </div> 

 

               <!-- /column --> 

               <div class="col-lg-6"> 

                   <h3 class="display-5 mb-7">Account Management</h3> 

                   <!-- Button to Open Change Password Modal --> 

                   <button id="openPasswordModal">Change Password</button> 

 

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

                   <!-- Button to open the modal --> 

                   <button id="openModalBtn">Change Username</button> 

 

                   <!-- The Modal --> 

                   <div id="changeUsernameModal" class="modal"> 

                       <div class="modal-content"> 

                           <span class="close" id="closeModalBtn">&times;</span> 

                           <h3>Change Username</h3> 

                           <form action="" method="POST"> 

                               <input type="text" name="new_username" placeholder="New Username" required> 

                               <button type="submit" name="change_username">Change Username</button> 

                           </form> 

                       </div> 

                   </div> 

 

                   <!-- Back and Logout Buttons --> 

                   <div class="dark-mode-toggle"> 

                       <h3>Directory</h3> 

                   </div> 

                   <div class="back-button"> 

                       <a href="/Tamsakay/View/User/dashboard_user.php"><button style="background-color: coral; color:#ddd;">Back to Home</button></a> 

                       <a href="/Tamsakay/View/User/logout.php"><button>Logout</button></a> 

                   </div> 

               </div> 

               <!-- /column --> 

           </div> 

           <!-- /.row --> 

       </div> 

       <!--/.card-body --> 

   </div> 

   <!-- JavaScript for Modals --> 

   <script> 

    // Get the modal elements 

    var changeUsernameModal = document.getElementById("changeUsernameModal"); 

    var passwordModal = document.getElementById("passwordModal"); 

 

    // Get the buttons that open the modals 

    var openUsernameBtn = document.getElementById("openModalBtn"); 

    var openPasswordBtn = document.getElementById("openPasswordModal"); 

 

    // Get the close elements 

    var closeUsernameSpan = document.getElementById("closeModalBtn"); 

    var closePasswordSpan = document.getElementsByClassName("close")[0]; 

 

    // When the user clicks on the button, open the modal  

    openUsernameBtn.onclick = function() { 

        changeUsernameModal.style.display = "block"; 

    } 

 

    openPasswordBtn.onclick = function() { 

        passwordModal.style.display = "block"; 

    } 

 

    // When the user clicks on the close (x), close the modal 

    closeUsernameSpan.onclick = function() { 

        changeUsernameModal.style.display = "none"; 

    } 

 

    closePasswordSpan.onclick = function() { 

        passwordModal.style.display = "none"; 

    } 

 

    // When the user clicks anywhere outside of the modal, close it 

    window.onclick = function(event) { 

        if (event.target == changeUsernameModal) { 

            changeUsernameModal.style.display = "none"; 

        } else if (event.target == passwordModal) { 

            passwordModal.style.display = "none"; 

        } 

    } 

   </script> 

 

</body> 

</html> 