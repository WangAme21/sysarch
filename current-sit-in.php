<?php
session_start();
include('db.php');
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
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<center><h1>Current Sit in</h1></center>
<table>
    <thead>
        <tr>
            <th>ID Number</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Laboratory</th>
            <th>Session</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $hasData = false;

        // Show currently seated-in students
        if(isset($_SESSION['sit_in_students']) && count($_SESSION['sit_in_students']) > 0) {
            $id_list = implode("','", $_SESSION['sit_in_students']);
            $query = "SELECT * FROM userstbl WHERE idno IN ('$id_list')";
            $result = mysqli_query($connection, $query);

            if($result && mysqli_num_rows($result) > 0) {
                $hasData = true;
                while($row = mysqli_fetch_assoc($result)) {
                    echo ' 
                        <tr>
                            <td>'.$row['idno'].'</td>
                            <td>'.$row['firstname'].' '.$row['lastname'].'</td>
                            <td>'.$row['purpose'].'</td>
                            <td>'.$row['labs'].'</td>
                            <td>'.$row['sessions'].'</td>
                            <td>'.$row['status'].'</td>
                            <td><button class="removebtn" onclick="removeStudent(\''.$row['idno'].'\')">Log out</button></td>
                        </tr>';
                }
            }
        }

        if (!$hasData) {
            echo '<tr><td colspan="7">No students currently seated in.</td></tr>';
        }
        ?>
    </tbody>
</table>

<!-- Search Modal -->
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
    function removeStudent(id) {
        if(confirm("Are you sure you want to remove this student?")) {
            window.location.href = "remove_student.php?id=" + id;
        }
    }

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
</script>
</html>
