<?php 
// Set session lifetime to 1 hour (3600 seconds)
ini_set('session.gc_maxlifetime', 3600); // Lifetime of the session in seconds
session_set_cookie_params(3600); // Lifetime of the session cookie in seconds

// Start the session
session_start(); 

// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

// Check if the login form is submitted
if (isset($_POST['submit'])) {
    // Retrieve user input
    $email = $_POST['user_email'];
    $password = $_POST['password'];

    // Query to verify the driver credentials
    $query = "SELECT driver_id, driver_password FROM for_driver_registration_tbl WHERE email = ?";
    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param("s", $email);  // Bind the email parameter
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                // Fetch the driver password and ID
                $stmt->bind_result($driver_id, $hashed_password);
                $stmt->fetch();

                // Verify the password using password_verify
                if (password_verify($password, $hashed_password)) {
                    // Password is correct, store driver ID and login status in session
                    $_SESSION['driver_logged_in'] = true;  // Set login status
                    $_SESSION['driver_id'] = $driver_id;   // Set driver ID in session
                    
                    // Provide a success message and redirect to the dashboard
                    echo '<script language="javascript">
                        alert("Login successfully!");
                        window.location="driver_dashboard.php";
                    </script>';
                    exit();
                } else {
                    echo '<div class="alert alert-danger" role="alert">Invalid login credentials!</div>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">No user found with that email!</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Database error occurred.</div>';
        }
    }
}

// Check if the user is already logged in
if (isset($_SESSION['driver_logged_in']) && $_SESSION['driver_logged_in'] === true) {
    // If logged in, redirect to the dashboard
    header("Location: driver_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap">
    <link rel="stylesheet" href="css/login_admin.css" />
    <title>Login</title>
 
    <style>
        /* FEU theme colors */
        :root {
            --feu-green: #006747;
            --feu-gold: #F1C40F;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: var(--feu-green);
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        footer {
            background-color: var(--feu-green);
            color: white;
            padding: 10px 0;
            text-align: center;
            margin-top: auto;
        }

        .footer-links a {
            color: var(--feu-gold);
            margin: 0 10px;
            text-decoration: none;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Card container styling */
        .card {
            border-radius: 1rem;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .card-body {
            padding: 3rem;
        }

        .form-outline input {
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
        }

        .form-outline input:focus {
            outline: none;
            border: 2px solid var(--feu-green);
        }

        h5 {
            color: var(--feu-green);
        }

        .forgot-password-link,
        .register-link {
            color: var(--feu-green);
            text-decoration: none;
        }

        .forgot-password-link:hover,
        .register-link:hover {
            text-decoration: underline;
        }

        .img-fluid {
            border-radius: 1rem 0 0 1rem;
        }

        /* Gooey button style integration */
        .c-button {
            color: var(--feu-green);
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            padding: 0.9em 1.6em;
            cursor: pointer;
            display: inline-block;
            vertical-align: middle;
            position: relative;
            z-index: 1;
            border-radius: 1rem;
            border: 4px solid var(--feu-green);
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 700ms ease;
            width: 100%;
            text-align: center;
        }

        .c-button:hover {
            color: white;
            background-color: var(--feu-green);
        }

        .c-button .c-button__blobs {
            height: 100%;
            filter: url(#goo);
            overflow: hidden;
            position: absolute;
            top: 0;
            left: 0;
            bottom: -3px;
            right: -1px;
            z-index: -1;
        }

        .c-button .c-button__blobs div {
            background-color: var(--feu-green);
            width: 34%;
            height: 100%;
            border-radius: 100%;
            position: absolute;
            transform: scale(1.4) translateY(125%) translateZ(0);
            transition: all 700ms ease;
        }

        .c-button .c-button__blobs div:nth-child(1) {
            left: -5%;
        }

        .c-button .c-button__blobs div:nth-child(2) {
            left: 30%;
            transition-delay: 60ms;
        }

        .c-button .c-button__blobs div:nth-child(3) {
            left: 66%;
            transition-delay: 25ms;
        }

        .c-button:hover .c-button__blobs div {
            transform: scale(1.4) translateY(0) translateZ(0);
        }

        .footer-text {
            font-size: 0.9rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }

            .card {
                margin-top: 30px;
            }

            .card-body {
                padding: 1.5rem;
            }

            .c-button {
                padding: 1em 0; /* Adjust button padding for small screens */
            }

            .img-fluid {
                border-radius: 1rem;
                max-width: 100%;
                height: auto;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <h1>FEU Driver Login</h1>
    </header>

    <!-- Main content -->
    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-6 col-lg-5 d-none d-md-block">
                                <img src="https://cdn-icons-png.flaticon.com/512/5283/5283021.png" alt="login form" class="img-fluid" />
                            </div>
                            <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="d-flex align-items-center mb-3 pb-1">
                                            <span class="h1 fw-bold mb-0">Driver</span>
                                        </div>

                                        <h5 class="fw-normal mb-3 pb-3">Sign into your account</h5>

                                        <div class="form-outline mb-4">
                                            <input type="email" id="form2Example17" name="user_email" class="form-control form-control-lg" />
                                            <label class="form-label" for="form2Example17">Email</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="password" id="form2Example27" name="password" class="form-control form-control-lg" />
                                            <label class="form-label" for="form2Example27">Password</label>
                                        </div>

                                        <div class="pt-1 mb-4">
                                            <button type="submit" name="submit" class="c-button">
                                                Login
                                                <span class="c-button__blobs">
                                                    <div></div>
                                                    <div></div>
                                                    <div></div>
                                                </span>
                                            </button>
                                        </div>

                                        <hr>

                                        <div class="text-center">
                                            <h5><a href="registration.php" class="register-link">Don't have an account?</a></h5>
                                            <h5><a href="forgot_password_driver.php" class="forgot-password-link">Forgot password?</a></h5>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p class="footer-text">© 2024 FEU. All rights reserved.</p>
    </footer>

    <!-- Gooey filter SVG -->
    <svg xmlns="http://www.w3.org/2000/svg" width="0" height="0">
        <defs>
            <filter id="goo">
                <feGaussianBlur in="SourceGraphic" stdDeviation="15" result="blur" />
                <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 20 -8" result="goo" />
                <feComposite in="SourceGraphic" in2="goo" operator="atop" />
            </filter>
        </defs>
    </svg>

</body>
</html>
