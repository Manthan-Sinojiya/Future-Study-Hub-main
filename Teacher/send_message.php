<?php
session_start();
include('includes/config.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check for teacher session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Verify that message and student_id are set
if (!isset($_POST['message']) || !isset($_POST['student_id']) || empty($_POST['message'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

    $message = $_POST['message'];
    $teacherUserId = $_SESSION['user_id'];
    $studentId = $_POST['student_id'];

    try {
    // Fetch the student_id, teacher_id, and video_id based on the teacher's user_id
    $sqlFetch = "SELECT sd.student_id, st.video_id 
                 FROM student_teacher_messages st
                 JOIN student_details sd ON sd.user_id = st.student_user_id
                 WHERE st.teacher_user_id = :teacher_user_id
                 ORDER BY st.created_at DESC LIMIT 1";
    $queryFetch = $dbh->prepare($sqlFetch);
    $queryFetch->bindParam(':teacher_user_id', $teacherUserId, PDO::PARAM_INT);
    
    if ($queryFetch->execute()) {
        $messageData = $queryFetch->fetch(PDO::FETCH_ASSOC);
        if (!$messageData) {
            echo json_encode(['status' => 'error', 'message' => 'No message data found.']);
            exit;
        }
        $videoId = $messageData['video_id'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching message data.']);
        exit;
    }

    // Retrieve student_user_id based on student_id
    $sql = "SELECT user_id AS student_user_id FROM student_details WHERE student_id = :student_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':student_id', $studentId, PDO::PARAM_INT);
    $query->execute();
    $studentUserResult = $query->fetch(PDO::FETCH_ASSOC);

    if (!$studentUserResult) {
        echo json_encode(['status' => 'error', 'message' => 'Student user ID not found.']);
        exit;
    }

    $studentUserId = $studentUserResult['student_user_id'];

    // Insert the message into the database
    $sql = "INSERT INTO student_teacher_messages (video_id, student_user_id, teacher_user_id, message, sender_role) 
            VALUES (:video_id, :student_user_id, :teacher_user_id, :message, 'teacher')";
    $query = $dbh->prepare($sql);
    $query->bindParam(':video_id', $videoId, PDO::PARAM_INT); // Bind video_id
    $query->bindParam(':student_user_id', $studentUserId, PDO::PARAM_INT);
    $query->bindParam(':teacher_user_id', $teacherUserId, PDO::PARAM_INT);
    $query->bindParam(':message', $message, PDO::PARAM_STR);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error inserting message']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>