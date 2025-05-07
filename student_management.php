<?php
session_start();
include('db.php');

// Handle search
$search = $_GET['search'] ?? '';
$searchQuery = '';
if (!empty($search)) {
    $searchSafe = mysqli_real_escape_string($connection, $search);
    $searchQuery = "WHERE idno LIKE '%$searchSafe%' OR CONCAT(firstname, ' ', lastname) LIKE '%$searchSafe%'";
}

$query = "SELECT * FROM userstbl $searchQuery ORDER BY idno ASC";
$result = mysqli_query($connection, $query);

// Top Points and Time Spent - adjust based on your DB structure
$topPointsQuery = "SELECT * FROM userstbl ORDER BY points DESC LIMIT 1";
$topPointsResult = mysqli_query($connection, $topPointsQuery);
$topPoints = mysqli_fetch_assoc($topPointsResult);

$topTimeQuery = "SELECT * FROM userstbl ORDER BY login_date DESC LIMIT 1";
$topTimeResult = mysqli_query($connection, $topTimeQuery);
$topTime = mysqli_fetch_assoc($topTimeResult);

// Handle points update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_points'])) {
    $idno = $_POST['idno'];
    $points = $_POST['points'];

    // Ensure points is a number
    if (is_numeric($points)) {
        // Update points
        $updatePointsQuery = "UPDATE userstbl SET points = points + ? WHERE idno = ?";
        $stmt = $connection->prepare($updatePointsQuery);
        $stmt->bind_param("is", $points, $idno);
        $stmt->execute();

        // Fetch the user's new points and session info
        $sessionUpdateQuery = "SELECT points, claimed_rewards, sessions FROM userstbl WHERE idno = ?";
        $stmt = $connection->prepare($sessionUpdateQuery);
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $stmt->bind_result($totalPoints, $claimedRewards, $sessions);
        $stmt->fetch();
        $stmt->close();

        // Calculate newly earned rewards based on the total points
        $newRewards = floor($totalPoints / 3);
        $newlyEarned = $newRewards - $claimedRewards;

        // Add new sessions if the user has earned any new rewards
        if ($newlyEarned > 0) {
            $sessions += $newlyEarned;
            $claimedRewards = $newRewards;

            // Update sessions and claimed rewards in the database
            $sessionUpdateQuery = "UPDATE userstbl SET sessions = ?, claimed_rewards = ? WHERE idno = ?";
            $stmt = $connection->prepare($sessionUpdateQuery);
            $stmt->bind_param("iis", $sessions, $claimedRewards, $idno);
            $stmt->execute();
        }

        // Redirect to avoid form resubmission
        header("Location: student_management.php");
        exit();
    } else {
        $errorMessage = "Points must be a valid number.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">    
    <title>Student Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f7f9;
        }
        .header {
            font-size: 24px;
            margin-bottom: 15px;
        }
        .top-boxes {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .box {
            flex: 1;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .search-bar {
            margin: 20px 0;
        }
        input[type="text"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn.danger {
            background-color: #e74c3c;
            text-decoration: none;
        }
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            border-radius: 6px;
        }
        th, td {
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .points-input {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<nav>
    <h1>CCS Admin</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links-admin" id="nav-links">
        <a id="home-nav" href="admindashboard.php"> Home</a>
        <a href="#" onclick="searchFunction()" id="search-btn"> Search Students</a>
        <a href="current-sit-in.php"> Sit-in</a>
        <a href="view-sit-in-records.php"> Sit-in Records</a>
        <a href="sit-in-reports.php"> Sit-in Reports</a>   
        <a href="feedback-reports.php"> Feedback Reports</a>   
        <a href="view-reservations.php">Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin_computer_control.php">PC Control</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="header">Student Management</div>

<div class="top-boxes">
    <div class="box">
        <strong>Top Points Earner</strong><br>
        <?= $topPoints ? $topPoints['firstname'] . " " . $topPoints['lastname'] . " - Sessions: " . $topPoints['sessions'] : "No points data available" ?>
    </div>
    <div class="box">
        <strong>Top Time Spent</strong><br>
        <?= $topTime ? $topTime['firstname'] . " " . $topTime['lastname'] . " - Last Login: " . $topTime['login_date'] : "No time data available" ?>
    </div>
</div>

<div class="search-bar">
    <form method="get">
        <input type="text" name="search" placeholder="Enter student ID or name" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn">Search</button>
        <a href="reset-all-sessions.php" class="btn danger" onclick="return confirm('Reset all sessions for all students?')">Reset All Active Sessions</a>
        <a href="reset-all-points.php" class="btn danger" onclick="return confirm('Reset points for all students?')">Reset All Points</a>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Year</th>
            <th>Points</th>
            <th>Total Sessions</th>
            <th>Active Sessions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['idno'] ?></td>
            <td><?= $row['firstname'] . " " . $row['lastname'] ?></td>
            <td><?= $row['course'] ?></td>
            <td><?= $row['level'] ?></td>
            <td><?= $row['points'] ?></td>
            <td><?= $row['sessions'] ?></td>
            <td><?= $row['labs'] ?? 0 ?></td>
            <td>
                <form method="post" action="reset-student-session.php" style="display:inline;">
                    <input type="hidden" name="idno" value="<?= $row['idno'] ?>">
                    <button type="submit" class="btn danger">Reset Sessions</button>
                </form>
                <a href="view-student-sessions.php?idno=<?= $row['idno'] ?>" class="btn">View Sessions</a>

                <form method="post" action="student_management.php" style="display:inline;">
                    <input type="hidden" name="idno" value="<?= $row['idno'] ?>">
                    <input type="number" name="points" class="points-input" placeholder="Points" required min="1">
                    <button type="submit" name="update_points" class="btn">Update Points</button>
                </form>

                <form method="post" action="reset-student-points.php" style="display:inline;">
                    <input type="hidden" name="idno" value="<?= $row['idno'] ?>">
                    <button type="submit" name="reset_points" class="btn danger">Reset Points</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php if (isset($errorMessage)): ?>
    <p style="color: red;"><?= $errorMessage ?></p>
<?php endif; ?>

</body>
</html>
