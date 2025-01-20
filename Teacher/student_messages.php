<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('./includes/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("location:../login.php");
    exit;
}

// Debugging teacher information
error_log("Teacher ID: " . $_SESSION['teacher_id']);
error_log("Teacher User ID: " . $_SESSION['user_id']);

// Fetch students who have asked questions to the teacher
$teacherId = $_SESSION['teacher_id'];
$teacherUserId = $_SESSION['user_id'];
$studentId = "";

// Update the SQL query to join with the correct tables
$sql = "SELECT DISTINCT 
            users.name AS student_name, 
            student_details.student_id,
            student_details.user_id AS student_user_id
        FROM student_teacher_messages 
        JOIN student_details ON student_teacher_messages.student_user_id = student_details.user_id 
        JOIN users ON student_details.user_id = users.user_id
        WHERE student_teacher_messages.teacher_user_id = :teacher_user_id";

$query = $dbh->prepare($sql);
$query->bindParam(':teacher_user_id', $teacherUserId, PDO::PARAM_INT);
// Execute and debug SQL query
if ($query->execute()) {
    $students = $query->fetchAll(PDO::FETCH_OBJ);
    error_log("Students fetched: " . json_encode($students));
} else {
    $errorInfo = $query->errorInfo();
    error_log("SQL Query Error: " . print_r($errorInfo, true));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teacher | Student Messages</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
        <link rel="stylesheet" href="css/toastr/toastr.min.css" media="screen">
        <link rel="stylesheet" href="css/icheck/skins/line/blue.css">
        <link rel="stylesheet" href="css/icheck/skins/line/red.css">
        <link rel="stylesheet" href="css/icheck/skins/line/green.css">
        <link rel="stylesheet" href="css/main.css" media="screen">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        /* Custom styles for messaging */
        .message-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
            background-color: #f9f9f9;
        }

        .message {
            margin-bottom: 10px;
        }

        .message.teacher {
            text-align: right;
        }

        .message.teacher p {
            background-color: #007bff;
            color: #fff;
            display: inline-block;
            border-radius: 5px;
            padding: 10px;
        }

        .message.student p {
            background-color: #f1f1f1;
            display: inline-block;
            border-radius: 5px;
            padding: 10px;
        }

        .message p {
            margin: 0;
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
                                <h2 class="title">Student Messages</h2>
                            </div>
                        </div>
                    </div>

                    <!-- /.row -->
                    <div class="row breadcrumb-div">
                        <div class="col-md-6">
                            <ul class="breadcrumb">
                                <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                <li> Students</li>
                                <li class="active">Student Message</li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.row -->

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>Messaging with Students</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body p-20">
                                            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Student Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cnt = 1;
                                                    foreach ($students as $student) { ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td><?php echo htmlentities($student->student_name); ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-primary btn-sm open-chat-btn" data-student-id="<?php echo $student->student_id; ?>" data-student-name="<?php echo htmlentities($student->student_name); ?>" data-toggle="modal" data-target="#chatModal">
                                                                    Open Chat
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php $cnt++; ?>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Chat -->
    <div id="chatModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="chatForm" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Chat with <span id="studentName"></span></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="student_id" name="student_id">
                        <div class="message-container" id="messageContainer">
                            <!-- Messages will be loaded here -->
                        </div>
                        <div class="form-group">
                            <label for="message" class="control-label">Your Message:</label>
                            <textarea class="form-control" id="message" name="message" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
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
        $(function () {
            $('.open-chat-btn').click(function () {
                var studentId = $(this).data('student-id');
                var studentName = $(this).data('student-name');
                $('#student_id').val(studentId);
                $('#studentName').text(studentName);
                loadMessages(studentId);
            });

            // In the chatForm submit event
            $('#chatForm').submit(function (event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'send_message.php', // Ensure this path is correct
                    data: formData,
                    dataType: 'json', // Expect a JSON response
                    success: function (res) {
                        if (res.status === 'success') {
                            $('#message').val(''); // Clear the message input
                            loadMessages($('#student_id').val()); // Reload messages
                        }
                            alert(res.message); // Show error message
                        },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error); // Log AJAX errors for debugging
                        alert('Error sending message.'. $studentId);
                    }
                });
            });

            function loadMessages(studentId) {
    $.ajax({
        type: 'POST',
        url: 'fetch_messages.php',
        data: { student_id: studentId },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                const messages = res.messages;
                $('#messageContainer').empty();
                if (messages.length > 0) {
                    messages.forEach(function (message) {
                        const messageClass = message.sender_role === 'teacher' ? 'teacher' : 'student';
                        $('#messageContainer').append('<div class="message ' + messageClass + '"><p>' + message.message + '</p></div>');
                    });
                } else {
                    $('#messageContainer').append('<div class="message"><p>No messages found.</p></div>');
                }
                $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
            } else {
                alert(res.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert('Error fetching messages.');
        }
    });
}

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

</body>
</html>