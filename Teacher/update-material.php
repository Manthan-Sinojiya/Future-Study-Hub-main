<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("location:../login.php");
    exit;
}

if (isset($_GET['material_id'])) {
    $material_id = intval($_GET['material_id']);
    
    // Fetch the current details of the material
    $sql = "SELECT * FROM material WHERE material_id = :material_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':material_id', $material_id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    // Check if form is submitted
    if (isset($_POST['update'])) {
        $material_name = $_POST['material_name'];
        $description = $_POST['description'];
        
        // Handle file upload
        $pdf_path = $result->pdf_path; // Default to existing path

        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
            // Check file type and size
            $allowed = array('pdf' => 'application/pdf');
            $file_name = $_FILES['pdf_file']['name'];
            $file_type = $_FILES['pdf_file']['type'];
            $file_tmp = $_FILES['pdf_file']['tmp_name'];
            $file_size = $_FILES['pdf_file']['size'];

            // Validate file type
            if (!array_key_exists(pathinfo($file_name, PATHINFO_EXTENSION), $allowed)) {
                echo '<script>alert("Error: Please select a valid PDF file.")</script>';
            } elseif ($file_size > 2000000) { // Limit file size to 2MB
                echo '<script>alert("Error: File size is larger than 2MB.")</script>';
            } else {
                // Create a unique file name and move the uploaded file
                $pdf_path = 'uploads/' . uniqid() . '-' . basename($file_name);
                move_uploaded_file($file_tmp, $pdf_path);
            }
        }

        // Update the material details
        $sql = "UPDATE material SET material_name = :material_name, description = :description, pdf_path = :pdf_path WHERE material_id = :material_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':material_name', $material_name);
        $query->bindParam(':description', $description);
        $query->bindParam(':pdf_path', $pdf_path);
        $query->bindParam(':material_id', $material_id, PDO::PARAM_INT);
        $query->execute();

        echo '<script>alert("Material updated successfully.")</script>';
        echo "<script>window.location.href ='manage-material.php'</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Material</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="css/prism/prism.css" media="screen">
    <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css" />
    <link rel="stylesheet" href="css/main.css" media="screen">

    <script src="js/modernizr/modernizr.min.js"></script>

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
                                <h2 class="title">Update Material</h2>
                            </div>
                            
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="manage-material.php">Manage Material</a></li>
                                    <li class="active">Update Material</li>
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
                                                <h5>Update Material Info</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body p-20">
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="material_name">Material Name</label>
                                                    <input type="text" name="material_name" class="form-control" value="<?php echo htmlentities($result->material_name); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Material Description</label>
                                                    <textarea name="description" class="form-control" required><?php echo htmlentities($result->description); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="pdf_path">PDF File (Upload New File)</label>
                                                    <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
                                                    <small>Current file: <a href="<?php echo htmlentities($result->pdf_path); ?>" target="_blank"><?php echo basename($result->pdf_path); ?></a></small>
                                                </div>
                                                <button type="submit" name="update" class="btn btn-primary">Update Material</button>
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

<?php } ?>
