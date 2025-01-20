<?php
session_start();
include('./assets/include/config.php');
include('./assets/include/db.php');
include("./assets/include/Header.php");

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location: ./login.php");
    exit;
}

$student_id = $_SESSION['user_id']; // Assuming student ID is stored in session

// Create a connection to the database
$conn = new mysqli("localhost", "root", "", "future_study_hub");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch quiz results from the "li_results" table
$stmt1 = $conn->prepare("SELECT q.quiz_id, q.question, r.score, r.total_questions, r.correct_answers, r.submitted_at
                        FROM li_results r
                        JOIN li_quiz q ON r.quiz_id = q.quiz_id
                        WHERE r.student_id = ?");
if (!$stmt1) {
    die("Error in SQL Query 1: " . $conn->error);
}
$stmt1->bind_param("i", $student_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$liResults = $result1->fetch_all(MYSQLI_ASSOC);

// Fetch quiz results from the "re_results" table
$stmt2 = $conn->prepare("SELECT quiz_id, score, total_questions, correct_answers, submitted_at 
                         FROM re_results
                         WHERE student_id = ? 
                         ORDER BY submitted_at DESC");
if (!$stmt2) {
    die("Error in SQL Query 2: " . $conn->error);
}
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$reResults = $result2->fetch_all(MYSQLI_ASSOC);


// Fetch quiz results from the "wr_results" table
$stmt3 = $conn->prepare("SELECT quiz_id, score, total_questions, correct_answers, submitted_at 
                         FROM wr_results
                         WHERE student_id = ? 
                         ORDER BY submitted_at DESC");
if (!$stmt3) {
    die("Error in SQL Query 3: " . $conn->error);
}
$stmt3->bind_param("i", $student_id);
$stmt3->execute();
$result3 = $stmt3->get_result();
$wrResults = $result3->fetch_all(MYSQLI_ASSOC);

// Close the database connections
$stmt1->close();
$stmt2->close();
$stmt3->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Quiz Results</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .containerv {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        h3 {
            color: black;
            font-size: 20px;
            margin-top: 40px;
        }

        /* Table Styles */
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            background-color: #f9f9f9;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #0078d4;
            color: white;
        }

        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #e0e0e0;
        }

        /* No Results Message */
        p {
            color: #777;
            font-size: 16px;
            text-align: center;
        }

        /* Button Styles */
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0078d4;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #0078d4;
        }
    </style>
</head>

<body>
    <div class="containerv">
        <h2>My Quiz Results</h2>

        <h3>Results from li_results</h3>
        <?php if (count($liResults) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Score (%)</th>
                        <th>Total Questions</th>
                        <th>Correct Answers</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($liResults as $result): ?>
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
            <p>No results found in li_results.</p>
        <?php endif; ?>

        <h3>Results from re_results</h3>
        <?php if (count($reResults) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Score (%)</th>
                        <th>Total Questions</th>
                        <th>Correct Answers</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reResults as $result): ?>
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
            <p>No results found in re_results.</p>
        <?php endif; ?>


        <h3>Results from wr_results</h3>
        <?php if (count($wrResults) > 0): ?>
            <table>
                <thead>
                    <tr>
                    <th>Quiz ID</th>
                        <th>Score (%)</th>
                        <th>Total Questions</th>
                        <th>Correct Answers</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wrResults as $result): ?>
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
            <?php else: ?>x
            <p>No results found in wr_results.</p>
        <?php endif; ?>

        <a href="./profile.php" class="back-button">Back to Dashboard</a>
    </div>
    <?php
    include("./assets/include/Footer.php");
    ?>
</body>

</html>