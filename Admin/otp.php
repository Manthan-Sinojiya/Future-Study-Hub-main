<?php
session_start();

if (isset($_POST['con'])) {
    // Retrieve OTP from session
    $session_otp = $_SESSION['otp'] ?? null;
    $entered_otp = implode('', $_POST['code']); // Combine the input fields

    if ($entered_otp == $session_otp) {
        $_SESSION['otp_verified'] = true; // Set verification flag
        echo "<script>alert('OTP VERIFIED');</script>";
        header("location:./dashboard.php"); // Redirect to the home page after verification
        exit;
    } else {
        echo "<script>alert('PLEASE ENTER A VALID OTP');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Future Study Hub</title>

    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700,800,900&display=swap" rel="stylesheet">

    <style>
        body {
            background: #eee;
        }

        .card {
            box-shadow: 0 20px 27px 0 rgb(0 0 0 / 5%);
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0 solid rgba(0, 0, 0, .125);
            border-radius: 1rem;
        }

        .img-thumbnail {
            padding: .25rem;
            background-color: #ecf2f5;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            max-width: 100%;
            height: auto;
        }

        .avatar-lg {
            height: 150px;
            width: 150px;
        }

        h1:hover {
            color: #007bff;
        }

        h1 {
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .form-control {
                font-size: 1.5rem;
                height: 50px;
            }

            .col-2 {
                padding: 0 5px;
            }
        }
    </style>

</head>

<body onload="focusFirstInput()">
    <div class="container">
        <br>
        <div class="row">
            <div class="col-lg-5 col-md-7 mx-auto my-auto">
                <h1>Future Study Hub</h1>
                <div class="card">

                    <div class="card-body px-lg-5 py-lg-5 text-center">
                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png" class="rounded-circle avatar-lg img-thumbnail mb-4" alt="profile-image">
                        <h2 class="text-info">2FA Security</h2>
                        <p class="mb-4">Enter 6-digit code from your authenticator app.</p>
                        <form id="2faForm" method="post">
                            <div class="row mb-4">
                                <?php for ($i = 0; $i < 6; $i++) { ?>
                                    <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2 pe-0 pe-md-2">
                                        <input type="text" class="form-control text-lg text-center" placeholder="_" name="code[]" minlength="1" maxlength="1" inputmode="numeric" pattern="[0-9]*" aria-label="2fa" oninput="moveToNext(this, event)" onkeydown="handleBackspace(this, event)" onkeypress="preventNonNumericInput(event)">
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="con" class="btn bg-info btn-lg my-4">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function focusFirstInput() {
            setTimeout(() => {
                document.querySelector('input[name="code[]"]').focus();
            }, 100);
        }

        function moveToNext(input, event) {
            if (input.value.length >= input.maxLength) {
                const next = input.parentElement.nextElementSibling?.querySelector('input');
                if (next) {
                    setTimeout(() => next.focus(), 50);
                }
            }
        }

        function handleBackspace(input, event) {
            if (event.key === "Backspace" && input.value === "") {
                const prev = input.parentElement.previousElementSibling?.querySelector('input');
                if (prev) {
                    prev.focus();
                    prev.value = ""; // Clear the previous input value
                }
            }
        }

        function preventNonNumericInput(event) {
            const isNumeric = /^\d$/.test(event.key);
            if (!isNumeric) {
                event.preventDefault();
            }
        }
    </script>
</body>

</html>