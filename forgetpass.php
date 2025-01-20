<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "future_study_hub1";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";

// Start session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $email = $conn->real_escape_string($_POST['email']);
    // $password = $conn->real_escape_string($_POST['password']);
    function sendMail($conn)
    {
        $mail = $_POST['email'];
        $r = mt_rand(100000, 999999);
        $sqlotp = "UPDATE onetimepassword SET otp = '$r', mail = '$mail'";
        $resultotp = mysqli_query($conn, $sqlotp);

        require './vendor/autoload.php';

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->CharSet = 'utf-8';
        $mail->SMTPAuth = true;
        $mail->Username   = 'leadergoal12@gmail.com';                     //SMTP username
        $mail->Password   = 'tppz xjsk ixzj sdzp';
        $mail->setFrom('leadergoal12@gmail.com');
        $mail->addAddress($_POST['email']);
        $mail->addReplyTo('leadergoal12@gmail.com');
        $mail->SMTPDebug = false;
        $mail->isHTML(true);
        $message = "<html><body> Your OTP is:- $r </body></html>";
        $mail->Subject = 'Future Study Hub';
        $mail->Body = $message;

        if (!$mail->send()) {
            echo '<script>alert("Message could not be sent.");</script>';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }
    $query = "SELECT * FROM student WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    // Check if user is a student
    if (mysqli_num_rows($result) == 1) {
        // User is a student
        $_SESSION['role'] = 'student';
        // $_SESSION['email'] = $email;
        sendMail($conn);
        header("location:forgetotp.php");
        // header("Location:./index.php");
        exit;
    }

    // Invalid username or password
    $msg = "<div class='alert alert-danger'>Email or password do not match.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <link rel="icon" href="./images/fj.png" type="image/gif" sizes="16x16"> -->
    <title>Future Study Hub</title>

    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700,800,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">

    <link rel="stylesheet" href="css/animate.css">

    <link rel="stylesheet" href="css/owl.carousel.min.css">

    <link rel="stylesheet" href="css/owl.theme.default.min.css">

    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">

    <link rel="stylesheet" href="css/flaticon.css">

    <link rel="stylesheet" href="css/icomoon.css">

    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->

    <style>
        body {
            background: #eee;
        }

        .card {
            box-shadow: 0 20px 27px 0 rgb(0 0 0 / 5%);
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0 solid rgba(0, 0, 0, .125);
            border-radius: 1rem;
        }

        .img-thumbnail {
            padding: .25rem;
            background-color: #ecf2f5;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            max-width: 100%;
            height: auto;
        }

        .avatar-lg {
            height: 150px;
            width: 150px;
        }
    </style>

</head>

<body>
    <div class="container">
        <br>
        <div class="row">
            <div class="col-lg-5 col-md-7 mx-auto my-auto">
                <div class="card">
                    <div class="card-body px-lg-5 py-lg-5 text-center">
                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png" class="rounded-circle avatar-lg img-thumbnail mb-4" alt="profile-image">
                        <h2 class="text-info">Forgot Password?</h2>
                        <p class="mb-4">You can reset your password here.</p>
                        <form  method="post">
                            <div class="row mb-4">
                                <div class="col-lg col-md-2 col-8 ps-0 ps-md-2">
                                    <input type="email" class="form-control text-lg text-center" placeholder="Enter your Email Address" name="email" required>
                                </div>

                            </div>
                            <div class="text-center">
                                <input type="submit" name="con" class="btn bg-info btn-lg my-4">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>