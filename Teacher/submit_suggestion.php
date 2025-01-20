<?php
session_start();
include('includes/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentId = $_POST['studentId'];
    $teacherId = $_SESSION['teacher_id'];
    $suggestion = $_POST['suggestion'];

    // Insert suggestion into database
    $sql = "INSERT INTO teacher_suggestions (student_id, teacher_id, suggestion) 
            VALUES (:studentId, :teacherId, :suggestion)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':studentId', $studentId, PDO::PARAM_INT);
    $query->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
    $query->bindParam(':suggestion', $suggestion, PDO::PARAM_STR);

    if ($query->execute()) {
        echo "Suggestion submitted successfully.";
    } else {
        echo "Error submitting suggestion.";
    }
} else {
    echo "Invalid request.";
}
?>
