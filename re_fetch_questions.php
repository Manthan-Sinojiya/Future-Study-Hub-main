<?php
session_start();
include('./assets/include/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:./login.php");
    exit;
}

$student_id = $_SESSION['user_id']; 
$conn = new mysqli("localhost", "root", "", "future_study_hub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT id, question, option1, option2, option3, option4, text, img_path FROM re_quiz");
$stmt->execute();
$result = $stmt->get_result();
$questions = [];

while ($row = $result->fetch_assoc()) {
    $questions[] = [
        'id' => $row['id'],
        'question' => htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8'),
        'option1' => htmlspecialchars($row['option1'], ENT_QUOTES, 'UTF-8'),
        'option2' => htmlspecialchars($row['option2'], ENT_QUOTES, 'UTF-8'),
        'option3' => htmlspecialchars($row['option3'], ENT_QUOTES, 'UTF-8'),
        'option4' => htmlspecialchars($row['option4'], ENT_QUOTES, 'UTF-8'),
        'text' => htmlspecialchars($row['text'], ENT_QUOTES, 'UTF-8'),
        'img_path' => htmlspecialchars($row['img_path']),
    ];
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($questions);
?>

