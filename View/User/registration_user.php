<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay-finals/Controller/Registration_user.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay-finals/db.php';

if (isset($_POST['submit'])) {
    // Variables for user input
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    //$username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $list = $_POST['passenger_type'];

    // Registration logic
    $registration = new User_registration(); // Object
    $registration->registration($db, $first_name, $last_name, $username, $password, $email, $list);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Registration</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --feu-green: #006400; /* FEU green */
            --feu-dark-green: #05683B; /* Darker green */
            --feu-yellow: #FFD700; /* FEU yellow */
        }
 
        /* Hover effect for the registration card */
        .card:hover {
            transform: scale(1.02); /* Slightly grow */
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; /* Smooth animation */
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2); /* Slight shadow */
        }
 
        /* Button styling */
        .btn-register {
            background-color: var(--feu-green);
            color: white;
            font-weight: 600;
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out;
        }
 
        .btn-register:hover {
            background-color: #FFBF00;
            transform: scale(1.02);
        }
 
        /* Ensure header has consistent color */
        header {
            background-color: #05683B;
        }
 
        .logo {
            width: 50px;
            margin-right: 15px;
        }
 
        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
 
        .header-container h1 {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
       
       
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">



<!-- Header -->
<header class="py-4 shadow-md">
    <div class="header-container container mx-auto">
        <img src="/tamsakay-finals/Tamsakay Logo.png" alt="Tamsakay Logo" class="logo">
        <h1 class="text-2xl font-bold text-white">Tamsakay Shuttle Service</h1>
    </div>
</header>
 
<!-- Registration Form -->
<div class="flex flex-col justify-center items-center flex-grow">
    <div class="card bg-white w-full max-w-lg p-8 shadow-lg rounded-lg">
        <h2 class="text-center text-2xl font-bold text-feu-green mb-4">Passenger Registration</h2>
        <p class="text-center text-gray-600 mb-6">Complete the form below to join our service.</p>
 
        <form method="POST" class="space-y-4">
            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-feu-green">First Name</label>
                <input type="text" name="first_name"
                       placeholder="Enter your first name"
                       class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-feu-green focus:ring-opacity-50">
            </div>
            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-feu-green">Last Name</label>
                <input type="text" name="last_name"
                       placeholder="Enter your last name"
                       class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-feu-green focus:ring-opacity-50">
            </div>
            <!-- Passenger Type -->
            <div>
                <label for="passenger_type" class="block text-sm font-medium text-feu-green">Passenger Type</label>
                <select name="passenger_type"
                        class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-feu-green focus:ring-opacity-50">
                    <option value="" disabled selected>Select passenger type</option>
                    <option value="STUDENT">STUDENT</option>
                    <option value="FACULTY">FACULTY</option>
                    <option value="VISITOR">VISITOR</option>
                    <option value="PARENT">PARENT</option>
                </select>
            </div>
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-feu-green">Email</label>
                <input type="email" name="email"
                       placeholder="Enter your email"
                       class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-feu-green focus:ring-opacity-50">
            </div>
            <!-- Username
            <div>
                <label for="username" class="block text-sm font-medium text-feu-green">Username</label>
                <input type="text" name="username"
                       placeholder="Enter your username"
                       class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-feu-green focus:ring-opacity-50">
            </div> -->
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-feu-green">Password</label>
                <input type="password" name="password" id = "mypassword"
                       placeholder="Create password"
                       class="w-full mt-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-feu-green focus:ring-opacity-50">

                      
            </div>
            <input type = "checkbox" onclick="showpassword()"> Show Password 
            <!-- Submit Button -->
            <div>
                <button type="submit" name="submit"
                        class="btn-register w-full py-2 rounded-md shadow-md">
                    Register
                </button>
            </div>
        </form>
    </div>
</div>
 
<!-- Footer -->
<!-- Footer -->
<footer class="bg-gray-50 text-center py-6 text-gray-600 text-sm border-t border-gray-300 mt-8">
    <p>© 2024 Tamsakay. All rights reserved.</p>
</footer>
 

</body>

<script>

function showpassword(){
    var x = document.getElementById("mypassword");
    if(x.type === "password"){
        x.type = "text";
    }else{
        x.type = "password";
    }
}

</script>
</html>
 
