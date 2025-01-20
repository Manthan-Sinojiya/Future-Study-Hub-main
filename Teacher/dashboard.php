<?php
session_start();
error_reporting(1);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("location:../login.php");
    exit;
} else {
    // Fetching data for the pie chart
    $sql1 = "SELECT student_id FROM student_details";
    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $totalstudents = $query1->rowCount();

    $sql2 = "SELECT teacher_id FROM teacher_details";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $totalsubjects = $query2->rowCount();

    $sql3 = "SELECT material_id FROM material";
    $query3 = $dbh->prepare($sql3);
    $query3->execute();
    $totalclasses = $query3->rowCount();

    $sql4 = "SELECT video_id FROM videos";
    $query4 = $dbh->prepare($sql4);
    $query4->execute();
    $totalvideo = $query4->rowCount();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Future Study Hub | Dashboard</title>
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
        <link rel="stylesheet" href="css/toastr/toastr.min.css" media="screen">
        <link rel="stylesheet" href="css/icheck/skins/line/blue.css">
        <link rel="stylesheet" href="css/icheck/skins/line/red.css">
        <link rel="stylesheet" href="css/icheck/skins/line/green.css">
        <link rel="stylesheet" href="css/main.css" media="screen">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <style>
            #myPieChart {
                width: 50% !important;
                height: auto !important;
            }

            /* For screens up to 768px wide */
            @media (max-width: 768px) {

                /* Hide the sidebar initially on mobile view */
                .left-sidebar {
                    position: fixed;
                    width: 250px;
                    left: -250px;
                    top: 0;
                    bottom: 0;
                    background: #333;
                    transition: left 0.3s;
                    z-index: 9999;
                }

                /* When sidebar is open */
                .left-sidebar.open {
                    left: 0;
                }

                /* Main content should take full width when sidebar is hidden */
                .content-container {
                    margin-left: 0;
                }

                /* Adjust top bar */
                .top-navbar .navbar-toggle {
                    display: inline-block;
                }

                /* Hide icons and shorten text on smaller screens */
                .side-nav li a {
                    font-size: 14px;
                }

                /* Adjust the main container when sidebar is visible */
                .main-wrapper.overlay-active .content-container {
                    margin-left: 250px;
                }
            }
        </style>
        <script src="js/modernizr/modernizr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
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
                                <div class="col-sm-6">
                                    <h2 class="title">Dashboard</h2>
                                </div>
                            </div>
                        </div>

                        <section class="section">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3>Statistics Overview</h3>
                                        <canvas id="myPieChart"></canvas> <!-- Canvas for Pie Chart -->
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== COMMON JS FILES ========== -->
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/jquery-ui/jquery-ui.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/pace/pace.min.js"></script>
        <script src="js/lobipanel/lobipanel.min.js"></script>
        <script src="js/iscroll/iscroll.js"></script>
        <script src="js/toastr/toastr.min.js"></script>
        <script src="js/main.js"></script>

        <script>
            $(document).ready(function() {
                $('.mobile-nav-toggle').on('click', function() {
                    $('.left-sidebar').toggleClass('open');
                    $('.main-wrapper').toggleClass('overlay-active');
                });

                // Close sidebar when clicking outside on mobile view
                $(document).click(function(e) {
                    if (!$(e.target).closest('.left-sidebar, .mobile-nav-toggle').length) {
                        $('.left-sidebar').removeClass('open');
                        $('.main-wrapper').removeClass('overlay-active');
                    }
                });
            });

            $(function() {
                // Pie chart
                var ctx = document.getElementById('myPieChart').getContext('2d');
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Registered Users', 'Profiles', 'Materials', 'Videos'],
                        datasets: [{
                            label: 'Statistics',
                            data: [
                                <?php echo htmlentities($totalstudents); ?>,
                                <?php echo htmlentities($totalsubjects); ?>,
                                <?php echo htmlentities($totalclasses); ?>,
                                <?php echo htmlentities($totalvideo); ?>
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
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Dashboard Statistics'
                            }
                        }
                    }
                });

                // Welcome notification
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
                toastr["success"]("Welcome to Future Study Hub!");
            });
        </script>
    </body>

    </html>
<?php } ?>