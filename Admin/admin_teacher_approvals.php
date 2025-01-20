<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
}

include('includes/config.php');

function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function sendMail($email, $teacher_name, $subject, $body) {
    require '../vendor/autoload.php';
    $mail = new PHPMailer(true);
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
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
}

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
                $sql = "UPDATE teacher_details SET status='approved' WHERE teacher_id = :teacher_id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $approval_message = "Teacher approved successfully!";

                    // Send approval email
                    $subject = "Teacher Request Approved";
                    $body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc;'>
                            <img src='https://img.freepik.com/free-vector/expert-approved-cartoon-character-holding-checkmark-symbol-hand-finished-task-done-sign-satisfactory-official-sanction-acceptance_335657-2369.jpg' alt='Future Study Hub' style='display: block; margin: 0 auto 20px; width: 350px;'>
                            <h2 style='color: #28a745; text-align: center;'>Congratulations, $teacher_name!</h2>
                            <p style='font-size: 16px;'>
                                We are excited to inform you that your request to become a teacher at <strong>Future Study Hub</strong> has been approved.
                            </p>
                            <p style='font-size: 16px;'>Here are your login credentials:</p>
                            <table style='width: 100%; margin-bottom: 20px;'>
                                <tr>
                                    <td style='font-weight: bold;'>Email:</td>
                                    <td>$email</td>
                                </tr>
                                <tr>
                                    <td style='font-weight: bold;'>Password:</td>
                                    <td>$password</td>
                                </tr>
                            </table>
                            <p style='font-size: 16px;'>You can now login and start sharing your knowledge with students.</p>
                            <p style='font-size: 16px;'>Thank you for joining us!</p>
                            <p style='text-align: center;'>
                                <a href='http://localhost:3000/login.php' style='display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; font-size: 16px; border-radius: 5px;'>Login Now</a>
                            </p>
                            <hr>
                            <p style='font-size: 12px; color: #888; text-align: center;'>Future Study Hub Team</p>
                        </div>
                    ";
                    sendMail($email, $teacher_name, $subject, $body);
                    header('Location:dashboard.php');
                    exit;
                } else {
                    throw new Exception("Error updating record: " . implode(", ", $stmt->errorInfo()));
                }
            } elseif ($_POST['action'] == 'reject') {
                // Update the teacher status in teacher_details
                $sql = "UPDATE teacher_details SET status='rejected' WHERE teacher_id = :teacher_id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $approval_message = "Teacher rejected successfully!";

                    // Send rejection email
                    $subject = "Teacher Request Rejected";
                    $body = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc;'>
                            <img src='https://cdn2.careeraddict.com/uploads/article/58259/illustration-panicked-man-rejection-letter.jpg' alt='Future Study Hub' style='display: block; margin: 0 auto 20px; width: 250px;'>
                            <h2 style='color: #dc3545; text-align: center;'>Hello, $teacher_name</h2>
                            <p style='font-size: 16px;'>
                                We regret to inform you that your request to become a teacher at <strong>Future Study Hub</strong> has been rejected.
                            </p>
                            <p style='font-size: 16px;'>We appreciate your interest in our platform and encourage you to apply again in the future.</p>
                            <p style='font-size: 16px;'>Thank you for considering us.</p>
                            <hr>
                            <p style='font-size: 12px; color: #888; text-align: center;'>Future Study Hub Team</p>
                        </div>
                    ";
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
    $stmt = $dbh->query("SELECT td.teacher_id, u.name AS teacher_name, u.email 
                     FROM teacher_details td
                     JOIN users u ON td.user_id = u.user_id
                     WHERE td.status = 'pending'");

    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}

$dbh = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Approvals - Admin</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</head>

<body>
    <div class="container-scroller">
        <!-- Navbar -->
        <?php include('includes/topbar.php'); ?>

        <div class="container-fluid page-body-wrapper">
            <!-- Sidebar -->
            <?php include('includes/leftbar.php'); ?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Teacher Approvals</h3>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?php if ($approval_message): ?>
                                <div class="alert alert-success"><?= $approval_message; ?></div>
                            <?php elseif ($error_message): ?>
                                <div class="alert alert-danger"><?= $error_message; ?></div>
                            <?php endif; ?>

                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Pending Teacher Requests</h4>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($teachers as $teacher): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($teacher['teacher_id']); ?></td>
                                                        <td><?= htmlspecialchars($teacher['teacher_name']); ?></td>
                                                        <td><?= htmlspecialchars($teacher['email']); ?></td>
                                                        <td>
                                                            <form method="POST" style="display:inline;">
                                                                <input type="hidden" name="teacher_id" value="<?= $teacher['teacher_id']; ?>">
                                                                <input type="hidden" name="teacher_name" value="<?= $teacher['teacher_name']; ?>">
                                                                <input type="hidden" name="email" value="<?= $teacher['email']; ?>">
                                                                <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php include('includes/footer.php'); ?>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>

</html>
