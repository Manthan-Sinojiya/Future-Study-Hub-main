<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
} else {
    if (isset($_POST['submit'])) {
        $password = $_POST['password'];
        $newpassword = $_POST['newpassword'];
        $username = $_SESSION['alogin'];
        
        // Fetch admin details including password from database
        $sql = "SELECT * FROM admin";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $current_password = $result['password'];

        // Verify current password
        if ($password === $current_password) {
            // Update password in database
            $con = "UPDATE admin SET password=:newpassword";
            $chngpwd1 = $dbh->prepare($con);
            $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
            $chngpwd1->execute();

            // Update password in session
            $_SESSION['password'] = $newpassword;

            $msg = "Your Password has been changed successfully.";
        } else {
            $error = "Your current password is wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin | Change Password</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        function valid() {
            if (document.chngpwd.newpassword.value != document.chngpwd.confirmpassword.value) {
                alert("New Password and Confirm Password Field do not match  !!");
                document.chngpwd.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="container-scroller">
        <?php include('includes/topbar.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include('includes/leftbar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Change Password</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Change Password</h4>
                                    <?php if ($msg) { ?>
                                        <div class="alert alert-success" role="alert">
                                            <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                        </div>
                                    <?php } else if ($error) { ?>
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                        </div>
                                    <?php } ?>
                                    <form name="chngpwd" method="post" onSubmit="return valid();">
                                        <div class="form-group">
                                            <label for="currentPassword">Current Password</label>
                                            <input type="password" name="password" class="form-control" id="currentPassword" required="required">
                                        </div>
                                        <div class="form-group">
                                            <label for="newPassword">New Password</label>
                                            <input type="password" name="newpassword" class="form-control" id="newPassword" required="required">
                                        </div>
                                        <div class="form-group">
                                            <label for="confirmPassword">Confirm Password</label>
                                            <input type="password" name="confirmpassword" class="form-control" id="confirmPassword" required="required">
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-gradient-primary me-2">Change Password</button>
                                        <button type="reset" class="btn btn-light">Cancel</button>
                                    </form>
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
</body>

</html>
