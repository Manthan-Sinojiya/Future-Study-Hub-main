<?php
ob_start();

include('./assets/include/db.php');

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];

    // Fetch user details to get profile image
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if OTP is verified
    if (isset($_SESSION['role']) && (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified'])) {
        header("Location: ./otp.php");
        exit();
    }

    // Fetch profile image from student_details table
    $stmt = $conn->prepare("SELECT profile_image FROM student_details WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output data of each row
        $row = $result->fetch_assoc();
        $profile_picture = $row['profile_image']; // Store the profile picture path
    } else {
        // No profile image found
        $profile_picture = 'default-avatar.png';
    }

    // Check if profile image exists or use default image
    $profile_image = !empty($profile_picture) ? '../uploads/profile_pictures/' . $profile_picture : 'default-avatar.png';
} else {
    // If user is not logged in, use default image
    $profile_image = 'default-avatar.png';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Future Study Hub</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <!-- Custom Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400&display=swap">
    <!-- Custom Styles -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin-top: 10px;
        }

        .navbar-brand img {
            width: 150px;
        }

        .profile .dropdown-toggle::after {
            display: none;
        }

        .profile img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        /* Hover functionality */
        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }

        .dropdown-menu {
            display: none;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-xl navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <h1>FSH</h1>
                </a>
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="examDropdown" role="button" aria-expanded="false">
                                Exam
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="examDropdown">
                                <li><a class="dropdown-item" href="li.php">Listening Exam</a></li>
                                <li><a class="dropdown-item" href="re.php">Reading Exam</a></li>
                                <li><a class="dropdown-item" href="wr.php">Writing Exam</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="courses.php">IELTS</a></li>
                        <li class="nav-item"><a class="nav-link" href="material.php">Material</a></li>
                        <li class="nav-item"><a class="nav-link" href="video-lectures.php">Video-Lectures</a></li>
                    </ul>
                    <?php if (isset($_SESSION['email'])): ?>
                        <div class="dropdown profile">
                            <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if (!empty($profile_picture) && file_exists($profile_picture)): ?>
                                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                                <?php else: ?>
                                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar">
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="profileDropdown">
                                <li><span class="dropdown-item-text">Welcome, <?php echo htmlspecialchars($user['name']); ?></span></li>
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="d-flex">
                            <a href="./login.php" class="btn btn-outline-secondary me-2">Login</a>
                            <a href="./Registration.php" class="btn btn-outline-primary">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>