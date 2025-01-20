<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Student Registration Report</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container-scroller">
        <?php include('includes/topbar.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include('includes/leftbar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h3 class="page-title d-flex align-items-center">
                            <span class="page-title-icon bg-gradient-primary text-white me-2">
                                <i class="mdi mdi-file-document"></i>
                            </span>
                            Student Registration Report
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Registration Report</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Generate Student Registration Report</h4>
                            <form method="post" class="d-flex">
                                <input type="date" name="startDate" class="form-control me-2" required>
                                <input type="date" name="endDate" class="form-control me-2" required>
                                <button class="btn btn-primary" type="submit" name="submit">Generate Report</button>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Report Results</h4>
                                    <div class="table-responsive">
                                        <table id="reportTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Registrations Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                                    include 'includes/db.php';
                                                    $startDate = $_POST['startDate'];
                                                    $endDate = $_POST['endDate'];

                                                    $sql = "SELECT DATE(enrollment_date) AS enrollment_date, COUNT(*) AS enrollment 
                                                            FROM student_details 
                                                            WHERE enrollment_date BETWEEN '$startDate' AND '$endDate' 
                                                            GROUP BY DATE(enrollment_date)";
                                                    $result = $conn->query($sql);

                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
                                                                <td>" . htmlspecialchars($row['enrollment_date']) . "</td>
                                                                <td>" . htmlspecialchars($row['enrollment']) . "</td>
                                                              </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='2' class='text-center'>No registrations found.</td></tr>";
                                                    }
                                                    $conn->close();
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <div id="reportTable-pagination-controls" class="pagination-controls d-flex justify-content-end mt-3"></div>
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

    <!-- JS Files -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/tableUtility.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/dashboard.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new TableUtility({
                tableId: 'reportTable',
                rowsPerPage: 10,
                searchPlaceholder: 'Search report...'
            });
        });
    </script>
</body>

</html>
