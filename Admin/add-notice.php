<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
} else {
    if (isset($_POST['submit'])) {
        $ntitle = $_POST['noticetitle'];
        $ndetails = $_POST['noticedetails'];
        $sql = "INSERT INTO notice(noticeTitle, noticeDetails) VALUES(:ntitle, :ndetails)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ntitle', $ntitle, PDO::PARAM_STR);
        $query->bindParam(':ndetails', $ndetails, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        
        if ($lastInsertId) {
            echo '<script>alert("Notice added successfully")</script>';
            echo "<script>window.location.href ='manage-notices.php'</script>";
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin | Add Notice</title>
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
        <?php include('includes/topbar.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include('includes/leftbar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Add Notice</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="manage-notices.php">Notices</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Notice</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Add Notice</h4>
                                    <p class="card-description"> Enter notice details below </p>
                                    <form class="forms-sample" method="post">
                                        <div class="form-group">
                                            <label for="noticetitle">Notice Title</label>
                                            <input type="text" name="noticetitle" class="form-control" id="noticetitle" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="noticedetails">Notice Details</label>
                                            <textarea class="form-control" name="noticedetails" id="noticedetails" rows="5" required></textarea>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-gradient-primary me-2">Submit</button>
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
<?php } ?>
