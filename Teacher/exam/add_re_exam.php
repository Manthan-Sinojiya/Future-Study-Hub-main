<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the file containing the database connection
include '../includes/db_connection.php';

// Initialize $msg variable to store messages
$msg = '';

// Check if the database connection is valid
if ($conn) {
    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Extract form data
        $quiz_id = $_POST["quiz_id"];
        $exam_time = $_POST["exam_time"];

        // Check if quiz_id is provided in the POST request
        if (!empty($quiz_id)) {
            // Prepare the SQL statement to fetch quiz details including total questions
            $sql_quiz = "SELECT total FROM quiz WHERE quiz_id = ?";
            $stmt_quiz = $conn->prepare($sql_quiz);
            $stmt_quiz->bind_param("i", $quiz_id);
            $stmt_quiz->execute();
            $result_quiz = $stmt_quiz->get_result();

            // Fetch the total number of questions from the database
            if ($row_quiz = $result_quiz->fetch_assoc()) {
                $total = $row_quiz['total'];

                // Loop through the total number of questions
                for ($i = 1; $i <= $total; $i++) {
                    // Extract form data for each question
                    $question = $_POST["question"][$i] ?? ''; // Use null coalescing operator to handle undefined array keys
                    $options = $_POST["options"][$i] ?? []; // Use null coalescing operator to handle undefined array keys
                    $correct_answer = $_POST["correct_answer"][$i] ?? ''; // Use null coalescing operator to handle undefined array keys

                    // Assign array elements to variables
                    $title = "Question $i"; // Default title value
                    // $img_path = ''; // This is missing from your code, make sure to provide the img_path value
                    $text = ''; // This is missing from your code, make sure to provide the text value
                    $option1 = $options[0] ?? '';
                    $option2 = $options[1] ?? '';
                    $option3 = $options[2] ?? '';
                    $option4 = $options[3] ?? '';

                    // Image upload handling
                    $image_path = ''; // Initialize image path variable

                    // Check if an image file is uploaded
                    if (!empty($_FILES['image']['tmp_name'][$i])) {
                        // Create the uploads/images directory if it doesn't exist
                        $uploads_dir = '../uploads/images/';
                        if (!is_dir($uploads_dir)) {
                            mkdir($uploads_dir, 0777, true);
                        }

                        // Move the uploaded file to the specified directory
                        $file_name = basename($_FILES["image"]["name"][$i]);
                        $target_file = $uploads_dir . $file_name;
                        if (move_uploaded_file($_FILES["image"]["tmp_name"][$i], $target_file)) {
                            $image_path = $target_file;

                            // Prepare the SQL statement to insert quiz questions
                            // Prepare the SQL statement to insert quiz questions
                            $sql_insert = "INSERT INTO re_quiz (question, text, option1, option2, option3, option4, correct_answer, quiz_id, img_path, exam_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                            // Prepare the statement and check for errors
                            if ($stmt_insert = $conn->prepare($sql_insert)) {
                                // Bind parameters and execute the query for each question
                                $stmt_insert->bind_param('ssssssssss', $question, $text, $option1, $option2, $option3, $option4, $correct_answer, $quiz_id, $image_path, $exam_time);

                                if ($stmt_insert->execute()) {
                                    // Update successful
                                    $msg = "Questions added successfully!";
                                    header("location: ../dashboard.php");
                                    exit();
                                } else {
                                    // Error during execution
                                    $msg = "Error adding questions: " . $stmt_insert->error;
                                }
                            } else {
                                // Error preparing the statement
                                $msg = "Error preparing statement: " . $conn->error;
                            }   
                        } else {
                            $msg = "Error uploading image.";
                        }
                    } else {
                        $msg = "No image file uploaded.";
                    }
                }
            } else {
                $msg = "No questions found for this quiz.";
            }
        } else {
            $msg = "Quiz ID is missing.";
        }
    } else {
        // Fetch total number of questions for the quiz
        if (isset($_GET['quiz_id'])) {
            $quiz_id = $_GET['quiz_id'];
            $sql_total = "SELECT total FROM quiz WHERE quiz_id = ?";
            $stmt_total = $conn->prepare($sql_total);
            $stmt_total->bind_param("i", $quiz_id);
            $stmt_total->execute();
            $result_total = $stmt_total->get_result();

            if ($row_total = $result_total->fetch_assoc()) {
                $total = $row_total['total'];
            } else {
                $msg = "No questions found for this quiz.";
            }
        } else {
            $msg = "Quiz ID is missing.";
        }
    }
} else {
    $msg = "Error connecting to the database: " . mysqli_connect_error();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Exam Question</title>
    <link rel="stylesheet" href="../css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="../css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="../css/animate-css/animate.min.css" media="screen">
    <link rel="stylesheet" href="../css/lobipanel/lobipanel.min.css" media="screen">
    <link rel="stylesheet" href="../css/main.css" media="screen">
    <script src="../js/modernizr/modernizr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("input[name^='options']").on("keyup", function() {
                var questionIndex = $(this).attr('name').match(/\[(.*?)\]/)[1];
                var options = [
                    $("input[name='options[" + questionIndex + "][]']").eq(0).val(),
                    $("input[name='options[" + questionIndex + "][]']").eq(1).val(),
                    $("input[name='options[" + questionIndex + "][]']").eq(2).val(),
                    $("input[name='options[" + questionIndex + "][]']").eq(3).val()
                ];
                var selectOptions = '';
                options.forEach(function(option) {
                    selectOptions += '<option value="' + option + '">' + option + '</option>';
                });
                $("#correct_answer" + questionIndex).html(selectOptions);
            });
        });
    </script>
