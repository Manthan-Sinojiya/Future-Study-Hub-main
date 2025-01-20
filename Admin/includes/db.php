<?php
// db.php - Database connection code

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "future_study_hub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
