<?php
session_start();
include('includes/config.php');

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("location:../login.php");
    exit;
}

// Check if video_id is provided
if (isset($_GET['video_id'])) {
    $video_id = $_GET['video_id'];

    // Fetch the video details from the database including the video_path
    $sql = "SELECT video_title, description, video_path FROM videos WHERE video_id = :video_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':video_id', $video_id, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) {
        // Display the video details
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>View Video</title>
            <style>
            /* For screens up to 768px wide */
            @media (max-width: 768px) {

                /* Hide the sidebar initially on mobile view */
                .left-sidebar {
                    position: fixed;
                    width: 250px;
                    left: -250px;
                    top: 0;
                    bottom: 0;
                    background: #333;
                    transition: left 0.3s;
                    z-index: 9999;
                }

                /* When sidebar is open */
                .left-sidebar.open {
                    left: 0;
                }

                /* Main content should take full width when sidebar is hidden */
                .content-container {
                    margin-left: 0;
                }

                /* Adjust top bar */
                .top-navbar .navbar-toggle {
                    display: inline-block;
                }

                /* Hide icons and shorten text on smaller screens */
                .side-nav li a {
                    font-size: 14px;
                }

                /* Adjust the main container when sidebar is visible */
                .main-wrapper.overlay-active .content-container {
                    margin-left: 250px;
                }
            }
                .video-container {
                    width: 600px;
                    margin: auto;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="video-container">
                <h1><?php echo htmlentities($result->video_title); ?></h1>
                <p><?php echo htmlentities($result->description); ?></p>

                <!-- Embed or display the video using video_path from the database -->
                <video width="600" controls>
                    <source src="<?php echo htmlentities($result->video_path); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
                <script>
     $(document).ready(function() {
                $('.mobile-nav-toggle').on('click', function() {
                    $('.left-sidebar').toggleClass('open');
                    $('.main-wrapper').toggleClass('overlay-active');
                });

                // Close sidebar when clicking outside on mobile view
                $(document).click(function(e) {
                    if (!$(e.target).closest('.left-sidebar, .mobile-nav-toggle').length) {
                        $('.left-sidebar').removeClass('open');
                        $('.main-wrapper').removeClass('overlay-active');
                    }
                });
            });
    </script>
        </body>
        </html>
        <?php
    } else {
        echo "Video not found!";
    }
} else {
    echo "No video ID provided!";
}
?>
    