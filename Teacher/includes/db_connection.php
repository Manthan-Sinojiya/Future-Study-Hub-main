<?php

// Replace these values with your actual database credentials
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'future_study_hub';

// Create a connection to the database
$conn = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Close the database connection when done


?>
