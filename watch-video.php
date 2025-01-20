<?php
session_start();
error_reporting(1);
include('./assets/include/db.php');
include('./assets/include/config.php');
include('./assets/include/Header.php');

// Validate the video ID
if (!isset($_GET['video']) || empty($_GET['video'])) {
    header("location:index.php");
    exit;
}

// Assuming video ID is passed through GET parameter (ensure to sanitize and validate this in production)
$videoId = intval($_GET['video']);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch video details from database based on video ID
$sql_video_details = "SELECT video_title, description, video_path FROM videos WHERE video_id = :video_id";
$query_video_details = $dbh->prepare($sql_video_details);
$query_video_details->bindParam(':video_id', $videoId, PDO::PARAM_INT);
$query_video_details->execute();
$videoDetails = $query_video_details->fetch(PDO::FETCH_ASSOC);

// If no video details found, redirect to index.php
if (!$videoDetails) {
    header("location:index.php");
    exit;
}

$videoTitle = $videoDetails['video_title'];
$videoDescription = $videoDetails['description'];
$videoPath = $videoDetails['video_path']; // Ensure the correct video_path is fetched

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Video - Future Study Hub</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="./Student/includes/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>


    <div class="container video-container">
        <div class="video-wrapper">
            <h2 class="video-title"><?php echo htmlentities($videoTitle); ?></h2>
            <p class="video-description"><?php echo htmlentities($videoDescription); ?></p>
            <div class="video-player">
                <video id="video" controls>
                    <source src="./Teacher/<?php echo htmlentities($videoPath); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
        <a href="video-lectures.php" class="btn-back">Back to Video Lectures</a>
    </div>

    <?php include("./Student/includes/Footer.php"); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const videoId = <?php echo json_encode($videoId); ?>;
            const userId = <?php echo json_encode($userId); ?>;

            let isPlaying = false;
            let progressInterval = null;
            let lastProgressTime = 0;
            let lastProgressPercentage = 0;

            // Show loading indicator while video loads
            const loadingIndicator = document.createElement('div');
            loadingIndicator.classList.add('loading-indicator');
            loadingIndicator.innerHTML = '<span></span><span></span><span></span><span></span>';
            document.querySelector('.video-player').appendChild(loadingIndicator);

            // Show loading indicator while video loads
            video.addEventListener('waiting', () => {
                loadingIndicator.style.display = 'block';
            });

            // Hide loading indicator when video starts playing
            video.addEventListener('playing', () => {
                loadingIndicator.style.display = 'none';
                startProgressTracking();
            });

            // Pause progress tracking when video is paused
            video.addEventListener('pause', () => {
                clearInterval(progressInterval);
                sendProgressData(video.currentTime, video.duration, 'pause');
            });

            // Update the visual progress bar as video plays
            video.addEventListener('timeupdate', () => {
                const percentage = (video.currentTime / video.duration) * 100;
                document.querySelector('.progress-fill').style.width = percentage + '%';
            })

            function startProgressTracking() {
                progressInterval = setInterval(() => {
                    const currentTime = video.currentTime;
                    const duration = video.duration;
                    const percentage = (currentTime / duration) * 100;

                    if (currentTime >= lastProgressTime + 10 || percentage >= lastProgressPercentage + 10) {
                        sendProgressData(currentTime, duration, 'playing');
                        lastProgressTime = currentTime;
                        lastProgressPercentage = percentage;
                    }
                }, 10000);
            }

            video.addEventListener('dblclick', () => {
                if (!document.fullscreenElement) {
                    document.querySelector('.video-player').requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            });

            function sendProgressData(currentTime, duration, action) {
                fetch('track_progress.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            video_id: videoId,
                            watched_duration: currentTime,
                            total_duration: duration,
                            action: action
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error:', data.error);
                        } else {
                            console.log('Progress saved successfully');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                    });
            }
        });
    </script>

</body>

</html>