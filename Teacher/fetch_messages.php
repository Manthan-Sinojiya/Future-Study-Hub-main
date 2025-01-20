<?php 
session_start();
include('includes/config.php');

if (!isset($_POST['student_id']) || empty($_POST['student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$studentId = $_POST['student_id'];
$teacherUserId = $_SESSION['user_id'];

try {
    // Retrieve student_user_id based on student_id
    $sql = "SELECT user_id AS student_user_id FROM student_details WHERE student_id = :student_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':student_id', $studentId, PDO::PARAM_INT);

    if (!$query->execute()) {
        error_log("Error executing student_user_id query: " . implode(" ", $query->errorInfo()));
        echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve student user ID.']);
        exit;
    }

    $studentUserResult = $query->fetch(PDO::FETCH_ASSOC);
    if (!$studentUserResult) {
        echo json_encode(['status' => 'error', 'message' => 'Student user ID not found.']);
        exit;
    }
    $studentUserId = $studentUserResult['student_user_id'];

    // Fetch messages
    $sql = "SELECT message, sender_role FROM student_teacher_messages 
            WHERE student_user_id = :student_user_id AND teacher_user_id = :teacher_user_id 
            ORDER BY created_at ASC";
    $query = $dbh->prepare($sql);
    $query->bindParam(':student_user_id', $studentUserId, PDO::PARAM_INT);
    $query->bindParam(':teacher_user_id', $teacherUserId, PDO::PARAM_INT);

    if (!$query->execute()) {
        error_log("Error executing messages fetch query: " . implode(" ", $query->errorInfo()));
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch messages.']);
        exit;
    }

    $messages = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'messages' => $messages]);

} catch (Exception $e) {
    error_log("Exception caught: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred.']);
}
