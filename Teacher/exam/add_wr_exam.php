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
        $answer_type = $_POST["answer_type"];

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

                // Prepare the SQL statement to insert quiz questions
                $sql_insert = "INSERT INTO wr_quiz (question, option1, option2, option3, option4, correct_answer, quiz_id, exam_time, answer_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                // Loop through the total number of questions
                for ($i = 1; $i <= $total; $i++) {
                    // Extract form data for each question
                    $question = $_POST["question"][$i] ?? '';
                    $options = $_POST["options"][$i] ?? [];
                    $correct_answer = $_POST["correct_answer"][$i] ?? '';

                    // Assign array elements to variables
                    $option1 = $options[0] ?? '';
                    $option2 = $options[1] ?? '';
                    $option3 = $options[2] ?? '';
                    $option4 = $options[3] ?? '';

                    // Handle the case for text answer
                    if ($answer_type === 'text') {
                        // Get the text answer from the input
                        $correct_answer = $_POST["correct_answer_text"][$i] ?? ''; // Make sure this corresponds to the text input
                        // Set options to null for text answers
                        $option1 = $option2 = $option3 = $option4 = ''; // No options for text answers
                    } else {
                        $correct_answer = $_POST["correct_answer"][$i] ?? ''; // This is for multiple choice
                        // Assign values for options for multiple choice
                        $option1 = $options[0] ?? '';
                        $option2 = $options[1] ?? '';
                        $option3 = $options[2] ?? '';
                        $option4 = $options[3] ?? '';
                    }

                    // Bind parameters
                    $stmt_insert->bind_param('sssssisss', $question, $option1, $option2, $option3, $option4, $correct_answer, $quiz_id, $exam_time, $answer_type);

                    // Execute the query for each question
                    if (!$stmt_insert->execute()) {
                        $msg .= "Error adding question $i: " . $stmt_insert->error . "<br>"; // Store individual errors
                    }
                }

                // If we reach here without errors, we can assume success
                if (empty($msg)) {
                    $msg = "Questions added successfully!";
                    header("location: ../dashboard.php");
                    exit();
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
                                            <form method="post">
                                                <input type="hidden" name="quiz_id" value="<?php echo isset($_GET['quiz_id']) ? $_GET['quiz_id'] : ''; ?>">
                                                <div class="form-group">
                                                    <label for="exam_time" class="control-label">Exam Timing (in minutes):</label>
                                                    <input type="number" name="exam_time" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="answer_type" class="control-label">Answer Type:</label>
                                                    <select name="answer_type" class="form-control" required>
                                                        <option value="options">Multiple Choice</option>
                                                        <option value="text">Text Answer</option>
                                                    </select>
                                                </div>

                                                <?php if ($total > 0) { ?>
                                                    <?php for ($i = 1; $i <= $total; $i++) { ?>
                                                        <div class="form-group question-block">
                                                            <label for="question" class="control-label">Question <?php echo $i; ?>:</label>
                                                            <input type="text" name="question[<?php echo $i; ?>]" class="form-control" required>
                                                            <div class="options">
                                                                <label for="options" class="control-label">Options (if applicable):</label>
                                                                <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 1" required>
                                                                <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 2" required>
                                                                <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 3" required>
                                                                <input type="text" name="options[<?php echo $i; ?>][]" class="form-control option" placeholder="Option 4" required>
                                                            </div>
                                                            <label for="correct_answer" class="control-label">Correct Answer:</label>
                                                            <input type="text" name="correct_answer_text[<?php echo $i; ?>]" class="form-control correct-answer-text" placeholder="Correct Answer" style="display:none;">
                                                            <select name="correct_answer[<?php echo $i; ?>]" class="form-control correct-answer-select" required>
                                                                <option value="">Select Correct Answer</option>
                                                                <option value="1">Option 1</option>
                                                                <option value="2">Option 2</option>
                                                                <option value="3">Option 3</option>
                                                                <option value="4">Option 4</option>
                                                            </select>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>

                                                <button type="submit" name="submit" class="btn btn-primary">Add Questions</button>
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
        $(document).ready(function() {
            // Initial check to hide/show elements based on default selected answer type
            $('select[name="answer_type"]').trigger('change');

            // Handle change event for answer type selection
            $('select[name="answer_type"]').change(function() {
                const selectedType = $(this).val();
                const questionBlocks = $('.question-block');

                questionBlocks.each(function() {
                    const correctAnswerSelect = $(this).find('.correct-answer-select');
                    const correctAnswerText = $(this).find('.correct-answer-text');
                    const optionsDiv = $(this).find('.options');

                    if (selectedType === 'text') {
                        correctAnswerSelect.hide(); // Hide the multiple choice options
                        correctAnswerText.show(); // Show the text answer input
                        optionsDiv.hide(); // Hide options input fields
                    } else {
                        correctAnswerSelect.show(); // Show the correct answer dropdown
                        correctAnswerText.hide(); // Hide the text answer input
                        optionsDiv.show(); // Show options input fields
                    }
                });
            });
        });
        
        $(function($) {
            $('#example').DataTable();

            $('#example2').DataTable({
                "scrollY": "300px",
                "scrollCollapse": true,
                "paging": false
            });

            $('#example3').DataTable();

            // Collapse other open menus when a new one is opened
            $('.has-children > a').on('click', function() {
                var $parent = $(this).parent();
                // Close other open menus
                $parent.siblings('.has-children').find('.child-nav').collapse('hide');
                // Toggle the clicked menu
                $parent.find('.child-nav').collapse('toggle');
            });
        });
    </script>
</body>

</html>