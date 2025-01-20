<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("location:../login.php");
    exit;
}

// Handle deletion of a notice
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM notice WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_STR);
    $query->execute();
    echo '<script>alert("Notice deleted.")</script>';
    echo "<script>window.location.href ='manage-notices.php'</script>";
}

// Fetch notices
$sql = "SELECT * FROM notice";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Manage Notices</title>

    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        .table th, .table td {
            white-space: normal;
            word-wrap: break-word;
            padding: 8px;
            vertical-align: middle;
        }
        .table td { font-size: 14px; }
        .table th:nth-child(1), .table td:nth-child(1) { width: 5%; }
        .table th:nth-child(2), .table td:nth-child(2),
        .table th:nth-child(3), .table td:nth-child(3) { max-width: 150px; }
    </style>
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
                            <i class="mdi mdi-bell-outline"></i>
                        </span>
                        Manage Notices
                    </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Notices</li>
                        </ol>
                    </nav>
                </div>

                <!-- <div class="row mb-4">
                    <div class="col-lg-12 d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Show List Notices</h4>
                        <form method="POST" class="d-flex">
                            <input type="text" name="searchTerm" class="form-control me-2" 
                                   placeholder="Search notices..." value="<?= htmlentities($searchTerm); ?>">
                            <button class="btn btn-primary" type="submit" name="search">Search</button>
                        </form>
                    </div>
                </div> -->

                <div class="row">
                    <div class="col-lg-12 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Notices Information</h4>
                                    <!-- <form method="GET" class="d-flex align-items-center">
                                        <label for="limit" class="me-2 mb-0">Records per page:</label>
                                        <select name="limit" id="limit" class="form-select" 
                                                style="width: auto;" onchange="this.form.submit()">
                                            <?php foreach ([1, 5, 10, 15, 20] as $option): ?>
                                                <option value="<?= $option; ?>" <?= $option == $limit ? 'selected' : ''; ?>>
                                                    <?= $option; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="searchTerm" value="<?= htmlentities($searchTerm); ?>">
                                        <input type="hidden" name="page" value="<?= $page; ?>">
                                    </form> -->
                                </div>

                                <div class="table-responsive">
                                    <table id="noticeTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Notice Title</th>
                                                <th>Notice Details</th>
                                                <th>Creation Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <tr>
                                                        <td><?= htmlentities($cnt); ?></td>
                                                        <td><?= htmlentities($result->noticeTitle); ?></td>
                                                        <td><?= htmlentities($result->noticeDetails); ?></td>
                                                        <td><?= htmlentities($result->Date); ?></td>
                                                        <td>
                                                            <a href="manage-notices.php?id=<?= htmlentities($result->id); ?>" 
                                                               onclick="return confirm('Do you really want to delete this notice?');">
                                                                <i class="mdi mdi-delete icon-md text-danger"
                                                                   title="Delete this Record"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php $cnt++;
                                                }
                                            } else { ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No notices found.</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="noticeTable-pagination-controls" 
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

<script src="assets/vendors/js/vendor.bundle.base.js"></script>
<script src="assets/js/tableUtility.js"></script>
<script src="assets/js/misc.js"></script>
<script src="assets/js/dashboard.js"></script>
<script src="assets/js/off-canvas.js"></script>
<script src="assets/js/hoverable-collapse.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        new TableUtility({
            tableId: 'noticeTable',
            rowsPerPage: 10,
            searchPlaceholder: 'Search notices...'
        });
    });
</script>

</body>
</html>
