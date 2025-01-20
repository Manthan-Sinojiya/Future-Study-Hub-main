<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/db_connection.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("location:../login.php");
    exit;
} else {
    $error = ""; // Initialize error variable

    if (isset($_POST['submit'])) {
        $material_name = $_POST["material_name"];
        $description = $_POST["description"];
        $sub_name = $_POST["course_field"];

        // Check if the file name contains invalid characters
        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $material_name)) {
            $error = "Sorry, the material name cannot contain special characters like , or '.";
        } else {
            $target_dir = "uploads/pdf/";
            $pdf_path = $target_dir . basename($_FILES["pdf_file"]["name"]);
            $pdfFileType = strtolower(pathinfo($pdf_path, PATHINFO_EXTENSION));
            $fileSize = $_FILES["pdf_file"]["size"]; // Get file size in bytes
            $maxFileSize = 20 * 1024 * 1024; // 20MB in bytes

            // Check if the file is a PDF
            if ($pdfFileType != "pdf") {
                $error = "Sorry, only PDF files are allowed.";
            } 
            // Check if the file exceeds the maximum size
            elseif ($fileSize > $maxFileSize) {
                $error = "Sorry, the file size must not exceed 20MB.";
            } 
            else {
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $pdf_path)) {
                    // Insert material details into the database
                    $sql = "INSERT INTO material (material_id, material_name, description, sub_id, pdf_path) VALUES (NULL, ?, ?, (SELECT sub_id FROM subject WHERE sub_name = ?), ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $material_name, $description, $sub_name, $pdf_path);

                    if ($stmt->execute()) {
                        $msg = "Material added successfully.";
                        // Debugging: Add a message to see if the material is added
                        echo "Material added to the database.";
                        header("location:./manage-material.php");
                        exit;
                    } else {
                        $error = "Error: " . $sql . "<br>" . $conn->error;
                    }
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            }
        }
    }

?>


    <html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Add Material</title>

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
                                    <h2 class="title">Add Material</h2>
                                </div>
                            </div>
                            <div class="row breadcrumb-div">
                                <div class="col-md-6">
                                    <ul class="breadcrumb">
                                        <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                        <li><a href="#">Material</a></li>
                                        <li class="active">Add Material</li>
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
                                                    <h5>Add Material</h5>
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
                                                        <label for="material_name" class="control-label">Material Name:</label>
                                                        <div class="">
                                                            <input type="text" name="material_name" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <label for="description" class="control-label">Description:</label>
                                                        <div class="">
                                                            <textarea name="description" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <label for="pdf_file" class="control-label">Upload PDF:</label>
                                                        <div class="">
                                                            <input type="file" name="pdf_file" accept=".pdf" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group has-success">
                                                        <label for="description" class="control-label">Subject</label>
                                                        <div class="">
                                                            <select name="course_field" class="form-control" id="default" required="required">
                                                                <option value="">Select Subject</option>
                                                                <?php
                                                                $sql = "SELECT * from subject";
                                                                $query = $dbh->prepare($sql);
                                                                $query->execute();
                                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                                if ($query->rowCount() > 0) {
                                                                    foreach ($results as $result) { ?>
                                                                        <option value="<?php echo htmlentities($result->sub_name); ?>"><?php echo htmlentities($result->sub_name); ?></option>
                                                                <?php }
                                                                } ?>
                                                            </select>
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



        <!-- ========== ADD custom.js FILE BELOW WITH YOUR CHANGES ========== -->
    </body>

    </html>
<?php  } ?>