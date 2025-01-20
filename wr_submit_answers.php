<?php
session_start();
include('./assets/include/config.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Method Not Allowed');
}

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['answers'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Invalid Input');
}

$answers = $data['answers'];
$totalQuestions = count($answers);
$correctAnswers = 0;

// Create a new database connection
$conn = new mysqli("localhost", "root", "", "future_study_hub");

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Initialize quiz ID variable
$quizId = null;

foreach ($answers as $questionId => $answer) {
    // Query the correct answer from the database
    $stmt = $conn->prepare("SELECT correct_answer, quiz_id FROM wr_quiz WHERE id = ?");
    if ($stmt === false) {
        die(json_encode(['error' => 'SQL preparation failed: ' . $conn->error]));
    }

    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $stmt->bind_result($correctAnswer, $quizIdFromDB);
    $stmt->fetch();
    $stmt->close();

    // Increment correct answers if the user's answer is correct
    if ($answer == $correctAnswer) {
        $correctAnswers++;
    }

    // Store the quiz ID from the last question
    $quizId = $quizIdFromDB;
}

// Calculate the score
$score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;

// Insert the result into the wr_results table
$student_id = $_SESSION['user_id'];  // Assuming session contains the logged-in student's ID

$stmt = $conn->prepare("INSERT INTO wr_results (student_id, quiz_id, score, total_questions, correct_answers, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())");
if ($stmt === false) {
    die(json_encode(['error' => 'SQL preparation failed: ' . $conn->error]));
}

$stmt->bind_param("iiiii", $student_id, $quizId, $score, $totalQuestions, $correctAnswers);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Successfully inserted
    $response = ['status' => 'success', 'score' => $score];
} else {
    // Insertion failed
    $response = ['error' => 'Failed to insert results'];
}

$stmt->close();
$conn->close();

// Send the response with the score
header('Content-Type: application/json');
echo json_encode($response);
?>