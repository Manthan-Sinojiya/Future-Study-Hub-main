<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');

// Redirect to login if user is not a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("location:../login.php");
    exit;
}

$msg = '';
$error = '';

// Process form submission to submit suggestions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['studentId']) && isset($_POST['suggestion'])) {
        $studentId = $_POST['studentId'];
        $teacherId = $_SESSION['teacher_id'];
        $suggestion = $_POST['suggestion'];

        // Insert suggestion into database
        $sql = "INSERT INTO teacher_suggestions (student_user_id, teacher_user_id, suggestion) 
                VALUES (:studentId, :teacherId, :suggestion)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentId', $studentId, PDO::PARAM_INT);
        $query->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
        $query->bindParam(':suggestion', $suggestion, PDO::PARAM_STR);

        if ($query->execute()) {
            $msg = "Suggestion submitted successfully.";
        } else {
            $error = "Error submitting suggestion.";
        }
    } else {
        $error = "Invalid request.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teacher | Student Progress</title>
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

        <!-- ========== TOP NAVBAR ========== -->
        <?php include('includes/topbar.php'); ?>
        <!-- ========== WRAPPER FOR BOTH SIDEBARS & MAIN CONTENT ========== -->
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('includes/leftbar.php'); ?>

                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Student Progress</h2>
                            </div>
                        </div>
                        <!-- /.row -->
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li> Students</li>
                                    <li class="active">Student Progress</li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>View Student Progress</h5>
                                            </div>
                                        </div>
                                        <?php if ($msg) { ?>
                                            <div class="alert alert-success left-icon-alert" role="alert">
                                                <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                            </div>
                                        <?php } else if ($error) { ?>
                                            <div class="alert alert-danger left-icon-alert" role="alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>
                                        <div class="panel-body p-20">
                                            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Student Name</th>
                                                        <th>Video Title</th>
                                                        <th>Watched Duration</th>
                                                        <th>Last Watched</th>
                                                        <th>Action</th> <!-- Added action column -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Updated query to join 'student_details' and 'users' tables
                                                    $sql = "SELECT sd.student_id, u.name AS student_name, v.video_title, p.watched_duration, p.last_watched
                                                            FROM progress p
                                                            INNER JOIN student_details sd ON p.student_id = sd.student_id
                                                            INNER JOIN users u ON sd.user_id = u.user_id
                                                            INNER JOIN videos v ON p.video_id = v.video_id";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    $cnt = 1;
                                                    foreach ($results as $result) { ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td><?php echo htmlentities($result->student_name); ?></td>
                                                            <td><?php echo htmlentities($result->video_title); ?></td>
                                                            <td><?php echo htmlentities($result->watched_duration); ?></td>
                                                            <td><?php echo htmlentities($result->last_watched); ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary btn-sm suggestion-btn"
                                                                    data-student-id="<?php echo $result->student_id; ?>"
                                                                    data-video-title="<?php echo htmlentities($result->video_title); ?>"
                                                                    data-toggle="modal" data-target="#suggestionModal">
                                                                    Suggestions
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php $cnt = $cnt + 1;
                                                            }
                                                         ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col-md-12 -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container-fluid -->
                    </section>
                    <!-- /.section -->
                </div>
                <!-- /.main-page -->
            </div>
            <!-- /.content-container -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /.main-wrapper -->

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

    <!-- ========== SCRIPT TO HANDLE MODAL AND FORM SUBMISSION ========== -->
    <script>
        $(function () {
            // Handle click on Suggestions button
            $('.suggestion-btn').click(function () {
                var studentId = $(this).data('student-id');
                var videoTitle = $(this).data('video-title');

                $('#studentId').val(studentId);
                $('#videoTitle').val(videoTitle);
            });

            // Handle form submission via AJAX
            $('#suggestionForm').submit(function (event) {
                event.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'submit_suggestion.php',
                    data: formData,
                    success: function (response) {
                        $('#suggestionModal').modal('hide');
                        alert(response); // Alert success or error message
                        location.reload(); // Reload page to update suggestions
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error submitting suggestion.');
                    }
                });
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
   <!-- Modal for Suggestions -->
<div id="suggestionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="suggestionForm" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Suggestions</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="studentId" class="control-label">Student ID:</label>
                        <input type="text" class="form-control" id="studentId" name="studentId" readonly>
                    </div>
                    <div class="form-group">
                        <label for="videoTitle" class="control-label">Video Title:</label>
                        <input type="text" class="form-control" id="videoTitle" name="videoTitle" readonly>
                    </div>
                    <div class="form-group">
                        <label for="suggestion" class="control-label">Your Suggestions:</label>
                        <textarea class="form-control" id="suggestion" name="suggestion" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
       
