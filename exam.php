<?php
session_start();
include('../assets/include/db.php');
include('../assets/include/config.php');
 include("../assets/include/Header.php");  // Adjust path as per your file structure

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:../login.php");
    exit;
}
// Add exam
$name = $_POST['name'] ?? '';
$module = $_POST['module'] ?? '';

if (!empty($name) && !empty($module)) {
    $sql = "INSERT INTO exams (name, module) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $module);

    if ($stmt->execute()) {
        echo "Exam added successfully";
    } else {
        echo "Error: Unable to add exam";
    }

    $stmt->close();
} else {
    echo "Error: Name and module are required";
}

// Fetch exams
$sql = "SELECT * FROM exams";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"] . " - Name: " . $row["name"] . " - Module: " . $row["module"] . "<br>";
    }
} else {
    echo "0 results";
}

// Submit result
$student_id = $_POST['student_id'] ?? '';
$exam_id = $_POST['exam_id'] ?? '';
$score = $_POST['score'] ?? '';

if (!empty($student_id) && !empty($exam_id) && !empty($score)) {
    $sql = "INSERT INTO results (student_id, exam_id, score) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $exam_id, $score);

    if ($stmt->execute()) {
        echo "Result submitted successfully";
    } else {
        echo "Error: Unable to submit result";
    }

    $stmt->close();
} else {
    echo "Error: Student ID, Exam ID, and Score are required";
}

$conn->close();
?>
