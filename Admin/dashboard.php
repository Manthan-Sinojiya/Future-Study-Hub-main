<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check user role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
} else {
    // Fetch data for the pie chart
    $sql1 = "SELECT COUNT(student_id) AS totalstudents FROM student_details";
    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $totalstudents = $result1['totalstudents'];

    $sql2 = "SELECT COUNT(course_id) AS totalcourses FROM courses";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $totalsubjects = $result2['totalcourses'];

    $sql3 = "SELECT COUNT(sub_id) AS totalclasses FROM subject";
    $query3 = $dbh->prepare($sql3);
    $query3->execute();
    $result3 = $query3->fetch(PDO::FETCH_ASSOC);
    $totalclasses = $result3['totalclasses'];

    $sql4 = "SELECT COUNT(id) AS totalnotice FROM notice";
    $query4 = $dbh->prepare($sql4);
    $query4->execute();
    $result4 = $query4->fetch(PDO::FETCH_ASSOC);
    $totalnotice = $result4['totalnotice'];
?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico" />
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
                    <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                            <i class="mdi mdi-home"></i>
                        </span> Dashboard
                    </h3>
                </div>
                <div class="row">
                    <div class="col-md-4 stretch-card grid-margin">
                        <div class="card bg-gradient-info card-img-holder text-white">
                            <div class="card-body" onclick="window.location.href='manage-students.php';" style="cursor: pointer;">
                            <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                                <h4 class="font-weight-normal mb-3">Total Students<i class="mdi mdi-account mdi-24px float-right"></i></h4>
                                <h2 class="mb-5"><?php echo htmlentities($totalstudents); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 stretch-card grid-margin">
                        <div class="card bg-gradient-success card-img-holder text-white">
                            <div class="card-body" onclick="window.location.href='manage-courses.php';" style="cursor: pointer;">
                            <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                                <h4 class="font-weight-normal mb-3">Total Subjects<i class="mdi mdi-book mdi-24px float-right"></i></h4>
                                <h2 class="mb-5"><?php echo htmlentities($totalsubjects); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 stretch-card grid-margin">
                        <div class="card bg-gradient-danger card-img-holder text-white">
                            <div class="card-body" onclick="window.location.href='manage-notices.php';" style="cursor: pointer;">
                            <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                                <h4 class="font-weight-normal mb-3">Total Notices<i class="mdi mdi-bell mdi-24px float-right"></i></h4>
                                <h2 class="mb-5"><?php echo htmlentities($totalnotice); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-lg-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Statistics</h4>
                                <canvas id="doughnutChart" style="height:250px"></canvas>
                                <div id="doughnutChart-legend" class="rounded-legend legend-vertical legend-bottom-left pt-4"></div>
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
<script src="assets/vendors/chart.js/Chart.min.js"></script>
<script src="assets/js/off-canvas.js"></script>
<script src="assets/js/hoverable-collapse.js"></script>
<script src="assets/js/misc.js"></script>
<script src="assets/js/dashboard.js"></script>
<script>
    var ctx = document.getElementById('doughnutChart').getContext('2d');
    var dashboardChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Students', 'Subjects', 'Classes', 'Notices'],
            datasets: [{
                data: [
                    <?php echo htmlentities($totalstudents); ?>,
                    <?php echo htmlentities($totalsubjects); ?>,
                    <?php echo htmlentities($totalclasses); ?>,
                    <?php echo htmlentities($totalnotice); ?>
                ],
                backgroundColor: [
                                'rgba(54, 162, 235, 0.6)', // Blue
                                'rgba(255, 99, 132, 0.6)', // Red
                                'rgba(255, 206, 86, 0.6)', // Yellow
                                'rgba(75, 192, 192, 0.6)' // Green
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)'
                            ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Dashboard Statistics' }
            }
        }
    });
</script>
</body>
</html>

<?php
}
?>