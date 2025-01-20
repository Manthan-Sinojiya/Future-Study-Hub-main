<?php
session_start();
include('./assets/include/config.php');
$msg = '';

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Sanitize and fetch form inputs
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Validate passwords (you can add more rules like password length here)
    if (strlen($password) < 6) {
        $msg = "Password should be at least 6 characters.";
    } elseif ($password !== $cpassword) {
        $msg = "Password and Confirm Password do not match.";
    } else {
        // Fetch student details based on session ID
        $sql = "SELECT * FROM student_details WHERE student_id = :student_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Hash the new password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update the password in the database
            $sql_update = "UPDATE student_details SET password = :password WHERE student_id = :student_id";
            $chngpwd1 = $dbh->prepare($sql_update);
            $chngpwd1->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $chngpwd1->bindParam(':student_id', $_SESSION['student_id'], PDO::PARAM_INT);
            $chngpwd1->execute();

            // Password successfully changed
            $msg = "Your password has been changed successfully.";
        } else {
            // Student record not found
            $msg = "Student record not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Future Study Hub</title>
    <link rel="stylesheet" href="./assets/css/login.css">
</head>

<body>
    <h1>Future Study Hub</h1>
    <div class="main">
        <form action="" method="post">
            <h3>Change Password</h3>
            <?php echo "<p>$msg</p>"; ?>
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your New Password" autocomplete="off" required>

            <label for="cpassword">Confirm Password:</label>
            <input type="password" id="cpassword" name="cpassword" placeholder="Enter your Confirm Password" autocomplete="off" required>

            <div class="wrap">
                <button type="submit" name="submit">Submit</button>
            </div>
        </form>
    </div>
</body>

</html>
