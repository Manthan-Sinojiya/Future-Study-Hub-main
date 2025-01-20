<?php
session_start();
include './includes/config.php';

$error = "";
$msg = "";

// Check if teacher_id is set in the session
if (!isset($_SESSION['teacher_id'])) {
    echo "Teacher ID not set in session.";
    exit;
}

$teacher_id = $_SESSION['teacher_id'];

// Function to get topic_number by topic_name from topics table
function getTopicNumberByName(PDO $dbh, $topicName) {
    $sql = "SELECT topic_number FROM topics WHERE topic_name = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$topicName]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['topic_number'] : null;
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve form data
    $video_title = $_POST["video_title"];
    $description = $_POST["description"];
    $subject = $_POST["subject"];
    $module = $_POST["module"];
    $topic_name = $_POST["topic_name"]; // Retrieve topic_name from form

    // Validate video title for special characters
    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $video_title)) {
        $error = "Sorry, the video title cannot contain special characters like , or '.";
    } else {
        $target_dir = "uploads/";
        $video_path = $target_dir . basename($_FILES["video_file"]["name"]);
        $videoFileType = strtolower(pathinfo($video_path, PATHINFO_EXTENSION));

        // Check if the file is a valid video format
        $allowedExtensions = ['mp4', 'avi', 'mkv', 'mov'];
        if (!in_array($videoFileType, $allowedExtensions)) {
            $error = "Sorry, only MP4, AVI, MKV, and MOV files are allowed.";
        } else {
            // Check the file size (500MB = 500 * 1024 * 1024 bytes)
            $maxFileSize = 200 * 1024 * 1024;
            if ($_FILES["video_file"]["size"] > $maxFileSize) {
                $error = "Sorry, the video size cannot exceed 200MB.";
            } else {
                // Ensure $dbh is defined and not null (Database Connection)
                if (isset($dbh) && $dbh !== null) {
                    // Get topic_number from topics table based on topic_name
                    $topic_number = getTopicNumberByName($dbh, $topic_name);

                    if ($topic_number === null) {
                        $error = "Invalid topic selected.";
                    } else {
                        // Move the uploaded file to the specified directory
                        if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $video_path)) {
                            // Insert video details into the database
                            $sql = "INSERT INTO videos (video_title, description, subject, module, topic_number, video_path, teacher_id) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmt = $dbh->prepare($sql);
                            if ($stmt) {
                                $stmt->bindParam(1, $video_title);
                                $stmt->bindParam(2, $description);
                                $stmt->bindParam(3, $subject);
                                $stmt->bindParam(4, $module);
                                $stmt->bindParam(5, $topic_number); // Bind topic_number fetched from database
                                $stmt->bindParam(6, $video_path);
                                $stmt->bindParam(7, $teacher_id);
                                if ($stmt->execute()) {
                                    $msg = "Video added successfully.";
                                    header("location: ./manage-video.php");
                                    exit;
                                } else {
                                    $error = "Error inserting video into database.";
                                }
                            } else {
                                $error = "Error preparing SQL statement.";
                            }
                        } else {
                            $error = "Sorry, there was an error uploading your file.";
                        }
                    }
                } else {
                    $error = "Database connection error.";
                }
            }
        }
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Video</title>
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
                                <h2 class="title">Add Video</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="#">Videos</a></li>
                                    <li class="active">Add Video</li>
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
                                                <h5>Add Video</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <?php if ($msg) { ?>
                                                <div class="alert alert-success left-icon-alert" role="alert">
                                                    <strong>Well done!</strong><?php echo htmlentities($msg); ?>
                                                </div>
                                            <?php } else if ($error) { ?>
                                                <div class="alert alert-danger left-icon-alert" role="alert">
                                                    <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                                </div>
                                            <?php } ?>
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="form-group has-success">
                                                    <label for="video_title" class="control-label">Video Title:</label>
                                                    <div class="">
                                                        <input type="text" name="video_title" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="description" class="control-label">Description:</label>
                                                    <div class="">
                                                        <textarea name="description" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="video_file" class="control-label">Upload Video:</label>
                                                    <div class="">
                                                        <input type="file" name="video_file" accept="video/*" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="subject" class="control-label">Subject:</label>
                                                    <div class="">
                                                        <select name="subject" class="form-control" id="default" required="required">
                                                            <option value="">Select Subject</option>
                                                            <?php
                                                            $sql = "SELECT * from subject";
                                                            $query = $dbh->prepare($sql);
                                                            $query->execute();
                                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                            if ($query->rowCount() > 0) {
                                                                foreach ($results as $result) { ?>
                                                                    <option value="<?php echo htmlentities($result->sub_id); ?>"><?php echo htmlentities($result->sub_name); ?></option>
                                                            <?php }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="module" class="control-label">Module</label>
                                                    <div class="">
                                                        <select name="module" class="form-control" required>
                                                            <option value="">Select Module</option>
                                                            <?php
                                                            $sql = "SELECT * FROM module";
                                                            $query = $dbh->prepare($sql);
                                                            $query->execute();
                                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                            if ($query->rowCount() > 0) {
                                                                foreach ($results as $result) { ?>
                                                                    <option value="<?php echo htmlentities($result->module_id); ?>"><?php echo htmlentities($result->module_name); ?></option>
                                                            <?php }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <label for="topic_name" class="control-label">Topic:</label>
                                                    <div class="">
                                                        <select name="topic_name" class="form-control" required>
                                                            <option value="">Select Topic</option>
                                                            <?php
                                                            $sql = "SELECT * from topics";
                                                            $query = $dbh->prepare($sql);
                                                            $query->execute();
                                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                            if ($query->rowCount() > 0) {
                                                                foreach ($results as $result) { ?>
                                                                    <option value="<?php echo htmlentities($result->topic_name); ?>"><?php echo htmlentities($result->topic_name); ?></option>
                                                            <?php }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group has-success">
                                                    <div class="">
                                                        <button type="submit" name="submit" class="btn btn-success btn-labeled">Submit<span class="btn-label btn-label-right"><i class="fa fa-check"></i></span></button>
                                                    </div>
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
