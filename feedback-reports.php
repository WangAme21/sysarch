<?php
session_start();
include('db.php');

// Optional: Admin session validation here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>Feedback Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f2f2f2;
        }

        tr:hover {
            background: #f9f9f9;
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

<div id="search-bg">
    <div class="search-container" style="display:none" id="search-container">
        <div class="search-content" method="get">
            <form action="searchdb.php" >
                <div class="search-closebtn" id="search-closebtn">
                    <button onclick="searchclosebtn()" type="button">X</button>
                </div>
                <h1>Search ID number</h1>
                <input name="idnum" type="number" placeholder="Idno">
            </form>
        </div>
    </div>
</div>

<h1>User Feedback Reports</h1>

<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Laboratory</th>
            <th>Date</th>
            <th>Status</th>
            <th>Feedback</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM sit_in_history WHERE feedback IS NOT NULL AND feedback != '' ORDER BY date DESC";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $formattedDate = date("F j, Y, g:i a", strtotime($row['date']));
                echo "<tr>";
                echo "<td>{$row['idno']}</td>";
                echo "<td>{$row['laboratory']}</td>";
                echo "<td>$formattedDate</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['feedback']}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No feedback available.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
<script>

const searchBtn = document.getElementById('search-btn');
const searchContainer = document.getElementById('search-container');
const searchbg = document.getElementById('search-bg');
     function searchFunction(){
        searchBtn.addEventListener("click", ()=>{
            searchContainer.style.display = "block";
            searchbg.style.display = "block"
        });
    }

    searchFunction();
</script>
</html>
