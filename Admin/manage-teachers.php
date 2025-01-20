<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
}

// Fetch all teachers data with user details
$sql = "
    SELECT 
        teacher_details.teacher_id, 
        teacher_details.course_field, 
        teacher_details.status, 
        teacher_details.module_name, 
        teacher_details.hire_date, 
        users.name AS teacher_name, 
        users.email, 
        users.dob, 
        users.gender 
    FROM 
        teacher_details 
    JOIN 
        users 
    ON 
        teacher_details.user_id = users.user_id 
    WHERE 
        users.user_type = 'teacher'
";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Teachers - Admin</title>
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
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h3 class="page-title d-flex align-items-center">
                            <span class="page-title-icon bg-gradient-primary text-white me-2">
                                <i class="mdi mdi-account-multiple"></i>
                            </span>
                            Manage Teachers
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="dashboard.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Manage Teachers</li>
                            </ul>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="card-title">Teacher List</h4>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover custom-table" id="teacherTable">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Date of Birth</th>
                                                    <th>Gender</th>
                                                    <th>Module Name</th>
                                                    <th>Course Field</th>
                                                    <th>Hire Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $cnt = 1;
                                                if ($query->rowCount() > 0) {
                                                    foreach ($results as $result) { ?>
                                                        <tr>
                                                            <td><?= htmlentities($cnt); ?></td>
                                                            <td><?= htmlentities($result->teacher_name); ?></td> <!-- Updated to use teacher_name -->
                                                            <td><?= htmlentities($result->email); ?></td> <!-- Updated to use email -->
                                                            <td><?= htmlentities($result->dob); ?></td>
                                                            <td><?= htmlentities($result->gender); ?></td>
                                                            <td><?= htmlentities($result->module_name); ?></td>
                                                            <td><?= htmlentities($result->course_field); ?></td>
                                                            <td><?= htmlentities($result->hire_date); ?></td>
                                                            <td>
                                                                <?php
                                                                $status = $result->status;
                                                                $badgeClass = $status === 'approved' ? 'badge-success' : ($status === 'pending' ? 'badge-warning' : 'badge-danger');
                                                                ?>
                                                                <label class="badge <?= $badgeClass; ?>">
                                                                    <?= ucfirst($status); ?>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                    <?php $cnt++;
                                                    }
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">No teachers found.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>

                                        </table>
                                    </div>
                                    <!-- Pagination controls below the table -->
                                    <div id="teacherTable-pagination-controls" class="pagination-controls mt-3 d-flex justify-content-end"></div>
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
    <script src="assets/js/dashboard.js"></script>
    <!-- Custom Table Utility JS -->
    <script src="assets/js/tableUtility.js"></script>
    <script src="assets/js/off-canvas.js"></script>

    <!-- Initialize the Table Utility -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new TableUtility({
                tableId: 'teacherTable',
                rowsPerPage: 10, // Default number of rows per page
                searchPlaceholder: 'Search teachers...' // Placeholder for the search box
            });
        });
    </script>
</body>

</html>