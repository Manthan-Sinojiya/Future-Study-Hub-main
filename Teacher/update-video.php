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

// Fetch video details for the provided video_id
if (isset($_GET['video_id'])) {
    $video_id = $_GET['video_id'];
    $sql = "SELECT v.video_id, v.video_title, v.description, v.subject, v.module, v.topic_number, v.video_path
            FROM videos v
            WHERE v.video_id = :video_id AND v.teacher_id = :teacher_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':video_id', $video_id, PDO::PARAM_STR);
    $query->bindParam(':teacher_id', $_SESSION['teacher_id'], PDO::PARAM_INT);
    $query->execute();
    $video = $query->fetch(PDO::FETCH_OBJ);

    // If video does not exist, redirect to manage-video.php
    if (!$video) {
        header("Location: manage-video.php");
        exit;
    }
}

// Fetch all available modules
$sqlModules = "SELECT module_id, module_name FROM module";
$queryModules = $dbh->prepare($sqlModules);
$queryModules->execute();
$modules = $queryModules->fetchAll(PDO::FETCH_OBJ);

// Fetch all available subjects
$sqlSubjects = "SELECT sub_id, sub_name FROM subject";
$querySubjects = $dbh->prepare($sqlSubjects);
$querySubjects->execute();
$subjects = $querySubjects->fetchAll(PDO::FETCH_OBJ);

// Fetch all available topics
$sqlTopics = "SELECT topic_number, topic_name FROM topics";
$queryTopics = $dbh->prepare($sqlTopics);
$queryTopics->execute();
$topics = $queryTopics->fetchAll(PDO::FETCH_OBJ);


// Handle video update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $video_title = $_POST['video_title'];
    $description = $_POST['description'];
    $subject = $_POST['subject'];
    $module = $_POST['module']; // This will now capture the selected module ID
    $topic_number = $_POST['topic_number'];
    $video_path = $video->video_path; // Keep the old video path by default

    // Check if a new video file was uploaded
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == UPLOAD_ERR_OK) {
        // Define upload directory and file path
        $upload_dir = 'uploads/videos/';
        $file_name = basename($_FILES['video_file']['name']);
        $target_file = $upload_dir . uniqid() . '-' . $file_name; // Use unique file name

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
            $video_path = $target_file; // Update the video path to the new file
        } else {
            echo '<script>alert("Error uploading the video file.")</script>';
        }
    }

    try {
        $sqlUpdate = "UPDATE videos SET video_title = :video_title, description = :description, subject = :subject, module = :module, topic_number = :topic_number, video_path = :video_path WHERE video_id = :video_id";
        $queryUpdate = $dbh->prepare($sqlUpdate);
        $queryUpdate->bindParam(':video_title', $video_title, PDO::PARAM_STR);
        $queryUpdate->bindParam(':description', $description, PDO::PARAM_STR);
        $queryUpdate->bindParam(':subject', $subject, PDO::PARAM_STR);
        $queryUpdate->bindParam(':module', $module, PDO::PARAM_STR); // Save the module ID
        $queryUpdate->bindParam(':topic_number', $topic_number, PDO::PARAM_STR);
        $queryUpdate->bindParam(':video_path', $video_path, PDO::PARAM_STR);
        $queryUpdate->bindParam(':video_id', $video_id, PDO::PARAM_STR);

        if ($queryUpdate->execute()) {
            echo '<script>alert("Video lecture updated successfully.")</script>';
            echo "<script>window.location.href ='manage-video.php'</script>";
        } else {
            echo '<script>alert("Error updating video lecture.")</script>';
        }
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
    <title>Update Video Lecture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="css/main.css" media="screen">
    <style>
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
                                <h2 class="title">Update Video Lecture</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="#">Videos</a></li>
                                    <li><a href="manage-video.php">Manage Video</a></li>
                                    <li class="active">Update Video</li>
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
                                                <h5>Edit Video Lecture Info</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body p-20">
                                            <form method="POST" action="" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="video_title">Video Title</label>
                                                    <input type="text" class="form-control" name="video_title" id="video_title" value="<?php echo htmlentities($video->video_title); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea class="form-control" name="description" id="description" rows="4" required><?php echo htmlentities($video->description); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="subject">Subject</label>
                                                    <select name="subject" id="subject" class="form-control" required>
                                                        <option value="">Select Subject</option>
                                                        <?php foreach ($subjects as $subject): ?>
                                                            <option value="<?php echo $subject->sub_id; ?>" <?php echo ($subject->sub_id == $video->subject) ? 'selected' : ''; ?>>
                                                                <?php echo htmlentities($subject->sub_name); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="module">Module</label>
                                                    <select name="module" id="module" class="form-control" required>
                                                        <option value="">Select Module</option>
                                                        <?php foreach ($modules as $module): ?>
                                                            <option value="<?php echo $module->module_id; ?>" <?php echo ($module->module_id == $video->module) ? 'selected' : ''; ?>>
                                                                <?php echo htmlentities($module->module_name); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="topic_number">Topic</label>
                                                    <select name="topic_number" id="topic_number" class="form-control" required>
                                                        <option value="">Select Topic</option>
                                                        <?php foreach ($topics as $topic): ?>
                                                            <option value="<?php echo $topic->topic_number; ?>" <?php echo ($topic->topic_number == $video->topic_number) ? 'selected' : ''; ?>>
                                                                <?php echo htmlentities($topic->topic_name); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="video_file">Upload New Video (Optional)</label>
                                                    <input type="file" class="form-control" name="video_file" id="video_file" accept="video/*">
                                                </div>
                                                <div class="form-group">
                                                    <label>Current Video:</label><br>
                                                    <?php if (!empty($video->video_path)) : ?>
                                                        <p>Current Video: <?php echo htmlentities(basename($video->video_path)); ?></p>
                                                        <p><a href="<?php echo htmlentities($video->video_path); ?>" target="_blank">View Current Video</a></p>
                                                    <?php else: ?>
                                                        <p>No video uploaded yet.</p>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Update Video Lecture</button>
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