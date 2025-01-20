<?php
session_start();
error_reporting(E_ALL); // Enable full error reporting for debugging

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
}

include('includes/config.php');

// Function to sanitize input data
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to send email using PHPMailer
function sendMail($email, $teacher_name, $subject, $body)
{
    require '../vendor/autoload.php';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->CharSet = 'utf-8';
    $mail->SMTPAuth = true;
    $mail->Username = 'leadergoal12@gmail.com'; // SMTP username
    $mail->Password = 'tppz xjsk ixzj sdzp'; // SMTP password
    $mail->setFrom('leadergoal12@gmail.com', 'Future Study Hub');
    $mail->addAddress($email);
    $mail->addReplyTo('leadergoal12@gmail.com');
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Mail could not be sent. Error: " . $mail->ErrorInfo;
        exit;
    }
}

// Initialize variables for messages
$approval_message = $error_message = "";

// Handle teacher approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['action'])) {
            $teacher_id = clean_input($_POST['teacher_id']);
            $teacher_name = clean_input($_POST['teacher_name']);
            $email = clean_input($_POST['email']);
            $password = clean_input($_POST['password']); // Assume password is sent for approval email

            if ($_POST['action'] == 'approve') {
                $sql = "UPDATE teacher SET status='approved' WHERE teacher_id = :teacher_id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $approval_message = "Teacher approved successfully!";

                    // Send approval email
                    $subject = "Teacher Request Approved";
                    $body = "Dear $teacher_name,<br><br>
                    Your request to become a teacher at Future Study Hub has been approved.<br><br>
                    You can now login using the following credentials:<br>
                    Email: $email<br>
                    Password: $password<br><br>
                    Thank you,<br>
                    Future Study Hub Team";
                    sendMail($email, $teacher_name, $subject, $body);

                    header('Location:dashboard.php');
                    exit;
                } else {
                    throw new Exception("Error updating record: " . implode(", ", $stmt->errorInfo()));
                }
            } elseif ($_POST['action'] == 'reject') {
                $sql = "UPDATE teacher SET status='rejected' WHERE teacher_id = :teacher_id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $approval_message = "Teacher rejected successfully!";

                    // Send rejection email
                    $subject = "Teacher Request Rejected";
                    $body = "Dear $teacher_name,<br><br>
                    We regret to inform you that your request to become a teacher at Future Study Hub has been rejected.<br><br>
                    Thank you for your interest.<br><br>
                    Best regards,<br>
                    Future Study Hub Team";
                    sendMail($email, $teacher_name, $subject, $body);

                    header('Location:dashboard.php');
                    exit;
                } else {
                    throw new Exception("Error updating record: " . implode(", ", $stmt->errorInfo()));
                }
            }
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch pending teacher requests
try {
    $select_sql = "SELECT * FROM teacher WHERE status = 'pending'";
    $stmt = $dbh->query($select_sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Database query failed: " . $e->getMessage();
}

// Close the database connection
$dbh = null;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Teacher Approvals</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <script src="js/modernizr/modernizr.min.js"></script>
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .approval-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #f5f5f5;
        }

        table td form {
            display: inline;
        }

        /* General button styles */
        .button {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Approve button */
        .button-approve {
            background-color: #28a745;
            /* Green color */
            color: white;
        }

        .button-approve:hover {
            background-color: #218838;
            /* Darker green */
        }

        /* Reject button */
        .button-reject {
            background-color: #dc3545;
            width: 109px;
            /* Red color */
            color: white;
        }

        .button-reject:hover {
            background-color: #c82333;
            /* Darker red */
        }
    </style>
</head>

<body class="top-navbar-fixed">
    <div class="main-wrapper">
        <?php include('includes/topbar.php'); ?>
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('includes/leftbar.php'); ?>
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Teacher Approvals</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li> Teachers</li>
                                    <li class="active">Add Teachers</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h5>Teacher Requests</h5>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        if (!empty($approval_message)) {
                                            echo '<div class="approval-message">' . $approval_message . '</div>';
                                        }
                                        if (!empty($error_message)) {
                                            echo '<div class="error-message">' . $error_message . '</div>';
                                        }
                                        ?>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Date of Birth</th>
                                                    <th>Gender</th>
                                                    <th>Module Name</th>
                                                    <th>Course Field</th>
                                                    <th>Hire Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($result as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['teacher_name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['module_name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['course_field']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['hire_date']) . "</td>";
                                                    echo "<td style='text-align: center;'>
                                                            <form method='post' action='' style='display:inline;'>
                                                                <input type='hidden' name='teacher_id' value='" . htmlspecialchars($row['teacher_id']) . "'>
                                                                <input type='hidden' name='teacher_name' value='" . htmlspecialchars($row['teacher_name']) . "'>
                                                                <input type='hidden' name='email' value='" . htmlspecialchars($row['email']) . "'>
                                                                <input type='hidden' name='password' value='" . htmlspecialchars($row['password']) . "'>
                                                                <input type='hidden' name='action' value='approve'>
                                                                <button type='submit' class='button button-approve'>Approve</button>
                                                            </form>
                                                            <form method='post' action='' style='display:inline;'>
                                                                <input type='hidden' name='teacher_id' value='" . htmlspecialchars($row['teacher_id']) . "'>
                                                                <input type='hidden' name='teacher_name' value='" . htmlspecialchars($row['teacher_name']) . "'>
                                                                <input type='hidden' name='email' value='" . htmlspecialchars($row['email']) . "'>
                                                                <input type='hidden' name='action' value='reject'>
                                                                <button type='submit' class='button button-reject'>Reject</button>
                                                            </form>
                                                        </td>
                                                        ";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/prism/prism.js"></script>
    <script src="js/main.js"></script>
</body>

</html>