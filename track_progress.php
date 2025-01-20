<?php
session_start();
include ('./assets/include/config.php');

// Retrieve JSON data sent from client-side JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Extract relevant data
$userId = isset($data['user_id']) ? intval($data['user_id']) : 0;
$videoId = isset($data['video_id']) ? intval($data['video_id']) : 0;
$watchedDuration = isset($data['watched_duration']) ? floatval($data['watched_duration']) : 0.0;
$action = isset($data['action']) ? trim($data['action']) : '';

// Ensure studentId and videoId are valid
if ($userId > 0 && $videoId > 0) {
    try {
        if ($action === 'playing') {
            // Check if there's already an entry for this student and video
            $sqlCheck = "SELECT * FROM progress WHERE student_id = (SELECT student_id FROM student_details WHERE user_id = :user_id) AND video_id = :video_id";
            $stmtCheck = $dbh->prepare($sqlCheck);
            $stmtCheck->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmtCheck->bindParam(':video_id', $videoId, PDO::PARAM_INT);
            $stmtCheck->execute();
            $rowCount = $stmtCheck->rowCount();

            if ($rowCount > 0) {
                // Update existing progress record
                $sql = "UPDATE progress 
                        SET watched_duration = :watched_duration, last_watched = CURRENT_TIMESTAMP
                        WHERE student_id = (SELECT student_id FROM student_details WHERE user_id = :user_id) AND video_id = :video_id";
            } else {
                // Insert new progress record
                $sql = "INSERT INTO progress (student_id, video_id, watched_duration, last_watched)
                        VALUES ((SELECT student_id FROM student_details WHERE user_id = :user_id), :video_id, :watched_duration, CURRENT_TIMESTAMP)";
            }

            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
            $stmt->bindParam(':watched_duration', $watchedDuration, PDO::PARAM_STR);
            $stmt->execute();
        } elseif ($action === 'pause') {
            // Update existing progress record when video is paused
            $sql = "UPDATE progress 
                    SET watched_duration = :watched_duration, last_watched = CURRENT_TIMESTAMP
                    WHERE student_id = (SELECT student_id FROM student_details WHERE user_id = :user_id) AND video_id = :video_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
            $stmt->bindParam(':watched_duration', $watchedDuration, PDO::PARAM_STR);
            $stmt->execute();
        }

        // Return success response if execution is successful
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Handle database error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Return error response if studentId or videoId are invalid
    echo json_encode(['error' => 'Invalid user_id or video_id']);
}
?>
