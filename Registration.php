<?php
include('./assets/include/db.php');

// Function to clean and validate user input
function clean_input($data)
{
    global $conn; // Access the database connection
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data); // Sanitize input for MySQL
}

// Initialize variables to store user input and error messages
$student_name = $email = $dob = $password = $contact_no = $gender = "";
$teacher_name = $module_name = $course_field = $hire_date = "";
$student_nameErr = $emailErr = $dobErr = $passwordErr = $contact_noErr = $genderErr = "";
$teacher_nameErr = $module_nameErr = $course_fieldErr = "";

// Fetch module names and course fields from the database
$module_names = [];
$sql_module = "SELECT module_name FROM module";
$result_module = $conn->query($sql_module);
if ($result_module->num_rows > 0) {
    while ($row = $result_module->fetch_assoc()) {
        $module_names[] = $row['module_name'];
    }
}

$module_names = [];
$sql_module = "SELECT module_id, module_name FROM module"; // Fetch both module_id and module_name
$result_module = $conn->query($sql_module);
if ($result_module->num_rows > 0) {
    while ($row = $result_module->fetch_assoc()) {
        $module_names[] = $row; // Store the full row to access both id and name
    }
}


$course_fields = [];
$sql_course = "SELECT sub_name FROM subject";
$result_course = $conn->query($sql_course);
if ($result_course->num_rows > 0) {
    while ($row = $result_course->fetch_assoc()) {
        $course_fields[] = $row['sub_name'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['student_submit'])) {
        $name = clean_input($_POST["student_name"]);
        $email = clean_input($_POST["email"]);
        $dob = clean_input($_POST["dob"]);
        $password = password_hash(clean_input($_POST["password"]), PASSWORD_BCRYPT);
        $gender = clean_input($_POST["gender"]);
        $module_id = clean_input($_POST["module_name"]);
        $user_type = 'student';

        // Validate inputs
        $errors = [];
        if (empty($name)) $errors[] = "Student name is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
        if (empty($dob)) $errors[] = "Date of birth is required.";
        if (empty($password)) $errors[] = "Password is required.";
        if (empty($gender)) $errors[] = "Gender is required.";
        if (empty($module_id)) $errors[] = "Module selection is required.";

        if (empty($errors)) {
            $user_query = "INSERT INTO users (name, email, dob, gender, password_hash, user_type) VALUES (?, ?, ?, ?, ?, ?)";
            $user_stmt = $conn->prepare($user_query);
            $user_stmt->bind_param("ssssss", $name, $email, $dob, $gender, $password, $user_type);

            if ($user_stmt->execute()) {
                $user_id = $conn->insert_id;
                
                $student_query = "INSERT INTO student_details (user_id, module_id) VALUES (?, ?)";
                $student_stmt = $conn->prepare($student_query);
                $student_stmt->bind_param("ii", $user_id, $module_id);
                
                if ($student_stmt->execute()) {
                    echo '<script>alert("Student registration successful!");</script>';
                    echo '<script>window.location.href="login.php";</script>';
                } else {
                    echo '<script>alert("Error: ' . $student_stmt->error . '");</script>';
                }
            } else {
                echo '<script>alert("Error: ' . $user_stmt->error . '");</script>';
            }
        } else {
            foreach ($errors as $error) echo '<p class="error">' . $error . '</p>';
        }
    }

    if (isset($_POST['teacher_submit'])) {
        $name = clean_input($_POST["teacher_name"]);
        $email = clean_input($_POST["email"]);
        $dob = clean_input($_POST["dob"]);
        $gender = clean_input($_POST["gender"]);

        // Fetch module name based on the module_id selected in form
        $module_id = clean_input($_POST["module_name"]);
        $module_name_query = "SELECT module_name FROM module WHERE module_id = ?";
        $module_stmt = $conn->prepare($module_name_query);
        $module_stmt->bind_param("i", $module_id);
        $module_stmt->execute();
        $module_result = $module_stmt->get_result();
        $module_name = $module_result->fetch_assoc()['module_name'];
        
        $course_field = clean_input($_POST["course_field"]);
        $password = password_hash(clean_input($_POST["password"]), PASSWORD_BCRYPT);
        $user_type = 'teacher';

        $errors = [];
        if (empty($name)) $errors[] = "Teacher name is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
        if (empty($dob)) $errors[] = "Date of birth is required.";
        if (empty($gender)) $errors[] = "Gender is required.";
        if (empty($module_name)) $errors[] = "Module name is required.";
        if (empty($course_field)) $errors[] = "Course field is required.";
        if (empty($password)) $errors[] = "Password is required.";

        if (empty($errors)) {
            $user_query = "INSERT INTO users (name, email, dob, gender, password_hash, user_type) VALUES (?, ?, ?, ?, ?, ?)";
            $user_stmt = $conn->prepare($user_query);
            $user_stmt->bind_param("ssssss", $name, $email, $dob, $gender, $password, $user_type);

            if ($user_stmt->execute()) {
                $user_id = $conn->insert_id;
                
                $hire_date = date("Y-m-d H:i:s");
                $status = "pending";
                $teacher_query = "INSERT INTO teacher_details (user_id, course_field, status, module_name, hire_date) VALUES (?, ?, ?, ?, ?)";
                $teacher_stmt = $conn->prepare($teacher_query);
                $teacher_stmt->bind_param("issss", $user_id, $course_field, $status, $module_name, $hire_date);
                
                if ($teacher_stmt->execute()) {
                    echo '<script>alert("Teacher registration successful!");</script>';
                    echo '<script>window.location.href="index.php";</script>';
                } else {
                    echo '<script>alert("Error: ' . $teacher_stmt->error . '");</script>';
                }
            } else {
                echo '<script>alert("Error: ' . $user_stmt->error . '");</script>';
            }
        } else {
            foreach ($errors as $error) echo '<p class="error">' . $error . '</p>';
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="./assets/css/registration.css">

</head>

<body class="mybg">
    <section>
        <h1>Future Study Hub</h1>
        <div class="main">
            <div class="mybg">
                <div class="tabs">
                    <button class="stu" onclick="openTab('student')">Student Registration</button>
                    <button class="tec" onclick="openTab('teacher')">Teacher Registration</button>
                </div>

                <div id="student" class="tab-content">
                    <form method="post" name="student_form" onsubmit="return validateStudentForm()" enctype="multipart/form-data">
                        <h3>Student Registration Form</h3>
                        <label for="student_name">Student Name :</label>
                        <input type="text" id="student_name" name="student_name" placeholder="Enter your full name" autocomplete="off" required>
                        <span class="error"><?php echo $student_nameErr; ?></span>

                        <label for="email">Email :</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your Email" id="email" required autocomplete="off">
                        <span class="error"><?php echo $emailErr; ?></span>

                        <label for="dob">Date of Birth :</label>
                        <input type="date" id="dob" name="dob" placeholder="Enter your DOB" autocomplete="off" max="<?php echo date('Y-m-d', strtotime('-14 years')); ?>" required>
                        <span class="error"><?php echo $dobErr; ?></span>



                        <label for="password">Password :</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="off" pattern="^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,}$" title="Password must contain at least one number, one alphabet, one symbol, and be at least 8 characters long" required>
                        <span class="error"><?php echo $passwordErr; ?></span>

                        <label for="gender">Gender :</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="error"><?php echo $genderErr; ?></span>

                        <label for="module">Module :</label>
                        <select id="module_name" name="module_name" required>
                            <option value="">Select Module</option>
                            <?php foreach ($module_names as $module) { ?>
                                <option value="<?php echo $module['module_id']; ?>"><?php echo $module['module_name']; ?></option>
                            <?php } ?>
                        </select>

                        <div class="wrap">
                            <button type="submit" name="student_submit">Submit</button>
                            <button class="home-button" onclick="location.href='index.php'">Home</button>
                        </div>
                    </form>
                </div>

                <div id="teacher" class="tab-content" style="display:none;">
                    <form method="post" name="teacher_form" onsubmit="return validateTeacherForm()" enctype="multipart/form-data">
                        <h3>Teacher Registration Form</h3>
                        <label for="teacher_name">Teacher Name :</label>
                        <input type="text" id="teacher_name" name="teacher_name" placeholder="Enter your full name" autocomplete="off" required>
                        <span class="error"><?php echo $teacher_nameErr; ?></span>

                        <label for="email">Email :</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your Email" id="email" required autocomplete="off">
                        <span class="error"><?php echo $emailErr; ?></span>

                        <label for="dob">Date of Birth :</label>
                        <input type="date" id="dob" name="dob" placeholder="Enter your DOB" autocomplete="off" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" required>
                        <span class="error"><?php echo $dobErr; ?></span>

                        <label for="gender">Gender :</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="error"><?php echo $genderErr; ?></span>

                        <label for="module_name">Module Name :</label>
                        <select id="module_name" name="module_name" required>
                            <option value="">Select Module</option>
                            <?php foreach ($module_names as $module) { ?>
                                <option value="<?php echo $module['module_id']; ?>"><?php echo $module['module_name']; ?></option>
                            <?php } ?>
                        </select>
                        <span class="error"><?php echo $module_nameErr; ?></span>

                        <label for="course_field">Course Field :</label>
                        <select id="course_field" name="course_field" required>
                            <option value="">Select Course Field</option>
                            <?php foreach ($course_fields as $course) { ?>
                                <option value="<?php echo $course; ?>"><?php echo $course; ?></option>
                            <?php } ?>
                        </select>
                        <span class="error"><?php echo $course_fieldErr; ?></span>

                        <label for="password">Password :</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="off" pattern="^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,}$" title="Password must contain at least one number, one alphabet, one symbol, and be at least 8 characters long" required>
                        <span class="error"><?php echo $passwordErr; ?></span>
                        <div class="wrap">
                            <button type="submit" name="teacher_submit">Submit</button>
                            <button class="home-button" onclick="location.href='index.php'">Home</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <p class="login"> Alredy Registered?
            <a class="login1" href="./login.php">
                Login here
            </a>
        </p>
        <script>
            function openTab(tabName) {
                var i;
                var x = document.getElementsByClassName("tab-content");
                for (i = 0; i < x.length; i++) {
                    x[i].style.display = "none";
                }
                document.getElementById(tabName).style.display = "block";
            }

            function validateStudentForm() {
                // Basic client-side validation for student form
                var studentName = document.forms["student_form"]["student_name"].value;
                var email = document.forms["student_form"]["email"].value;
                var dob = document.forms["student_form"]["dob"].value;
                var password = document.forms["student_form"]["password"].value;
                var gender = document.forms["student_form"]["gender"].value;

                if (studentName == "") {
                    alert("Student name must be filled out");
                    return false;
                }

                if (email == "") {
                    alert("Email must be filled out");
                    return false;
                }

                if (dob == "") {
                    alert("Date of birth must be filled out");
                    return false;
                }

                if (password == "") {
                    alert("Password must be filled out");
                    return false;
                }

                if (gender == "") {
                    alert("Gender must be selected");
                    return false;
                }

                return true;
            }

            function validateTeacherForm() {
                // Basic client-side validation for teacher form
                var teacherName = document.forms["teacher_form"]["teacher_name"].value;
                var email = document.forms["teacher_form"]["email"].value;
                var dob = document.forms["teacher_form"]["dob"].value;
                var gender = document.forms["teacher_form"]["gender"].value;
                var moduleName = document.forms["teacher_form"]["module_name"].value;
                var courseField = document.forms["teacher_form"]["course_field"].value;
                var password = document.forms["teacher_form"]["password"].value;

                if (teacherName == "") {
                    alert("Teacher name must be filled out");
                    return false;
                }

                if (email == "") {
                    alert("Email must be filled out");
                    return false;
                }

                if (dob == "") {
                    alert("Date of birth must be filled out");
                    return false;
                }

                if (gender == "") {
                    alert("Gender must be selected");
                    return false;
                }

                if (moduleName == "") {
                    alert("Module name must be filled out");
                    return false;
                }

                if (courseField == "") {
                    alert("Course field must be filled out");
                    return false;
                }

                if (password == "") {
                    alert("Password must be filled out");
                    return false;
                }

                return true;
            }
        </script>
    </section>
</body>

</html>