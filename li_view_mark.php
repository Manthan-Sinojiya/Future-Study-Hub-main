<?php
session_start();
include('./assets/include/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:./login.php");
    exit;
}

$student_id = $_SESSION['user_id']; // Assuming student ID is stored in session
$conn = new mysqli("localhost", "root", "", "future_study_hub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student results
$stmt = $conn->prepare("SELECT q.quiz_id, q.question, r.score, r.total_questions, r.correct_answers, r.submitted_at 
                        FROM li_results r 
                        JOIN li_quiz q ON r.quiz_id = q.quiz_id 
                        WHERE r.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        h3 {
            color: #555;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        p {
            text-align: center;
            color: #555;
            font-size: 18px;
        }

        .back-button {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>My Profile</h2>
        <h3>Quiz Results</h3>
        <?php if (count($results) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Score</th>
                        <th>Total Questions</th>
                        <th>Correct Answers</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['quiz_id']); ?></td>
                            <td><?php echo htmlspecialchars($result['score']); ?>%</td>
                            <td><?php echo htmlspecialchars($result['total_questions']); ?></td>
                            <td><?php echo htmlspecialchars($result['correct_answers']); ?></td>
                            <td><?php echo htmlspecialchars($result['submitted_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No quiz results found.</p>
        <?php endif; ?>

        <a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
    </div>

</body>
</html>


<?php
session_start();
include('./assets/include/config.php');
include('./assets/include/db.php');
include("./assets/include/Header.php");

// Check if the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:./login.php");
    exit;
}

// Retrieve the student ID from session
$user_id = $_SESSION['user_id'];

// Fetch the quiz results for the student
$sql = "SELECT q.quiz_id, q.score, q.submitted_at, quiz.quiz_name FROM quiz_results q
        JOIN quizzes quiz ON quiz.id = q.quiz_id
        WHERE q.user_id = ? ORDER BY q.submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($quiz_id, $score, $submitted_at, $quiz_name);

$quizResults = [];
while ($stmt->fetch()) {
    $quizResults[] = [
        'quiz_id' => $quiz_id,
        'score' => $score,
        'submitted_at' => $submitted_at,
        'quiz_name' => $quiz_name
    ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Your Quiz Results</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Quiz Name</th>
                    <th>Score (%)</th>
                    <th>Submission Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($quizResults)) { ?>
                    <tr>
                        <td colspan="3">No quiz results available.</td>
                    </tr>
                <?php } else {
                    foreach ($quizResults as $result) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['quiz_name']); ?></td>
                            <td><?php echo number_format($result['score'], 2); ?>%</td>
                            <td><?php echo date("Y-m-d H:i:s", strtotime($result['submitted_at'])); ?></td>
                        </tr>
                    <?php } 
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php include("./assets/include/Footer.php"); ?>
<?php
session_start();
include('./assets/include/db.php');
include('./assets/include/config.php');

// Check if the student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location: ./login.php");
    exit;
}

$studentId = $_SESSION['student_id']; // Assuming the student ID is stored in the session

// Fetch the student's quiz results
$sql = "SELECT * FROM wr_results WHERE student_id = ? ORDER BY submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

// Display the results
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Results</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Your Quiz Results</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Score</th>
                        <th>Total Questions</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['quiz_id']; ?></td>
                            <td><?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?></td>
                            <td><?php echo $row['submitted_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven't taken any quizzes yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
