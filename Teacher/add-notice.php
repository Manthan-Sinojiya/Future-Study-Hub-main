<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("location:../login.php");
    exit;
}else {
    if (isset($_POST['submit'])) {
        $ntitle = $_POST['noticetitle'];
        $ndetails = $_POST['noticedetails'];
        $sql = "INSERT INTO  notice(noticeTitle,noticeDetails) VALUES(:ntitle,:ndetails)";
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
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Add Notice</title>

                <link rel="stylesheet" href="css/prism/prism.css" media="screen">
        <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css" />
        <link rel="stylesheet" href="css/main.css" media="screen">
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
        <link rel="stylesheet" href="css/toastr/toastr.min.css" media="screen">
        <link rel="stylesheet" href="css/icheck/skins/line/blue.css">
        <link rel="stylesheet" href="css/icheck/skins/line/red.css">
        <link rel="stylesheet" href="css/icheck/skins/line/green.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
               
            <script src="js/modernizr/modernizr.min.js"></script>
            
        <style>
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

            .errorWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #dd3d36;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            }

            .succWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #5cb85c;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            }
        </style>

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
                                <div class="col-md-6">
                                    <h2 class="title">Add Notice</h2>
                                </div>
                            </div>
                            <div class="row breadcrumb-div">
                                <div class="col-md-6">
                                    <ul class="breadcrumb">
                                        <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                        <li><a href="#">Notices</a></li>
                                        <li class="active">Add Notice</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <section class="section">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="panel">
                                            <div class="panel-heading">
                                                <div class="panel-title">
                                                    <h5>Add Notice</h5>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <form method="post">
                                                    <div class="form-group has-success">
                                                        <label for="success" class="control-label">Notice Title</label>
                                                        <div class="">
                                                            <input type="text" name="noticetitle" class="form-control" required="required" id="noticetitle">
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <label for="success" class="control-label">Notice Details</label>
                                                        <div class="">
                                                            <textarea class="form-control" name="noticedetails" required rows="5"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <div class="">
                                                            <button type="submit" name="submit" class="btn btn-success btn-labeled">Submit<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span></button>
                                                        </div>
                                                </form>
                                            </div>
                                        </div>
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

        <!-- ========== PAGE JS FILES ========== -->
        <script src="js/prism/prism.js"></script>

        <!-- ========== THEME JS ========== -->
        <script src="js/main.js"></script>




        <!-- ========== ADD custom.js FILE BELOW WITH YOUR CHANGES ========== -->

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
    </script>

    </body>

    </html>
<?php  } ?>