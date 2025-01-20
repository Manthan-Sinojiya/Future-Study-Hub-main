<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
}

$msg = $error = "";
$searchTerm = "";
$limitOptions = [1, 5, 10, 15, 20]; // Options for records per page
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default limit
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page or set to 1
$offset = ($page - 1) * $limit; // Calculate the offset for the SQL query

if (isset($_POST['search'])) {
    $searchTerm = trim($_POST['searchTerm']);
}

// Fetch data with join
$sql = "SELECT u.name AS student_name, u.email, sd.enrollment_date 
        FROM student_details sd 
        JOIN users u ON sd.user_id = u.user_id 
        WHERE u.user_type = 'student'";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Students - Admin</title>

    <!-- CSS Files -->
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
        <!-- Top Navbar -->
        <?php include('includes/topbar.php'); ?>

        <div class="container-fluid page-body-wrapper">
            <!-- Sidebar -->
            <?php include('includes/leftbar.php'); ?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h3 class="page-title d-flex align-items-center">
                            <span class="page-title-icon bg-gradient-primary text-white me-2">
                                <i class="mdi mdi-account-multiple"></i>
                            </span>
                            Manage Students
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Manage Students</li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Success or Error Alerts -->
                    <?php if ($msg): ?>
                        <div class="alert alert-success" role="alert">
                            <strong>Well done!</strong> <?= htmlentities($msg); ?>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Oh snap!</strong> <?= htmlentities($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Show List Students</h4>
                        </div>
                    </div>

                    <!-- Student List Table -->
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="studentTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Student Name</th>
                                                    <th>Email</th>
                                                    <th>Enroll Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $cnt = 1;
                                                foreach ($results as $result) { ?>
                                                    <tr>
                                                        <td><?= htmlentities($cnt); ?></td>
                                                        <td><?= htmlentities($result->student_name); ?></td>
                                                        <td><?= htmlentities($result->email); ?></td>
                                                        <td><?= htmlentities($result->enrollment_date); ?></td>
                                                    </tr>
                                                <?php $cnt++;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination Controls -->
                                    <div id="studentTable-pagination-controls" 
                                         class="pagination-controls mt-3 d-flex justify-content-end"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php include('includes/footer.php'); ?>
            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/tableUtility.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/dashboard.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new TableUtility({
                tableId: 'studentTable',
                rowsPerPage: 10,
                searchPlaceholder: 'Search students...'
            });
        });
    </script>
</body>

</html>
