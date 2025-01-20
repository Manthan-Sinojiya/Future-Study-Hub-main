<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = "";
include('./assets/include/db.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    function sendMail($email, $otp) {
        require './vendor/autoload.php';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Username = 'leadergoal12@gmail.com';
            $mail->Password = 'tppz xjsk ixzj sdzp';
            $mail->setFrom('leadergoal12@gmail.com', 'Future Study Hub');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your One-Time Password (OTP)';    
           
                        $message = "
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4CAF50;
            padding: 20px;
            color: #ffffff;
            text-align: center;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #333;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #00ff0f;
            margin: 20px 0;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        .image-container {
            margin: 20px 0;
        }
        p{
            color: #85c1e9;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            Future Study Hub
        </div>
        <div class='content'>
            <h2>Hello,</h2>
            <p>Here is your One-Time Password (OTP) for login:</p>
            <div class='otp-code'>$otp</div>
            <div class='image-container'>
                <img src='https://img.freepik.com/premium-photo/boy-using-tablet-distance-learning-online-vector-cartoon-illustration_1240525-109115.jpg?w=740' alt='Mail Image' style='width:100%; max-width:300px;'>
            </div>
        </div>
        <div class='footer'>
            &copy; 2024 Future Study Hub. All Rights Reserved.
        </div>
    </div>
</body>
</html>";



            $mail->Body = $message;
            $mail->send();
        } catch (Exception $e) {
            echo 'Mailer Error: ', $mail->ErrorInfo;
        }
    }

    // Rate limiting for OTP requests
    if (isset($_SESSION['otp_last_request']) && time() - $_SESSION['otp_last_request'] < 60) {
        $msg = "Please wait a minute before requesting a new OTP.";
    } else {
        // Check if user is an admin
        $admin_query = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
        $admin_result = mysqli_query($conn, $admin_query);

        if (mysqli_num_rows($admin_result) == 1) {
            $_SESSION['role'] = 'admin';
            $_SESSION['email'] = $email;

            $_SESSION['otp'] = mt_rand(100000, max: 999999);
            $_SESSION['otp_last_request'] = time();
            $_SESSION['otp_verified'] = false;

            sendMail($email, $_SESSION['otp']);
            echo "<script>window.location.href='Admin/otp.php';</script>";
            exit;
        }

        // Check if user is a student or teacher in users table
        $user_query = "SELECT * FROM users WHERE email='$email'";
        $user_result = mysqli_query($conn, $user_query);

        if (mysqli_num_rows($user_result) == 1) {
            $user_row = mysqli_fetch_assoc($user_result);

            if (password_verify($password, $user_row['password_hash'])) {
                $_SESSION['user_id'] = $user_row['user_id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user_row['user_type'];

                $_SESSION['otp'] = mt_rand(100000, 999999);
                $_SESSION['otp_last_request'] = time();
                $_SESSION['otp_verified'] = false;

                sendMail($email, $_SESSION['otp']);

                if ($user_row['user_type'] == 'student') {
                    $student_query = "SELECT student_id FROM student_details WHERE user_id=" . $user_row['user_id'];
                    $student_result = mysqli_query($conn, $student_query);
                    if ($student_result && mysqli_num_rows($student_result) == 1) {
                        $student_row = mysqli_fetch_assoc($student_result);
                        $_SESSION['student_id'] = $student_row['student_id'];
                    }
                    echo "<script>window.location.href='otp.php';</script>";
                    exit;
                } elseif ($user_row['user_type'] == 'teacher') {
                    $teacher_query = "SELECT * FROM teacher_details WHERE user_id=" . $user_row['user_id'];
                    $teacher_result = mysqli_query($conn, $teacher_query);
                
                    if ($teacher_result && mysqli_num_rows($teacher_result) == 1) {
                        $teacher_row = mysqli_fetch_assoc($teacher_result);
                
                        if ($teacher_row['status'] === 'approved') {
                            $_SESSION['teacher_id'] = $teacher_row['teacher_id'];
                            echo "<script>window.location.href='Teacher/otp.php';</script>";
                            exit;
                        } else {
                            $msg = "<div style='color: red; font-weight: bold;'>Teacher account not approved. Please wait for approval.</div>";
                        }
                    } else {
                        $msg = "<div style='color: red; font-weight: bold;'>No teacher details found.</div>";
                    }
                }
            } else {
                $msg = "Invalid password.";
            }
        } else {
            $msg = "Email not found.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login Form</title>
    <link rel="stylesheet" href="./assets/css/login.css">
</head>

<body class="mybg">
    <section>
        <h1>Future Study Hub</h1>
        <div class="main">
            <div class="mybg">
                <form action="" method="post">
                    <h3>Login Form</h3>
                    <?php echo $msg; ?>
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" placeholder="Enter your Email" autocomplete="off" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your Password" autocomplete="off" required>
                    <a class="forget" href="./forgetpass.php">Forget Password</a>

                    <div class="wrap">
                        <button type="submit">Submit</button>
                        <button class="home-button" onclick="location.href='index.php'" style="width: 75px;">Home</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="login">Not registered? <a class="login1" href="./Registration.php">Create an account</a></p>
    </section>
    <script>
        function goHome() {
            window.location.href = 'index.php';
        }
    </script>
</body>

</html>
