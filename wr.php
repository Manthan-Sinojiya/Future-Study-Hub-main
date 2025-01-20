<?php
session_start();
include('./assets/include/config.php');
include('./assets/include/db.php');
include("./assets/include/Header.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:./login.php");
    exit;
}

// Fetch quiz duration from the database
$quiz_id = 1; // Set the appropriate quiz ID dynamically
$query = "SELECT exam_time FROM wr_quiz WHERE quiz_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$stmt->bind_result($exam_time);

if ($stmt->fetch()) {
    $stmt->close();
} else {
    $exam_time = 6; // Default to 6 minutes if no value is found
    $stmt->close();
}
?>

<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .containerq {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .timer {
            font-size: 20px;
            margin-top: 20px;
            font-weight: bold;
        }

        .question-container {
            margin-top: 20px;
        }

        .question-card {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .form-check {
            margin: 10px 0;
        }

        .form-check-input {
            margin-right: 10px;
        }

        #score {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }

        .red {
            color: red;
        }

        .warning {
            color: orange;
        }

        @media (max-width: 600px) {
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .question-card {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="containerq">
        <h2 class="text-center mt-4">Questions</h2>
        <div class="text-center mt-4">
            <button class="btn btn-primary" id="startButton" onclick="startQuiz()">Start Quiz</button>
        </div>
        <div class="text-center timer" id="timer" style="display: none;">
            Time Remaining: <span id="timeDisplay"></span>
        </div>
        <div id="questions" class="question-container" style="display: none;">
            <!-- Questions will be loaded here -->
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-primary" id="submitButton" style="display: none;" onclick="submitAnswers()">Submit Answers</button>
        </div>
        <div id="score" class="text-center mt-4" style="display: none;">
            <!-- Score will be displayed here -->
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        let quizDuration = <?php echo json_encode($exam_time); ?>; // Fetching the exam time dynamically from PHP
        let timerInterval;

        $(document).ready(function() {
            $('#startButton').on('click', function() {
                fetchQuestions();
            });
        });

        function startQuiz() {
            $('#startButton').hide();
            $('#questions').show();
            $('#submitButton').show();
            startTimer(quizDuration * 60); // quizDuration is in minutes, convert it to seconds
        }

        function startTimer(duration) {
            let timer = duration,
                minutes, seconds;
            $('#timer').show();
            timerInterval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                $('#timeDisplay').text(`${minutes}:${seconds}`);
                if (timer === 60) {
                    $('#timeDisplay').addClass('red');
                }
                if (timer < 10) {
                    $('#timeDisplay').addClass('warning');
                }
                if (--timer < 0) {
                    clearInterval(timerInterval);
                    alert("Time's up! Submitting your answers now.");
                    setTimeout(() => {
                        submitAnswers(true);
                    }, 10000);
                }
            }, 1000);
        }

        function fetchQuestions() {
            $.ajax({
                url: 'wr_fetch_question.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (Array.isArray(data)) {
                        renderQuestions(data);
                    } else {
                        alert('Invalid data format from server.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching questions: ' + error);
                }
            });
        }

        function renderQuestions(questions) {
            let questionsHtml = '';
            questions.forEach(function(question) {
                questionsHtml += `
                <div class="card question-card">
                    <div class="card-body">
                        <h5 class="card-title">${question.question}</h5>
                        ${['option1', 'option2', 'option3', 'option4'].map(option => `
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question${question.id}" value="${question[option]}" id="question${question.id}_${option}">
                                <label class="form-check-label" for="question${question.id}_${option}">${question[option]}</label>
                            </div>
                        `).join('')}
                    </div>
                </div>`;
            });
            $('#questions').html(questionsHtml);
        }

        function submitAnswers(isAutoSubmit = false) {
            clearInterval(timerInterval);
            let answers = {};
            $('input[type=radio]:checked').each(function() {
                let questionId = $(this).attr('name').replace('question', '');
                answers[questionId] = $(this).val();
            });

            $.ajax({
                url: 'wr_submit_answers.php',
                type: 'POST',
                data: JSON.stringify({
                    answers: answers,
                    isAutoSubmit: isAutoSubmit
                }),
                contentType: 'application/json; charset=UTF-8',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#score').html(`Your score: ${response.score}%`).show();
                        $('#timer').hide();
                        $('#submitButton').hide();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error submitting answers: ' + error);
                }
            });
        }
    </script>
    <?php
    include("./assets/include/Footer.php");
    ?>
</body>

</html>