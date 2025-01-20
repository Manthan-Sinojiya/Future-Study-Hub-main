<?php
session_start();
error_reporting(1);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
}

// Handle deletion of a course
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $checkSql = "SELECT COUNT(*) FROM invoices WHERE course_id = :id";
    $checkQuery = $dbh->prepare($checkSql);
    $checkQuery->bindParam(':id', $id, PDO::PARAM_INT);
    $checkQuery->execute();
    $relatedRecords = $checkQuery->fetchColumn();

    if ($relatedRecords > 0) {
        echo '<script>alert("Cannot delete this course because it has related invoices.")</script>';
    } else {
        $sql = "DELETE FROM courses WHERE course_id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        echo '<script>alert("Course deleted.")</script>';
    }
    echo "<script>window.location.href ='manage-courses.php'</script>";
}

// Fetch courses data
$sql = "SELECT c.course_id, c.course_name, m.module_name, c.year, c.month, c.price, c.description 
        FROM courses c 
        JOIN module m ON c.module_id = m.module_id";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Manage Courses</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container-scroller">
        <?php include('includes/topbar.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include('includes/leftbar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h3 class="page-title d-flex align-items-center">
                            <span class="page-title-icon bg-gradient-primary text-white me-2">
                                <i class="mdi mdi-book-open-variant"></i>
                            </span>
                            Manage Courses
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Manage Courses</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- <div class="row mb-4">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Show List Courses</h4>
                            <form method="POST" class="d-flex">
                                <input type="text" name="searchTerm" class="form-control me-2" 
                                       placeholder="Search courses..." value="<?= htmlentities($searchTerm); ?>">
                                <button class="btn btn-primary" type="submit" name="search">Search</button>
                            </form>
                        </div>
                    </div> -->

                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-lg-12 d-flex justify-content-between align-items-center">
                                            <h4 class="card-title">Course Information</h4>
                                            <!-- <form method="GET" class="d-flex align-items-center">
                                                <label for="limit" class="me-2 mb-0">Records per page:</label>
                                                <select name="limit" id="limit" class="form-select me-2" 
                                                        style="width: auto;" onchange="this.form.submit()">
                                                    <?php foreach ($limitOptions as $option): ?>
                                                        <option value="<?= $option; ?>" <?= $option == $limit ? 'selected' : ''; ?>>
                                                            <?= $option; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="hidden" name="searchTerm" value="<?= htmlentities($searchTerm); ?>">
                                                <input type="hidden" name="page" value="<?= $page; ?>">
                                            </form> -->
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="courseTable" class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Course Name</th>
                                                    <th>Module</th>
                                                    <th>Year</th>
                                                    <th>Month</th>
                                                    <th>Price</th>
                                                    <th>Description</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $cnt = 1;
                                                if ($query->rowCount() > 0) {
                                                    foreach ($results as $result) { ?>
                                                        <tr>
                                                            <td><?= htmlentities($cnt); ?></td>
                                                            <td><?= htmlentities($result->course_name); ?></td>
                                                            <td><?= htmlentities($result->module_name); ?></td>
                                                            <td><?= htmlentities($result->year); ?></td>
                                                            <td><?= htmlentities($result->month); ?></td>
                                                            <td><?= htmlentities($result->price); ?></td>
                                                            <td><?= htmlentities($result->description); ?></td>
                                                            <td>
                                                                <a href="manage-courses.php?id=<?= htmlentities($result->course_id); ?>" 
                                                                   onclick="return confirm('Do you really want to delete this course?');">
                                                                    <i class="mdi mdi-delete icon-md text-danger" title="Delete this Course"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php $cnt++;
                                                    }
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center">No courses found.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="courseTable-pagination-controls" 
                                         class="pagination-controls mt-3 d-flex justify-content-end"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include('includes/footer.php'); ?>
            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/tableUtility.js"></script>
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/dashboard.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new TableUtility({
                tableId: 'courseTable',
                rowsPerPage: 10,
                searchPlaceholder: 'Search courses...'
            });
        });
    </script>
</body>
</html>
