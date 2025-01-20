<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
} else {
    if (isset($_POST['submit'])) {
        $courseName = $_POST['coursename'];
        $moduleId = $_POST['moduleid'];
        $year = $_POST['year'];
        $month = $_POST['month'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        $sql = "INSERT INTO courses(course_name, module_id, year, month, price, description) VALUES(:courseName, :moduleId, :year, :month, :price, :description)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':courseName', $courseName, PDO::PARAM_STR);
        $query->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
        $query->bindParam(':year', $year, PDO::PARAM_INT);
        $query->bindParam(':month', $month, PDO::PARAM_STR);
        $query->bindParam(':price', $price, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            echo '<script>alert("Course added successfully")</script>';
            echo "<script>window.location.href ='manage-courses.php'</script>";
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    }

    // Fetch modules
    $modulesQuery = $dbh->query("SELECT module_id, module_name FROM module");
    $modules = $modulesQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin | Add Course</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
                        <h3 class="page-title">Add Course</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="manage-courses.php">Courses</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Course</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Add Course</h4>
                                    <p class="card-description"> Enter course details below </p>
                                    <form class="forms-sample" method="post">
                                        <div class="form-group">
                                            <label for="coursename">Course Name</label>
                                            <input type="text" name="coursename" class="form-control" id="coursename" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="moduleid">Module</label>
                                            <select name="moduleid" class="form-control form-control-sm" id="moduleid" required>
                                                <option value="">Select Module</option>
                                                <?php foreach ($modules as $module) { ?>
                                                    <option value="<?php echo $module['module_id']; ?>"><?php echo $module['module_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="year">Year</label>
                                            <input type="text" name="year" class="form-control" id="year" value="<?php echo date('Y'); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="month">Month</label>
                                            <select name="month" class="form-control form-control-sm" id="month" required>
                                                <option value="">Select Month</option>
                                                <?php
                                                $months = [
                                                    'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 
                                                    'September', 'October', 'November', 'December'
                                                ];
                                                foreach ($months as $month) {
                                                    echo "<option value=\"$month\">$month</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-gradient-primary text-white">₹</span>
                                                </div>
                                                <input type="text" name="price" class="form-control" id="price" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" name="description" id="description" rows="4"></textarea>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                        <button class="btn btn-light">Cancel</button>
                                    </form>
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
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
</body>
</html>
<?php } ?>
