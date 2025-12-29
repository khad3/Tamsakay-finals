
<?php 

    
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/Controller/registration_driver.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay/db.php';

if(isset($_POST['submit'])) {

    $firstname = $_POST['firstName'];
    $lastname = $_POST['lastName'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];


    $driver = new Driver_registration();
    $driver->registration($db, $firstname, $lastname, $username, $password, $email );

}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title>Driver Registration</title>
 
    <style>
        /* FEU theme colors */
        :root {
            --feu-green: #006747;
            --feu-gold: #F1C40F;
            --gradient-1: hsla(194 100% 69% / 1);
            --gradient-2: hsla(217 100% 56% / 1);
            --radii: 0.5em;
            --btn-font-color: #fff;
            --input-border-color: #ddd;
        }
 
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
            margin-top: auto; /* This ensures footer stays at the bottom */
        }
 
        footer .footer-links a {
            color: var(--feu-gold);
            margin: 0 10px;
            text-decoration: none;
        }
 
        footer .footer-links a:hover {
            text-decoration: underline;
        }
 
        /* Form Container: Box-style with border and padding */
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 1rem;
            border: 2px solid var(--feu-green);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
 
        h3 {
            color: var(--feu-green);
            margin-bottom: 2rem;
            font-weight: bold;
        }
 
        .form-outline {
            position: relative;
            margin-bottom: 2rem;
        }
 
        /* Input Styling */
        .form-outline input {
            border-radius: 1.25rem;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--input-border-color);
            background-color: #f8f8f8;
            width: 100%;
            transition: all 0.3s ease;
        }
 
        .form-outline input:focus {
            outline: none;
            border: 2px solid var(--feu-green);
            box-shadow: 0px 0px 10px rgba(0, 103, 71, 0.2);
            background-color: #fff;
        }
 
        .form-outline input::placeholder {
            color: var(--feu-green);
        }
 
        .form-outline i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--feu-green);
            font-size: 1.5rem;
        }
 
        .form-label {
            font-size: 1rem;
            font-weight: 500;
            color: var(--feu-green);
        }
 
        /* Button Styling */
        .btn-donate {
            --clr-font-main: hsla(0 0% 20% / 100);
            --btn-bg-1: var(--feu-green); /* Using FEU Green */
            --btn-bg-2: var(--feu-gold); /* Using FEU Gold */
            --btn-bg-color: hsla(360 100% 100% / 1);
            --radii: 0.5em;
            cursor: pointer;
            padding: 1.2em 2em; /* Increased padding for a wider button */
            min-width: 180px; /* Increased minimum width */
            min-height: 50px; /* Increased height */
            font-size: var(--size, 1rem);
            font-weight: 500;
            transition: 0.8s;
            background-size: 280% auto;
            background-image: linear-gradient(
                325deg,
                var(--btn-bg-2) 0%,
                var(--btn-bg-1) 55%,
                var(--btn-bg-2) 90%
            );
            border: none;
            border-radius: var(--radii);
            color: var(--btn-bg-color);
            box-shadow:
                0px 0px 20px rgba(71, 184, 255, 0.5),
                0px 5px 5px -1px rgba(58, 125, 233, 0.25),
                inset 4px 4px 8px rgba(175, 230, 255, 0.5),
                inset -4px -4px 8px rgba(19, 95, 216, 0.35);
        }
 
        .btn-donate:hover {
            background-position: right top;
        }
 
        .btn-donate:is(:focus, :focus-visible, :active) {
            outline: none;
            box-shadow:
                0 0 0 3px var(--btn-bg-color),
                0 0 0 6px var(--btn-bg-2);
        }
 
        @media (prefers-reduced-motion: reduce) {
            .btn-donate {
                transition: linear;
            }
        }
 
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }
 
            .form-container {
                padding: 1.5rem;
                margin: 20px;
            }
 
            .btn-donate {
                width: 100%; /* Full-width button on small screens */
                padding: 1em 0; /* Adjust padding */
            }
 
            .form-row .col-md-12 {
                padding-left: 0;
                padding-right: 0;
            }
 
            footer {
                margin-top: 30px; /* Add margin to prevent footer from overlapping */
            }
        }
    </style>
</head>
 
<body>
    <!-- Header -->
    <header>
        <h1>FEU Driver Registration</h1>
    </header>
 
    <!-- Main Registration Form -->
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-12 col-lg-9 col-xl-7">
                    <div class="form-container">
                        <h3 class="mb-4">Driver Registration Form</h3>
                        <form method="POST">
 
                            <div class="form-row">
                                <div class="col-12 mb-4">
                                    <div class="form-outline">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="firstName" class="form-control form-control-lg" placeholder="First Name" required />
                                        <label class="form-label" for="firstName">First Name</label>
                                    </div>
                                </div>
                            </div>
 
                            <div class="form-row">
                                <div class="col-12 mb-4">
                                    <div class="form-outline">
                                        <i class="fas fa-user"></i>
                                        <input type="text" name="lastName" class="form-control form-control-lg" placeholder="Last Name" required />
                                        <label class="form-label" for="lastName">Last Name</label>
                                    </div>
                                </div>
                            </div>
 
                            <div class="form-row">
                                <div class="col-12 mb-4">
                                    <div class="form-outline">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Email Address" required />
                                        <label class="form-label" for="email">Email Address</label>
                                    </div>
                                </div>
                            </div>
 
                            <!-- <div class="form-row">
                                <div class="col-12 mb-4">
                                    <div class="form-outline">
                                        <i class="fas fa-user-circle"></i>
                                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" required />
                                        <label class="form-label" for="username">Username</label>
                                    </div>
                                </div>
                            </div> -->
 
                            <div class="form-row">
                                <div class="col-12 mb-4">
                                    <div class="form-outline">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" name="password" id = "mypassword" class="form-control form-control-lg" placeholder="Create password" required />
                                        <label class="form-label" for="password">Password</label>
                                        
                                    </div>
                                        
                                </div>
                            </div>
                            <input type ="checkbox" onclick="showPassword()"> Show Password 
                            <center><button type="submit" name="submit" class="btn-donate">
                                Register
                            </button></center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <!-- Footer -->
    <footer>
        <div class="footer-text">
            <p>&copy; 2024 FEU. All Rights Reserved.</p>
        </div>
    </footer>
 
    <!-- Bootstrap JS and other scripts -->
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
<script>

function showPassword() {

   var x = document.getElementById("mypassword");
    if(x.type === "password"){
        x.type = "text";
    }else{
        x.type = "password";
    }

}

</script>
</html>