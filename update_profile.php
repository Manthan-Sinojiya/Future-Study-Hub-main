<?php
session_start();
include_once("./assets/include/db.php");
include_once("./assets/include/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the posted values
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_name = $_FILES['profile_image']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate the file extension
        if (in_array(strtolower($file_ext), $allowed_ext)) {
            // Generate a unique file name
            $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
            $upload_dir = './uploads/profile_pictures/'; // Make sure this directory is writable

            // Move the uploaded file to the desired location
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $profile_picture = $upload_dir . $new_file_name;
            } else {
                echo "Failed to upload file.";
            }
        } else {
            echo "Invalid file type.";
        }
    }

    // Update the profile in the database
    $sql = "UPDATE users SET name = ?, email = ?, dob = ?, gender = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $dob, $gender, $user_id);
    if ($stmt->execute()) {
        // Now update the profile picture if available
        if ($profile_picture) {
            $sql_i = "UPDATE student_details SET profile_image = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql_i);
            $stmt->bind_param("si", $profile_picture, $user_id);
            $stmt->execute();
        }
        // Redirect or show success message
        header("Location: profile.php");
        exit;
    } else {
        echo "Error updating profile.";
    }
}
?>