</head>

<body class="top-navbar-fixed">
    <div class="main-wrapper">
        <?php include('../includes/topbar.php'); ?>
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('../includes/leftbar.php'); ?>
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Add Quiz Question</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="../dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li><a href="../quiz_details.php">Quiz</a></li>
                                    <li class="active">Add Quiz Question</li>
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
                                                <h5>Add Quiz Question</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <?php if (!empty($msg)) { ?>
                                                <div class="alert alert-danger" role="alert">
                                                    <?php echo $msg; ?>
                                                </div>
                                            <?php } ?>
                                            <form method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="quiz_id" value="<?php echo isset($_GET['quiz_id']) ? $_GET['quiz_id'] : ''; ?>">
                                                <div class="form-group">
                                                    <label for="exam_time" class="control-label">Exam Timing (in minutes):</label>
                                                    <input type="number" name="exam_time" class="form-control" required>
                                                </div>
                                                <?php if ($total > 0) { ?>
                                                    <?php for ($i = 1; $i <= $total; $i++) { ?>
                                                        <div class="form-group">
                                                            <label for="name" class="control-label">Question <?php echo $i; ?>:</label>
                                                            <input type="text" name="question[<?php echo $i; ?>]" class="form-control" required>
                                                            <label for="options" class="control-label">Options:</label>
                                                            <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 1" required>
                                                            <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 2" required>
                                                            <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 3" required>
                                                            <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 4" required>
                                                            <label for="image" class="control-label">Upload Image:</label>
                                                            <input type="file" name="image[<?php echo $i; ?>]" class="form-control-file">
                                                            <div class="form-group has-success">
                                                                <label for="correct_answer<?php echo $i; ?>" class="control-label">Correct Answer:</label>
                                                                <select name="correct_answer[<?php echo $i; ?>]" id="correct_answer<?php echo $i; ?>" class="form-control" required>
                                                                    <!-- Options will be generated dynamically using JavaScript -->
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p>No questions found for this quiz.</p>
                                                <?php } ?>
                                                <div class="form-group">
                                                    <button type="submit" name="submit" class="btn btn-success">Submit</button>
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
    <script src="../js/jquery/jquery-2.2.4.min.js"></script>
    <script src="../js/bootstrap/bootstrap.min.js"></script>
    <script src="../js/pace/pace.min.js"></script>
    <script src="../js/lobipanel/lobipanel.min.js"></script>
    <script src="../js/iscroll/iscroll.js"></script>

    <!-- ========== PAGE JS FILES ========== -->
    <script src="../js/prism/prism.js"></script>
    <script src="../js/DataTables/datatables.min.js"></script>

    <!-- ========== THEME JS ========== -->
    <script src="../js/main.js"></script>
    <script>
        $(function($) {
            $('#example').DataTable();

            $('#example2').DataTable({
                "scrollY": "300px",
                "scrollCollapse": true,
                "paging": false
            });

            $('#example3').DataTable();
        });
    </script>
</body>

</html>