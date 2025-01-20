<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

// Check if the user is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("location:../login.php");
    exit;
}

// For deleting the video lecture
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['video_id'])) {
    try {
        $video_id = $_GET['video_id'];

        // Delete related records in the progress table first
        $sqlProgress = "DELETE FROM progress WHERE video_id = :video_id";
        $queryProgress = $dbh->prepare($sqlProgress);
        $queryProgress->bindParam(':video_id', $video_id, PDO::PARAM_STR);
        $queryProgress->execute();

        // Now delete the video
        $sql = "DELETE FROM videos WHERE video_id = :video_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':video_id', $video_id, PDO::PARAM_STR);
        if ($query->execute()) {
            echo '<script>alert("Video lecture deleted.")</script>';
        } else {
            echo '<script>alert("Error deleting video lecture.")</script>';
        }

        echo "<script>window.location.href ='manage-video.php'</script>";
    } catch (PDOException $e) {
        echo '<script>alert("Database error: ' . $e->getMessage() . '")</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Video Lectures</title>
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
                                <h2 class="title">Manage Video Lectures</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li>Video</li>
                                    <li class="active">Manage Video Lectures</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>View Video Lecture Info</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body p-20">
                                            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Video Title</th>
                                                        <th>Description</th>
                                                        <th>Subject</th>
                                                        <th>Module</th>
                                                        <th>Topic Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Fetch the teacher_id from the session
                                                    $teacher_id = $_SESSION['teacher_id'];

                                                    // Fetch videos uploaded by the logged-in teacher
                                                    $sql = "SELECT v.video_id, v.video_title, v.description, s.sub_name, m.module_name, t.topic_name 
                                                            FROM videos v
                                                            JOIN subject s ON v.subject = s.sub_id
                                                            JOIN module m ON v.module = m.module_id
                                                            JOIN topics t ON v.topic_number = t.topic_number
                                                            WHERE v.teacher_id = :teacher_id";
                                                    $query = $dbh->prepare($sql);
                                                    $query->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    $cnt = 1;
                                                    if ($query->rowCount() > 0) {
                                                        foreach ($results as $result) { ?>
                                                            <tr>
                                                                <td><?php echo htmlentities($cnt); ?></td>
                                                                <td><?php echo htmlentities($result->video_title); ?></td>
                                                                <td><?php echo htmlentities($result->description); ?></td>
                                                                <td><?php echo htmlentities($result->sub_name); ?></td>
                                                                <td><?php echo htmlentities($result->module_name); ?></td>
                                                                <td><?php echo htmlentities($result->topic_name); ?></td>
                                                                <td>
                                                                    <a href="view-video.php?video_id=<?php echo htmlentities($result->video_id); ?>">
                                                                        <i class="fa fa-play fa-2x" title="View this Video" style="color:#259fff;"></i>
                                                                    </a>
                                                                    &nbsp;&nbsp;
                                                                    <a href="update-video.php?video_id=<?php echo htmlentities($result->video_id); ?>">
                                                                        <i class="fa fa-pencil fa-2x" title="Update this Video" style="color:#0cc100;"></i>
                                                                    </a>
                                                                    &nbsp;&nbsp;
                                                                    <a href="manage-video.php?action=delete&video_id=<?php echo htmlentities($result->video_id); ?>" onclick="return confirm('Do you really want to delete this video lecture?');">
                                                                        <i class="fa fa-trash fa-2x" title="Delete this Record" style="color:red;"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                    <?php $cnt++;
                                                        }
                                                    } ?>
                                                </tbody>
                                            </table>
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
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/iscroll/iscroll.js"></script>

    <!-- ========== PAGE JS FILES ========== -->
    <script src="js/prism/prism.js"></script>
    <script src="js/DataTables/datatables.min.js"></script>

    <!-- ========== THEME JS ========== -->
    <script src="js/main.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                "pagingType": "full_numbers", // Enable pagination with page numbers
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                }
            });
        });
    </script>
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
