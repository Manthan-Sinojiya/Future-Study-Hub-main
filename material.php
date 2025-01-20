<?php
session_start();
ob_start();
require_once('./assets/include/db.php');
require_once("./assets/include/Header.php"); // Adjust the path as needed

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("location:./login.php");
    exit;
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400">

    <title>Material</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .material-section {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;

        }

        .material-container {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            width: 750px;
            text-align: center;
        }

        .material-header h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .material-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .material-table th,
        .material-table td {
            padding: 10px;
            text-align: left;
        }

        .material-table th {
            background-color: #f9f9f9;
            color: #333;
            font-weight: 600;
        }

        .material-table td {
            background-color: #f9f9f9;
            border-bottom: 2px solid #ddd;
        }

        .material-name {
           
            align-items: center;
            gap: 10px;
        }

        .material-name::before {
            content: '';
            display: inline-block;
            width: 15px;
            height: 15px;
            background-color: #a5d6a7;
            /* Use different colors here to match icons */
            border-radius: 10px;
                
            
        }

        .material-download a {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .material-download a:hover {
            background-color: #45a049;
        }

        
    </style>
    
</head>

<body class="mybg">
    <section class="material-section">
        <div class="material-container">
            <div class="material-header">
                <h2>Your Materials Are Ready to Download ✌️</h2>
            </div>
            <table class="material-table">
                <thead>
                    <tr>
                        <th>Material Name</th>
                        <th>Description</th>
                        <th>Download Material</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM material";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td class='material-name'>" . htmlspecialchars($row['material_name']) . "</td>
                                <td class='material-desc'>" . htmlspecialchars($row['description']) . "</td>
                                <td class='material-download'><a href='../Teacher/" . htmlspecialchars(urlencode($row['pdf_path'])) . "' download>Download</a></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No materials available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
        </div>
    </section>
    <?php include("./assets/include/Footer.php"); ?>
</body>

</html>