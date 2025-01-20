<?php
session_start();
include("./includes/db_connection.php");
// Uncomment the following lines to enforce that only teachers can upload videos
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
//     header("location:../login.php");
//     exit;
// }
    
// HTML form for uploading video
echo '<div class="container mt-5">';
echo '<h2>Upload Video Lecture</h2>';
echo '<form action="" method="post" enctype="multipart/form-data">';
echo '<div class="form-group">';
echo '<label for="video_title">Video Title:</label>';
echo '<input type="text" class="form-control" id="video_title" name="video_title" required>';
echo '</div>';
echo '<div class="form-group">';
echo '<label for="video_file">Select Video:</label>';
echo '<input type="file" class="form-control-file" id="video_file" name="video_file" required>';
echo '</div>';
echo '<button type="submit" class="btn btn-primary" name="upload">Upload Video</button>';
echo '</form>';
echo '</div>';


// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
//     header("location:../login.php");
//     exit;
// }

// Handle video upload
if (isset($_POST['upload'])) {
    $video_title = $_POST['video_title'];
    $video_file = $_FILES['video_file']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($video_file);

    // Check if file is a video
    $videoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $extensions_arr = array("mp4","avi","3gp","mov","mpeg");

    if (in_array($videoFileType, $extensions_arr)) {
        // Upload file
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
            $query = "INSERT INTO videos (title, file_path) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $video_title, $target_file);
            $stmt->execute();
            echo "Upload successfully.";
        }
    } else {
        echo "Invalid file extension.";
    }
}

// Fetch all videos
$query = "SELECT * FROM videos";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Video ID: " . $row["id"] . " - Title: " . $row["title"] . "<br>";
    }
} else {
    echo "No videos found.";
}

$conn->close();
?>

<script>
// AJAX for tracking video progress
function updateProgress(videoId, userId, currentTime) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log('Progress updated');
        }
    };
    xhttp.open("POST", "update_video_progress.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("video_id=" + videoId + "&user_id=" + userId + "&current_time=" + currentTime);
}
</script>
