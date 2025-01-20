<?php
session_start();
include('./assets/include/config.php'); // Include your PDO connection file

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['video_id'])) {
    $videoId = $_GET['video_id'];

    try {
        // Fetch messages related to the video
        $sql = "SELECT message, sender_role, created_at 
                FROM student_teacher_messages 
                WHERE video_id = :video_id 
                ORDER BY created_at ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the messages and return them as JSON
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}