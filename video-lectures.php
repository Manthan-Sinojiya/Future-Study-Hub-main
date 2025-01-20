<?php
session_start();
include('./assets/include/db.php');
include('./assets/include/config.php');
include('./assets/include/Header.php');

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:./login.php");
    exit;
}

// Fetch unique topics with topic number, topic name, module name, and subject name from the database
$sql_topics = "SELECT DISTINCT v.topic_number, t.topic_name, m.module_name, s.sub_name 
                FROM videos v 
                LEFT JOIN topics t ON v.topic_number = t.topic_number
                LEFT JOIN module m ON v.module = m.module_id
                LEFT JOIN subject s ON v.subject = s.sub_id
                ORDER BY v.topic_number ASC";
$query_topics = $dbh->prepare($sql_topics);
$query_topics->execute();
$topics = $query_topics->fetchAll(PDO::FETCH_OBJ);

// Fetch all videos with teacher information from `users` and `teacher_details`
$sql_videos = "SELECT v.video_id, v.video_title, v.description, v.topic_number, v.video_path, v.created_at, 
                      v.teacher_id, u.name AS teacher_name
                FROM videos v
                LEFT JOIN teacher_details td ON v.teacher_id = td.teacher_id
                LEFT JOIN users u ON td.user_id = u.user_id
                ORDER BY v.topic_number ASC";
