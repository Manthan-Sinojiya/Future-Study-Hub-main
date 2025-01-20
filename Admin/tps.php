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
                                <input type="date" name="date" class="form-control me-2" required>
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
                                        <table id="studentReportTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th onclick="sortTable(0)">No</th>
                                                    <th onclick="sortTable(1)">Enrollment Date</th>
                                                    <th onclick="sortTable(2)">Name</th>
                                                    <th onclick="sortTable(3)">Email</th>
                                                    <th onclick="sortTable(4)">DOB</th>
                                                    <th onclick="sortTable(5)">Gender</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                                    include 'includes/db.php';
                                                    $date = $_POST['date'];
                                                    $cnt = 1;

                                                    $sql = "SELECT u.name AS student_name, u.email, sd.enrollment_date 
                                                            FROM student_details sd 
                                                            JOIN users u ON sd.user_id = u.user_id 
                                                            WHERE u.user_type = 'student' AND enrollment_date = '$date'";
                                                    $result = $conn->query($sql);

                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
                                                                <td>{$cnt}</td>
                                                                <td>" . htmlspecialchars($row['enrollment_date']) . "</td>
                                                                <td>" . htmlspecialchars($row['student_name']) . "</td>
                                                                <td>" . htmlspecialchars($row['email']) . "</td>
                                                                <td>" . htmlspecialchars($row['dob']) . "</td>
                                                                <td>" . htmlspecialchars($row['gender']) . "</td>
                                                              </tr>";
                                                            $cnt++;
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='6' class='text-center'>No registrations found.</td></tr>";
                                                    }
                                                    $conn->close();
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <div id="studentReportTable-pagination-controls" class="pagination-controls d-flex justify-content-end mt-3"></div>
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
                tableId: 'studentReportTable',
                rowsPerPage: 10,
                searchPlaceholder: 'Search report...'
            });
        });

        function sortTable(columnIndex) {
            const table = document.getElementById('studentReportTable');
            const rows = Array.from(table.rows).slice(1); // Skip header row
            const sortedRows = rows.sort((a, b) => {
                const aText = a.cells[columnIndex].innerText;
                const bText = b.cells[columnIndex].innerText;

                return aText.localeCompare(bText);
            });

            // Clear the table and append sorted rows
            table.tBodies[0].innerHTML = '';
            sortedRows.forEach(row => table.tBodies[0].appendChild(row));
        }
    </script>
</body>

</html>