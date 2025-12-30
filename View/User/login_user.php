<?php
session_start(); // Start the session

include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay-finals/db.php'; // Database connection

$message = ""; // Initialize a variable for feedback

if (isset($_POST['submit'])) {
    // Get username and password from the form
    $name = $_POST['user_email'];
    $pass = $_POST['password'];

    // Verify user credentials
    $stmt = $db->prepare("SELECT user_id, password FROM for_user_registration_tbl WHERE email = ?");
    $stmt->bind_param("s", $name); // Bind username parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($pass, $user['password'])) { // Use password_verify for hashed passwords
            // Set session variables for logged-in user
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id']; // Store user ID in session

            echo '<script language="javascript">
                    alert("Login successfully!");
                    window.location="dashboard_user.php";
                  </script>';

            // Redirect to the QR code generation page
            exit();
        } else {
            $message = "Invalid password."; // Incorrect password
        }
    } else {
        $message = "Invalid email."; // Username not found
    }

    $stmt->close();
}

// Check if the user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard_user.php");
    exit(); // Redirect to the dashboard if already logged in
}

if (!empty($message)) {
    echo '<script>alert("' . $message . '");</script>';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title>FEU Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #d1e7dd);
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #006400;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content:center;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2); 
            padding: 40px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.3);
        }

        .form-label {
            font-weight: bold;
            color: #006400;
        }

        .button {
            --color: #00A97F;
            padding: 0.8em 1.7em;
            background-color: transparent;
            border-radius: 0.3em;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: color 0.5s ease, border-color 0.5s ease, transform 0.3s ease;
            font-weight: 400;
            font-size: 17px;
            border: 1px solid var(--color);
            font-family: inherit;
            text-transform: uppercase;
            color: var(--color);
            z-index: 1;
            width: 100%;
        }

        .button::before,
        .button::after {
            content: '';
            display: block;
            position: absolute;
            width: 300%;
            height: 300%;
            border-radius: 50%;
            background-color: var(--color);
            transition: all 1s ease;
            z-index: -1;
        }

        .button::before {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
        }

        .button::after {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
        }

        .button:hover::before,
        .button:hover::after {
            transform: translate(-50%, -50%) scale(1);
        }

        .button:hover {
            color: #ffffff;
            border-color: var(--color);
            transform: scale(1.05);
        }

        .button:active {
            filter: brightness(0.8);
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #006400;
            font-size: 0.9rem;
        }
        .logo {
            width: 50px;
            margin-right: 15px;
        }

        .footer a {
            color: #006400;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .form-outline input:focus {
            border-color: #FFD700;
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
            transform: translateY(-3px);
        }

        .form-outline input {
            padding: 10px;
            font-size: 16px;
            border-radius: 0.3em;
            border: 2px solid #d1e7dd;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-outline input:focus {
            border-color: #00A97F;
            box-shadow: 0px 4px 10px rgba(0, 169, 127, 0.3);
        }

        a {
            color: #006400;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }

        a:hover {
            color: #003d00;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="header">
<img src="/Tamsakay/Tamsakay Logo.png" alt="Tamsakay Logo" class="logo">
<h2>Tamsakay Shuttle Service</h2>
</div>

<div class="main-content">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card p-4">
          <div class="text-center mb-4">
            <h3 class="text-uppercase" style="color: #006400;">Login</h3>
          </div>
          
          <!-- Display error message -->
          <?php if (!empty($message)): ?>
            <div class="alert alert-danger" role="alert">
              <?php echo $message; ?>
            </div>
          <?php endif; ?>

          <form method="POST">
            <div class="form-outline mb-4">
              <input type="email" id="username" name="user_email" class="form-control" placeholder="Enter your username" required />
              <label class="form-label" for="username">Email</label>
            </div>

            <div class="form-outline mb-4">
              <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required />
              <label class="form-label" for="password">Password</label>
            </div>

            <button type="submit" name="submit" class="button">Login</button>
          </form>

          <hr class="my-4">
          <div class="text-center">
            <a href="registration_user.php">Don't have an account?</a><br>
            <a href="forgot_password_user.php">Forgot password?</a>
          </div>
        </div>
        <div class="footer">
          &copy; 2024 <a href="landingpage_user.php">Tamsakay</a>. All rights reserved.
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
