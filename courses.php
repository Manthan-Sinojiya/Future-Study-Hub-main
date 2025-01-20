<?php
session_start();
include('./assets/include/db.php');

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:./login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Future Study Hub - IELTS Courses</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400">

    <style>
        .course-card {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            background-color: #fff;
            height: 100%;
            /* Ensure the card takes up full height of its container */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Pushes content to top and bottom */
        }

        .course-card h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .course-card p {
            font-size: 1em;
            margin-bottom: 15px;
        }

        .course-card .price {
            font-size: 1.25em;
            font-weight: bold;
            color: #333;
            /* Dark text color */
            margin-bottom: 15px;
        }

        .course-card .btn {
            background-color: #6a1b9a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-align: center;
            display: inline-block;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            margin-top: auto;
            /* Push button to the bottom */
        }

        .course-card .btn:hover {
            background-color: #4a148c;
        }

        .row.gx-5 {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Space between cards */
        }

        .col-md-6,
        .col-lg-4 {
            flex: 1;
            min-width: 300px;
            /* Ensures a minimum width for responsiveness */
        }
    </style>
</head>

<body>
    <?php include("./assets/include/Header.php"); ?>

    <section class="hero">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Future Study Hub - IELTS Courses</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="usp">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center spacer">
                    <h2>Online <i>Classes For <i>IELTS</i> Learning.</h2>
                    <p>
                        <i>
                            Score higher, go farther! Elevate your IELTS with a tailored course for Listening, Reading, Writing, Speaking. Achieve academic and immigration goals.
                        </i>
                    </p>
                </div>
            </div>

            <?php
            // Fetch courses from the database with the new tables structure
            $sql = "SELECT c.course_id, c.course_name, m.module_name, c.year, c.month, c.price, c.description 
                    FROM courses c 
                    JOIN module m ON c.module_id = m.module_id";
            $result = $conn->query($sql);
            ?>

            <div class="row gx-5">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-md-6 col-lg-4">';
                        echo '<div class="course-card">';
                        echo "<h2>" . htmlentities($row['course_name']) . " - " . htmlentities($row['module_name']) . "</h2>";
                        echo "<p>" . htmlentities($row['description']) . "</p>";
                        echo '<p class="price">Price: ₹' . htmlentities($row['price']) . '</p>';
                        echo '<button class="btn" data-course-id="' . htmlentities($row['course_id']) . '" onclick="startPayment(' . htmlentities($row['course_id']) . ')">Enroll Now</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No courses available.</p>";
                }
                ?>

            </div>
        </div>
    </section>

    <?php include("./assets/include/Footer.php"); ?>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function startPayment(courseId) {
            $.ajax({
                url: 'get_payment_data.php',
                type: 'POST',
                data: {
                    course_id: courseId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert(response.error);
                        return;
                    }
                    var options = {
                        "key": response.api_key,
                        "amount": response.amount,
                        "currency": response.currency,
                        "name": response.course_name,
                        "description": response.course_description,
                        "order_id": response.order_id,
                        "handler": function(paymentResponse) {
                            $.ajax({
                                url: 'verify_payment.php',
                                type: 'POST',
                                data: {
                                    razorpay_payment_id: paymentResponse.razorpay_payment_id,
                                    razorpay_order_id: paymentResponse.razorpay_order_id,
                                    razorpay_signature: paymentResponse.razorpay_signature,
                                    course_id: courseId
                                },
                                dataType: 'json',
                                success: function(result) {
                                    if (result.success) {
                                        alert("Payment successful");
                                        window.location.href = "video-lectures.php";
                                    } else {
                                        alert(result.error);
                                        console.log("Debug info:", result.debug); // Log debug info for further inspection
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Verification AJAX error: ", error);
                                }
                            });
                        },
                        "prefill": {
                            "name": "",
                            "email": ""
                        },
                        "theme": {
                            "color": "#F37254"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                },
                error: function(xhr, status, error) {
                    console.error("Payment initialization AJAX error: ", error);
                }
            });
        }
    </script>

</body>

</html>

<?php
$conn->close();
?>