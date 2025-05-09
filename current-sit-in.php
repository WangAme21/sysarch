<?php
session_start();
include('db.php');

if (isset($_GET['logout_reservation_id'])) {
    $logout_reservation_id = $_GET['logout_reservation_id'];

    // Get the id_number associated with the reservation
    $queryUser = "SELECT id_number FROM reservations WHERE id = ?";
    $stmtUser = $connection->prepare($queryUser);
    $stmtUser->bind_param("i", $logout_reservation_id);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    if ($resultUser->num_rows > 0) {
        $user = $resultUser->fetch_assoc();
        $idNumber = $user['id_number'];

        // Decrease the session count for the user
        $decrementQuery = "UPDATE userstbl SET sessions = GREATEST(sessions - 1, 0) WHERE idno = ?";
        $stmtDecrement = $connection->prepare($decrementQuery);
        $stmtDecrement->bind_param("s", $idNumber);
        $stmtDecrement->execute();

        // Update the reservation status to 'Completed' or similar
        $updateReservation = "UPDATE reservations SET status = 'Completed' WHERE id = ?";
        $stmtUpdateReservation = $connection->prepare($updateReservation);
        $stmtUpdateReservation->bind_param("i", $logout_reservation_id);
        $stmtUpdateReservation->execute();

        // Get the lab and pc_number from the reservation
        $queryPCInfo = "SELECT lab, pc_number FROM reservations WHERE id = ?";
        $stmtPCInfo = $connection->prepare($queryPCInfo);
        $stmtPCInfo->bind_param("i", $logout_reservation_id);
        $stmtPCInfo->execute();
        $resultPCInfo = $stmtPCInfo->get_result();

        if ($resultPCInfo->num_rows > 0) {
            $pcInfo = $resultPCInfo->fetch_assoc();
            $lab = $pcInfo['lab'];
            $pcNumber = $pcInfo['pc_number'];

            // Update PC status back to 'available'
            $updatePCStatus = "UPDATE pcs SET status = 'available' WHERE lab = ? AND pc_number = ?";
            $stmtUpdatePC = $connection->prepare($updatePCStatus);
            $stmtUpdatePC->bind_param("si", $lab, $pcNumber);
            $stmtUpdatePC->execute();
        }

        $_SESSION['message'] = "Reservation logged out and session decremented.";
    } else {
        $_SESSION['message'] = "Error: Reservation not found.";
    }

    header("Location: current-sit-in.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>Current Sit-in</title>
</head>
<body>
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
        <a href="view-reservations.php"> Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin_computer_control.php">PC Control</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div>
</nav>

<center><h1>Current Sit in (Based on Approved Reservations)</h1></center>
<?php if (isset($_SESSION['message'])): ?>
    <div style="background-color:#dff0d8; padding:10px; text-align:center; color:#3c763d;">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>
<table>
    <thead>
        <tr>
            <th>ID Number</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Laboratory</th>
            <th>PC Number</th>
            <th>Time In</th>
            <th>Reservation Date</th>
            <th>Remaining Session</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM reservations WHERE status = 'Accepted' ORDER BY date DESC, time_in DESC";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '
                    <tr>
                        <td>'.$row['id_number'].'</td>
                        <td>'.$row['student_name'].'</td>
                        <td>'.$row['purpose'].'</td>
                        <td>'.$row['lab'].'</td>
                        <td>'.$row['pc_number'].'</td>
                        <td>'.$row['time_in'].'</td>
                        <td>'.$row['date'].'</td>
                        <td>'.$row['remaining_session'].'</td>
                        <td>'.$row['status'].'</td>
                        <td>
                            <button class="logoutbtn" onclick="logoutReservation('.$row['id'].')">Log out</button>
                        </td>
                    </tr>';
            }
        } else {
            echo '<tr><td colspan="10">No approved reservations found.</td></tr>';
        }
        ?>
    </tbody>
</table>

<div id="search-bg">
    <div class="search-container" style="display:none" id="search-container">
        <div class="search-content" method="get">
            <form action="searchdb.php">
                <div class="search-closebtn" id="search-closebtn">
                    <button onclick="searchclosebtn()" type="button">X</button>
                </div>
                <h1>Search ID number</h1>
                <input name="idnum" type="number" placeholder="Idno">
            </form>
        </div>
    </div>
</div>

</body>

<script>
    function searchFunction(){
        document.getElementById('search-container').style.display = "block";
        document.getElementById('search-bg').style.display = "block";
    }

    function searchclosebtn(){
        document.getElementById('search-closebtn').addEventListener("click", ()=> {
            document.getElementById('search-bg').style.display = "none";
            document.getElementById('search-container').style.display = "none";
        });
    }

    function logoutReservation(reservationId) {
        if(confirm("Are you sure you want to log out this reservation? This will decrement the student's session.")) {
            window.location.href = "current-sit-in.php?logout_reservation_id=" + reservationId;
        }
    }
</script>
</html>