<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code - Tamsakay</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
 
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
 
        .container:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
 
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #05683B;
        }
 
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
 
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
 
        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #05683B;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
 
        button:hover {
            background-color: #FFBF00;
            transform: scale(1.05);
        }
 
        .info {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }
 
        @media (max-width: 600px) {
            h2 {
                font-size: 20px;
            }
            button, input[type="text"] {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>


    <?php 
    
    include $_SERVER['DOCUMENT_ROOT'] . '/tamsakay-finals/Controller/verification.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/tamsakay-finals/db.php';
    if (isset($_POST['submit'])) {
        //variables for user input
        $verification_code = $_POST['verification_code'];
        $verification = new User_verification(); // Object
        $verification->verify_code($db,$verification_code);
    
    }
    ?>
    
   <div class="container">
        <h2>Enter Verification Code</h2>
        <form action="" method="post">
            <input type="text" name="verification_code" placeholder="Enter 6-digit code" required>
            <button type="submit" name="submit">Verify</button>
        </form>
        <div class="info">
            <p>Didn't receive a code? Check your email or <a href="/tamsakay-finals/View/Driver/forgot_password_driver.php" style="color: #05683B; text-decoration: none;">request a new one</a>.</p>
        </div>
    </div>
</body>
</html>