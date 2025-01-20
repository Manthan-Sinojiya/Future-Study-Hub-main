<?php
session_start();
include 'includes/config.php'; // Adjust path as per your file structure

// Check if the request method is GET and video parameter exists
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['video_id'])) {
    $videoId = $_GET['video_id'];

    // Fetch suggestions from the database based on video id
    $sql = "SELECT suggestion 
            FROM teacher_suggestions 
            WHERE video_id = :video_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
    $stmt->execute();
    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return suggestions as JSON response
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
} else {
    // Invalid request, return error response
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}
?>
