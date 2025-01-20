<?php
session_start();
include_once("./assets/include/db.php");
include_once("./assets/include/config.php");
include_once("./assets/include/Header.php"); 

// Check if the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:login.php");
    exit;
}

// Fetch the logged-in student's ID from the session
$user_id = $_SESSION['user_id'];

// Fetch student data from users and student_details tables
$sql = "SELECT u.name AS name, u.email, u.dob, u.gender, sd.profile_image, sd.enrollment_date 
        FROM users u
        JOIN student_details sd ON u.user_id = sd.user_id
        WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Output data of each row
    $row = $result->fetch_assoc();
    $student_name = $row['name'];
    $email = $row['email'];
    $dob = $row['dob'];
    $gender = $row['gender'];
    $enrollment_date = $row['enrollment_date'];
    $profile_picture = $row['profile_image']; // Store the profile picture path
} else {
    echo "No results found";
    exit;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Profile - Future Study Hub</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Plus+Jakarta+Sans:wght@400">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .con {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #0078d4;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
        }

        .view-reports {
            background-color: #ffcc00;
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .profile-card {
            padding: 20px;
        }

        .profile-header {
            text-align: center;
        }

        .profile-picture {
            background-color: #cccccc;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            overflow: hidden; /* Make sure image fits in the circular container */
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-details {
            display: flex;
            justify-content: space-between;
        }

        .personal-details, .class-details {
            width: 45%;
        }

        h3 {
            border-bottom: 2px solid #0078d4;
            padding-bottom: 5px;
        }

        .status-active {
            color: green;
            font-weight: bold;
        }

        .update-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #0078d4;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }

        .update-button:hover {
            background-color: #005fa3;
        }

        .update-form {
            display: none;
        }

        .update-form input,
        .update-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="con">
        <div class="header">
            <h1>Student Profile</h1>
            <a href="./view_result.php" class="view-reports">View Result</a>
        </div>
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-picture">
                    <?php if (!empty($profile_picture) && file_exists($profile_picture)): ?>
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar">
                    <?php endif; ?>
                </div>
                <h2><?php echo htmlspecialchars($student_name); ?></h2>
            </div>
            <div class="profile-details">
                <div class="personal-details">
                    <h3>Personal details</h3>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
                    <p><strong>Birth Date:</strong> <?php echo date('d F Y', strtotime($dob)); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                </div>
                <div class="class-details">
                    <h3>Class details</h3>
                    <p><strong>Date of entry:</strong> <?php echo date('d F Y', strtotime($enrollment_date)); ?></p>
                </div>
            </div>
            <button class="update-button">Update</button>
            <form class="update-form" method="POST" action="update_profile.php" enctype="multipart/form-data">
                <h3>Update Profile</h3>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <label for="student_name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student_name); ?>" required>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>" required>
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)">
                <button type="submit" class="update-button">Save Changes</button>
            </form>
        </div>
    </div>
    <script>
        document.querySelector('.update-button').addEventListener('click', function() {
            var form = document.querySelector('.update-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        });

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.querySelector('.profile-picture img');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    <?php include("./assets/include/Footer.php"); ?>
</body>
</html>
