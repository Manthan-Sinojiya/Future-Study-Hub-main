<?php
session_start();

include('./assets/include/db.php');
if (isset($_POST['con'])) {

    $sql = "SELECT otp FROM onetimepassword";
    $result = mysqli_query($conn, $sql);

    $otp = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $otp = $row['otp'];
    }

    $ot = '';
    for ($i = 1; $i <= 6; $i++) {
        $ot .= $_POST[$i];
    }

    if ($ot == $otp) {
        echo "<script>alert('OTP VERIFIED')</script>";
        header("location: forgetvr.php");
    } else {
        echo "<script>alert('PLEASE ENTER VALID OTP')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <link rel="icon" href="../Admin Dashboard/fj.png" type="image/gif" sizes="16x16"> -->
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
    </style>

</head>

<body onload="focusFirstInput()">
    <div class="container">
        <br>
        <div class="row">
            <div class="col-lg-5 col-md-7 mx-auto my-auto">
                <div class="card">
                    <div class="card-body px-lg-5 py-lg-5 text-center">
                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png" class="rounded-circle avatar-lg img-thumbnail mb-4" alt="profile-image">
                        <h2 class="text-info">2FA Security</h2>
                        <p class="mb-4">Enter 6-digit code from your authenticator app.</p>
                        <form id="2faForm" method="post">
                            <div class="row mb-4">
                                <!-- Use an array for input names to simplify processing -->
                                <?php for ($i = 1; $i <= 6; $i++) { ?>
                                    <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2 pe-0 pe-md-2">
                                        <input type="text" class="form-control text-lg text-center" required placeholder="_" name="<?php echo $i; ?>" minlength="1" maxlength="1" aria-label="2fa" oninput="moveToNext(this, event)">
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
            document.querySelector('input[name="1"]').focus();
        }

        function moveToNext(input, event) {
            if (input.value.length >= input.maxLength) {
                var next = input.parentElement.nextElementSibling.querySelector('input');
                if (next !== null) {
                    next.focus();
                } else {
                    // Submit form when last input is filled
                    document.getElementById("2faForm").submit();
                }
            }
        }
    </script>
</body>

</html>