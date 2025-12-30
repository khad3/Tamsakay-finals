<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay-finals/db.php'; // Ensure db.php contains a mysqli connection
 //include the php mailer

 include $_SERVER['DOCUMENT_ROOT'] . '/Tamsakay-finals/vendor/autoload.php';

 use PHPMailer\PHPMailer\PHPMailer; 
 use PHPMailer\PHPMailer\Exception;

 class User_registration {

    // Attributes for user registration
    public string $first_name = '';
    public string $last_name = '';
    public string $username = '';
    public string $password = '';
    public string $email = '';
    public string $list = '';

    public function registration($db, $first_name, $last_name, $username, $password, $email , $list) {
        // Check if the email already exists in the database
        $stmt = $db->prepare("SELECT email FROM for_user_registration_tbl WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<script language="javascript">
            alert("Email already exists.");
           </script>';
        } else {
            // Generate a verification code
            $pin = rand(100000, 999999);

            // Hash the password before saving it into the database
            // $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Setup PHPMailer for sending the verification email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'pagawpawjemoyacerenado@gmail.com'; // Your email
                $mail->Password = 'ufgj dayy eamf fluu'; // Your app-specific password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Send Email
                $mail->setFrom('kathleendonasco@gmail.com','Tamsakay'); // Your email
                $mail->addAddress($email);
                $mail->addReplyTo('pagawpawjemoyacerenado@gmail.com'); // Your email
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = "Verification code!";
                $mail->Body = "Hi, your verification code is " . $pin;

                $mail->send();
                $_SESSION['message'] = 'Message has been sent';
            } catch (Exception $e) {
                $_SESSION['message'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
                return;
            }

          // Hash the password using PASSWORD_DEFAULT
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user data into the database
$stmt = $db->prepare("INSERT INTO for_user_registration_tbl (first_name, last_name, user_name, password, verification_code, verification_status, email, passenger_type) VALUES (?, ?, ?, ?, ?, 0, ?, ?)");
$stmt->bind_param('sssssss', $first_name, $last_name, $username, $hashed_password, $pin, $email, $list);

if ($stmt->execute()) {
    echo '<script language="javascript">
            alert("Registration Successfully!");
            window.location="verification_user.php";
          </script>';
} else {
    echo "Error: " . $stmt->error;
    echo '<script language="javascript">
            alert("Registration Failed!"); 
          </script>';
}

        }
    }
}
        
            
        
    



    









?>