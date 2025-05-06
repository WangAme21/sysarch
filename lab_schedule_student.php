<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'usersdb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>Lab Schedules</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 75px;
        }

        nav h1 {
            margin: 0;
            font-size: 22px;
        }

        .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
            font-size: 14px;
        }

        .schedule-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            font-size: 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        .card {
            background: white;
            padding: 15px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card h3 {
            margin: 0 0 6px 0;
            color: #333;
            font-size: 16px;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin-top: 10px;
        }

        .download-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            text-align: center;
            margin-top: auto;
        }

        .download-btn:hover {
            background-color: #0056b3;
        }

        .date {
            color: #666;
            font-size: 13px;
            margin: 6px 0;
        }

        .menu-icon {
            display: none;
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .menu-icon {
                display: block;
                font-size: 24px;
                cursor: pointer;
            }

            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
<nav>
    <h1>Dashboard</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links" id="nav-links">
        <a href=""> Notification</a>
        <a id="home-nav" href="dashboard.php"> Home</a>
        <a href="editprofile.php"> Edit Profile</a>
        <a href="sitin-history.php">Sit-in History</a>
        <a href="reservation.php"> Reservation</a>
        <a href="lab_schedule_student.php">Lab Schedules</a>
        <a href="lab_resources_view.php">Lab Resources</a>
        <a href="#" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="schedule-container">
    <div class="header">
        📅 Lab Schedules
    </div>

    <div class="grid">
    <?php
    // Query the active lab schedules
    $result = $conn->query("SELECT * FROM lab_schedules WHERE status = 'active' ORDER BY start_date DESC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $title = htmlspecialchars($row['title']);
            $start = date("F j, Y", strtotime($row['start_date']));
            $end = date("F j, Y", strtotime($row['end_date']));
            $file = $row['file_path']; // Correct the path
            $fileName = htmlspecialchars(basename($file)); // Get the file name
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); // Get the file extension

            echo '<div class="card">';
            echo "<h3>{$title}</h3>";
            echo "<div class='date'>📅 {$start} - {$end}</div>";

            // Display image if the file is an image (jpg, jpeg, png, gif)
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<img src='{$file}' alt='Schedule Image'>";
            } else {
                echo "<p><strong>File:</strong> {$fileName}</p>";
            }

            // Provide a download button for the schedule file
            echo "<a class='download-btn' href='{$file}' download>⬇ Download</a>";
            echo '</div>';
        }
    } else {
        echo '<div class="card">No schedules available.</div>';
    }
    ?>
    </div>
</div>

</body>
</html>