$query_videos = $dbh->prepare($sql_videos);
$query_videos->execute();
$videos = $query_videos->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Lectures - Future Study Hub</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin-top: 10px;
        }

        .dropdown-item {
            cursor: pointer;
        }

        .video-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .video-card img {
            width: 100%;
            height: auto;
        }

        .video-card-body {
            padding: 15px;
        }

        .video-card-body h5 {
            margin-bottom: 10px;
            font-size: 1.25rem;
        }

        .video-card-body p {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .video-details {
            display: none;
            margin-top: 10px;
        }

        .teacher-info {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .video-actions {
            margin-top: 10px;
        }

        .video-actions button {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
        }

        .video-actions button:hover {
            background-color: #0056b3;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .chat-box {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-height: 400px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message-list {
            list-style: none;
            padding: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .message {
            display: flex;
            margin-bottom: 10px;
        }

        .message-bubble {
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
            position: relative;
            clear: both;
        }

        .message-right {
            justify-content: flex-end;
        }

        .message-left {
            justify-content: flex-start;
        }

        .message-right .message-bubble {
            background-color: #007bff;
            color: white;
            border-top-left-radius: 20px;
        }

        .message-left .message-bubble {
            background-color: #ffffff;
            color: #343a40;
            border-top-right-radius: 20px;
        }

        .message-input {
            border-radius: 20px;
            padding: 10px 15px;
            border: 1px solid #ccc;
            font-size: 14px;
            width: 100%;
            resize: none;
        }

        #sendMessageBtn {
            background-color: #007bff;
            border-radius: 40%;
            width: 50px;
            padding: 12px;
            border: none;
            margin-left: 10px;
            color: white;
            transition: background-color 0.3s;
        }

        #sendMessageBtn:hover {
            background-color: #0056b3;
        }

        .chat-box {
            overflow-y: auto;
            scroll-behavior: smooth;
        }
    </style>
</head>

<body>
    <section class="mybg">
        <div class="container">
            <h1>Video Lectures</h1>
            <div class="accordion" id="topicsAccordion">
                <?php
                $prevTopicNumber = null;
                foreach ($topics as $topic) {
                    if ($prevTopicNumber !== $topic->topic_number) {
                        $prevTopicNumber = $topic->topic_number;
                ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-<?php echo htmlentities($topic->topic_number); ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo htmlentities($topic->topic_number); ?>" aria-expanded="false" aria-controls="collapse-<?php echo htmlentities($topic->topic_number); ?>">
                                    <?php echo "Topic " . htmlentities($topic->topic_number) . ": " . htmlentities($topic->topic_name); ?>
                                </button>
                            </h2>
                            <div id="collapse-<?php echo htmlentities($topic->topic_number); ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo htmlentities($topic->topic_number); ?>" data-bs-parent="#topicsAccordion">
                                <div class="accordion-body">
                                    <p><strong>Module:</strong> <?php echo htmlentities($topic->module_name); ?></p>
                                    <p><strong>Subject:</strong> <?php echo htmlentities($topic->sub_name); ?></p>
                                    <div class="row">
                                        <?php foreach ($videos as $video) {
                                            if ($video->topic_number == $topic->topic_number) { ?>
                                                <div class="col-md-4">
                                                    <div class="video-card">
                                                        <img src="https://img.youtube.com/vi/<?php echo htmlentities($video->video_path); ?>/hqdefault.jpg" alt="Video Thumbnail">
                                                        <div class="video-card-body">
                                                            <h5><?php echo htmlentities($video->video_title); ?></h5>
                                                            <p><?php echo htmlentities($video->description); ?></p>
                                                            <div class="teacher-info">
                                                                <strong>Uploaded by:</strong> <?php echo htmlentities($video->teacher_name); ?>
                                                            </div>
                                                            <div class="video-actions">
                                                                <button class="video-list-item" data-video-id="<?php echo htmlentities($video->video_id); ?>">Watch Video</button>
                                                                <button class="suggestion-btn btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#messageModal" data-video-id="<?php echo htmlentities($video->video_id); ?>">
                                                                    <i class="fas fa-comments"></i> Message Teacher
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
            </div>
        </div>
    </section>

    <!-- Modal for Teacher-Student Chat -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Teacher-Student Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body chat-box">
                    <div class="message-list">
                        <!-- Messages will be dynamically loaded here -->
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center">
                    <textarea class="form-control message-input" id="message-input" placeholder="Ask a question..." rows="1"></textarea>
                    <button type="button" class="btn btn-primary" id="sendMessageBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            let currentVideoId = null;

            // Handle message modal open
            $(document).on('click', '.suggestion-btn', function(event) {
                event.stopPropagation();
                const videoId = $(this).data('video-id');
                currentVideoId = videoId;
                loadMessages(videoId);
            });

            // Handle send message
            $('#sendMessageBtn').on('click', function() {
                const message = $('#message-input').val().trim();
                if (message) {
                    sendMessage(currentVideoId, message);
                    $('#message-input').val('');
                } else {
                    alert("Please enter a message before sending.");
                }
            });

            // Fetch messages and display them in the message box
            function loadMessages(videoId) {
                $.ajax({
                    type: 'GET',
                    url: 'fetch_messages.php',
                    data: {
                        video_id: videoId
                    },
                    dataType: 'json',
                    success: function(response) {
                        const messageList = $('.message-list');
                        messageList.html(''); // Clear previous messages

                        if (response.length > 0) {
                            response.forEach(msg => {
                                const messageClass = msg.sender_role === 'student' ? 'message-right' : 'message-left';
                                const listItem = `
                                <div class="message ${messageClass}">
                                    <div class="message-bubble">${msg.message} <span style="font-size: 0.8em; display: block;">(Sent at: ${msg.created_at})</span></div>
                                </div>`;
                                messageList.append(listItem);
                            });
                        } else {
                            messageList.append('<div class="message"><p>No messages yet.</p></div>');
                        }
                        // Scroll to the bottom of the chat smoothly after loading messages
                        $('.chat-box').animate({
                            scrollTop: $('.chat-box')[0].scrollHeight
                        }, 'smooth');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching messages:', error);
                    }
                });
            }

            // Send a message
            function sendMessage(videoId, message) {
                $.ajax({
                    type: 'POST',
                    url: 'send_message.php',
                    data: {
                        video_id: videoId,
                        message: message
                    },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === "success") {
                                loadMessages(videoId);
                            } else {
                                alert('Error: ' + result.message);
                            }
                        } catch (error) {
                            console.error('Error parsing response:', error);
                            alert('Unexpected error occurred while processing the response.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        alert('Failed to send the message. AJAX error: ' + error);
                    }
                });
            }

            // Expand and collapse video details
            $(document).on('click', '.video-item', function() {
                const topicNumber = $(this).data('topic-number');
                const detailsRow = $('#details-' + topicNumber);
                detailsRow.toggle();
            });
            // Event listener for video list items (to redirect to watch-video.php)
            $('.video-list-item').on('click', function() {
                const videoId = $(this).data('video-id');
                window.location.href = 'watch-video.php?video=' + videoId;
            });
        });
    </script>

    <?php include("./assets/include/Footer.php"); ?>
</body>

</html>