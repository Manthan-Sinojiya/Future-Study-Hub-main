<?php
session_start();
include('./assets/include/config.php'); // Ensure this includes the PDO connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['video_id']) && isset($_POST['message'])) {
    $videoId = $_POST['video_id'];
    $message = $_POST['message'];

    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

    // Check if videoId and message are not empty
    if (empty($videoId) || empty($message) || empty($role)) {
        echo json_encode(["status" => "error", "message" => "Video ID, message, or role is missing."]);
        exit;
    }

    // Fetch the teacher user ID associated with the video
    try {
        $sql = "SELECT td.user_id AS teacher_user_id FROM videos v 
                LEFT JOIN teacher_details td ON v.teacher_id = td.teacher_id 
                WHERE v.video_id = :video_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
        $stmt->execute();

        $teacherData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$teacherData) {
            echo json_encode(["status" => "error", "message" => "Teacher not found for the specified video."]);
            exit;
        }
        $teacherUserId = $teacherData['teacher_user_id'];
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Exception: " . $e->getMessage()]);
        exit;
    }

    // Prepare the student_user_id and teacher_user_id fields based on sender's role
    $studentUserId = $role === 'student' ? $userId : null;
    $teacherUserId = $role === 'teacher' ? $userId : $teacherUserId;

    // Insert the message into the database
    try {
        $sql = "INSERT INTO student_teacher_messages (video_id, student_user_id, teacher_user_id, message, sender_role) 
                VALUES (:video_id, :student_user_id, :teacher_user_id, :message, :sender_role)";
        $stmt = $dbh->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
        $stmt->bindParam(':student_user_id', $studentUserId, PDO::PARAM_INT);
        $stmt->bindParam(':teacher_user_id', $teacherUserId, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':sender_role', $role, PDO::PARAM_STR);

        // Execute and check for success
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Message sent successfully."]);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(["status" => "error", "message" => "Database error: " . $errorInfo[2]]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Exception: " . $e->getMessage()]);
    }
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}