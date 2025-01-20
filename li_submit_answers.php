<?php
session_start();
include('./assets/include/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Method Not Allowed');
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['answers'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Invalid Input');
}

$answers = $data['answers'];
$totalQuestions = count($answers);
$correctAnswers = 0;

$conn = new mysqli("localhost", "root", "", "future_study_hub");

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

foreach ($answers as $questionId => $answer) {
    // Query the correct answer from the database
    $stmt = $conn->prepare("SELECT correct_answer, quiz_id FROM li_quiz WHERE id = ?");
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $stmt->bind_result($correctAnswer, $quizId);
    $stmt->fetch();
    $stmt->close();

    // Increment correct answers if the user's answer is correct
    if ($answer == $correctAnswer) {
        $correctAnswers++;
    }
}

// Calculate the score
$score = ($correctAnswers / $totalQuestions) * 100;

// Insert the result into the li_results table
$student_id = $_SESSION['user_id'];  // Assuming session contains the logged-in student's ID
$quiz_id = $quizId;  // Assuming quiz_id is obtained from the `li_quiz` table

$stmt = $conn->prepare("INSERT INTO li_results (student_id, quiz_id, score, total_questions, correct_answers, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iiiii", $student_id, $quiz_id, $score, $totalQuestions, $correctAnswers);
$stmt->execute();
$stmt->close();

// Close the database connection
$conn->close();

// Send the response with the score
$response = ['status' => 'success', 'score' => $score];
header('Content-Type: application/json');
echo json_encode($response);
?>
