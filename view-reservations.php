<?php
session_start();
include('db.php');

// Handle search
$search = $_GET['search'] ?? '';
$searchQuery = '';
if (!empty($search)) {
    $searchSafe = mysqli_real_escape_string($connection, $search);
    $searchQuery = "WHERE user_id LIKE '%$searchSafe%' OR student_name LIKE '%$searchSafe%' OR lab LIKE '%$searchSafe%' OR purpose LIKE '%$searchSafe%'";
}

$query = "SELECT * FROM reservations $searchQuery ORDER BY id DESC";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reservations</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            background-color: #f3f7f9;
            font-family:  sans-serif;
        }
        h2 {
            text-align: center;
            margin: 20px 0;
        }
        .table-container {
            max-width: 95%;
            margin: auto;
            overflow-x: auto;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            background-color: white;
            border-radius: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: center;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 300px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-btn {
            padding: 8px 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 5px;
            cursor: pointer;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination button {
            padding: 5px 15px;
            margin: 2px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION['message'])): ?>
    <div style="background-color:#dff0d8; padding:10px; text-align:center; color:#3c763d;">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<nav>
    <h1>CCS Admin</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links-admin" id="nav-links">
        <a id="home-nav" href="admindashboard.php"> Home</a>
        <a href="#" onclick="searchFunction()" id="search-btn"> Search Students</a>
        <a href="current-sit-in.php"> Sit-in</a>
        <a href="view-sit-in-records.php">Sit-in Records</a>
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


<h2>View Reservation</h2>

<div class="search-bar">
    <form method="get">
        <input type="text" name="search" placeholder="Search by ID, name, lab, or purpose..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="search-btn">Search</button>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Student Name</th>
                <th>Lab</th>
                <th>Reservation Date</th>
                <th>Time In</th>
                <th>Purpose</th>
                <th>Remaining Session</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['id_number']}</td>";
                echo "<td>{$row['student_name']}</td>";
                echo "<td>{$row['lab']}</td>";
                echo "<td>{$row['date']}</td>";
                echo "<td>{$row['time_in']}</td>";
                echo "<td>{$row['purpose']}</td>";
                echo "<td>{$row['remaining_session']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>
    <form method='post' action='update-reservation-status.php' style='display:inline;'>
        <input type='hidden' name='id' value='" . $row['id'] . "'>
        <input type='hidden' name='status' value='Accepted'>
        <button type='submit' style='background-color:#4CAF50; color:white; border:none; padding:5px 10px; border-radius:4px;'>Approve</button>
    </form>
    <form method='post' action='update-reservation-status.php' style='display:inline;'>
        <input type='hidden' name='id' value='" . $row['id'] . "'>
        <input type='hidden' name='status' value='Declined'>
        <button type='submit' style='background-color:#e74c3c; color:white; border:none; padding:5px 10px; border-radius:4px;'>Decline</button>
    </form>
</td>";

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No reservations found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <button>1</button> <!-- placeholder for pagination -->
</div>

</body>
</html>
