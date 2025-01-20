<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Example</title>

    <style>
        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
        }
        footer h2 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: bold;
            color: #000;
        }
        footer p, footer a {
            color: #000;
            text-decoration: none;
            font-size: 0.9rem;
            margin: 5px 0;
        }
        footer .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        footer .col-md-3 {
            flex: 1 1 calc(25% - 20px); /* Adjust column width */
            margin: 10px;
            min-width: 200px; /* Ensures columns don't shrink too much */
        }
        .bottom {
            background-color: #e9ecef;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            text-align: center;
        }
    </style>
</head>
<body>
    <footer>
        <div class="container">
            <div class="col-md-3">
                <h2>About Us</h2>
                <p>Future Study Hub is a leading educational platform, committed to providing high-quality learning resources.</p>
            </div>
            <div class="col-md-3">
                <h2>Support</h2>
                <p><a href="/courses.php">Courses</a></p>
                <p><a href="/video-lectures.php">Video Lectures</a></p>
                <p><a href="/material.php">Materials</a></p>
            </div>
            <div class="col-md-3">
                <h2>Our Social</h2>
                <p><a href="#">Facebook</a></p>
                <p><a href="#">Instagram</a></p>
                <p><a href="#">Telegram</a></p>
                <p><a href="#">X</a></p>
            </div>
            <div class="col-md-3">
                <h2>Company</h2>
                <p><a href="#">Contact Us</a></p>
                <p><a href="#">Terms of Service</a></p>
                <p><a href="#">Privacy Policy</a></p>
                <p><a href="#">Responsible Disclosure</a></p>
            </div>
        </div>
    </footer>
    <section class="bottom">
        <div class="container">
            <p>© Future Study Hub. All rights reserved.</p>
        </div>
    </section>
</body>
</html>
