<?php
    session_start();
    include('db.php');
   /* $query = "SELECT purpose, count(*) as number FROM userstbl WHERE status = 'active' GROUP BY purpose";*/
    $query = "SELECT purpose, count(*) as number FROM userstbl GROUP BY purpose";
    $result = mysqli_query($connection, $query);

    /* students registered  */
    $queryTotalStudents = "SELECT idno, count(*) as total_students FROM userstbl";
    $resultTotalStudents = mysqli_query($connection, $queryTotalStudents);
    $rowTotalStudents = mysqli_fetch_assoc($resultTotalStudents);
    $totalStudents = $rowTotalStudents['total_students'];
    $_SESSION['totalstudents'] = $totalStudents;

    /* currently sit-in */
    $queryCurrentSitIn = "SELECT status, count(*) as currentNumber FROM userstbl WHERE status = 'active' GROUP BY status";
    $resultCurrentSitIn = mysqli_query($connection, $queryCurrentSitIn);
    $rowCurrentSitIn = mysqli_fetch_assoc($resultCurrentSitIn);
    $currentlySitIn = $rowCurrentSitIn['currentNumber'] - 1;
    $_SESSION['totalstudents'] = $currentlySitIn;


if(isset($_GET['idnum'])) {
    $idno = $_GET['idnum'];

    $query = "SELECT * FROM userstbl WHERE idno = '$idno'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $idno = $row['idno'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $sessions = $row['sessions'];

        $_SESSION['idno'] = $idno;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['sessions'] = $sessions;
    } else {
        echo "<script>alert('ID not found!'); window.location.href='admindashboard.php';</script>";
        exit();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <title>Document</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'charteditor']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Purpose', 'Number'],
            <?php
            while($row = mysqli_fetch_array($result)){
                echo "['".$row["purpose"]."', ".$row["number"]. "], ";      
            }
            ?>
        ]);

        var options = {
             // More descriptive title
            is3D: true,  // 3D chart for better visuals
            slices: {
                0: { offset: 0.1 },  // Slightly offset the first slice for emphasis
                1: { offset: 0.1 },  // Slightly offset other slices as well
            },
            pieSliceText: 'percentage',  // Display percentage in slices
            legend: { position: 'bottom', alignment: 'center' },  // Move legend to the bottom
            tooltip: {
                isHtml: true,  // Allow custom HTML in tooltips
                trigger: 'both',  // Show tooltips both on hover and on click
            },
            pieSliceTextStyle: {
                color: 'black',  // Make the text inside slices more readable
                fontSize: 16,
            },
            colors: ['#ff6f61', '#6b8e23', '#8a2be2', '#ff6347', '#20b2aa', '#ff1493'],  // Custom colors
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
</script>
</head>
<style>
    #piechart-container {
        width: 100%;
        max-width: 700px;
        margin: 50px auto; 
        padding: 20px;
        background: linear-gradient(135deg, #f0f8ff, #e0f7fa);
        border-radius: 15px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); 
        text-align: center;
        font-family: Arial, sans-serif;
    }

    #piechart-container h2 {
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
    }

    #piechart {
        width: 100%;
        height: 400px;
        border-radius: 10px;
    }

    .pie-announcement-container{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    body {
    background-color: #f4f6f8;
    margin: 0;
    padding: 0;
    color: #333;
}


textarea, input, select {
    border-radius: 6px;
    border: 1px solid #ccc;
    padding: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
    margin-top: 5px;
    margin-bottom: 10px;
}

.announcement-submitbtn {
    background-color: #06d6a0;
    border: none;
    color: white;
    padding: 10px 15px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.announcement-submitbtn:hover {
    background-color: #118a7e;
}

.announcement-card {
    background-color: white;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 12px;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.announcement-card:hover {
    transform: scale(1.01);
}

input[type="submit"] {
    background-color: #219ebc;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #023047;
}



    @media (max-width: 768px) {
        #piechart-container {
            padding: 15px;
        }

        #piechart {
            height: 300px;
        }
    }

    .new-announcement-box, .all-announcement-box {
    background-color: #ffffff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.08);
}

.announcement-list-scroll {
    max-height: 300px; /* adjust as needed */
    overflow-y: auto;
    margin-top: 10px;
    padding-right: 8px;
}



</style>



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
        <a href="view-reservations.php">Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="modal-container-reservation" id="sit-in" style="display:none">
    <div class="modal-content-reservation">
        <form action="sit-in-db.php" method="post">
            <div class="reservation-btn" id="reservation-btn">
                <input onclick="reservationClosebtn()" type="button" value="X">
            </div>
            <h1>Sit-in Form</h1>
                
                    <label>ID Number:</label>
                    <input type="idno" readonly value="<?php echo htmlspecialchars($idno);?>">
                
                    <label>Student Name: </label>
                    <input type="text" readonly value="<?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>">
                
                    <label>Purpose: </label>
                    <select name="purpose">
                        <option value="C Programming">C Programming</option>
                        <option value="Java Programming">Java Programming</option>
                        <option value="C++ Programming">C++ Programming</option>
                        <option value="C# Programming">C# Programming</option>
                        <option value="Php Programming">Php Programming</option>
                        <option value="Python Programming">Python Programming</option>
                    </select>
                
                    <label>Labs: </label>
                    <select name="labs">
                        <option value="524">524</option>
                        <option value="544">544</option>
                        <option value="542">542</option>
                        <option value="530">530</option>
                        <option value="528">528</option>
                        <option value="526">526</option>
                        <option value="MAC Laboratory">MAC Laboratory</option>
                    </select>
                
                    <label>Remaining Sessions: </label>
                    <input type="number" readonly value="<?php echo htmlspecialchars($sessions)?>">

                    <div class="sit-in-closebtn">
                        <input type="submit" name="sit-in" vaue="Submit">
                    </div>
        </form>
    </div>

</div>

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

<?php
    if(isset($_GET['success']) && $_GET['success'] == 1){
        echo "<script>
            window.onload = ()=>{
                const registerModal = document.getElementById('register-modal');    
                registerModal.style.display = 'block';
            };
        </script>";
    }
?>

    <div class="register-modal" style="display:none" id="register-modal">
        <div class="register-modal-content">
            <img src="assets/check.png" alt="check" class="check-logo">
            <h3>Success</h3>
            <p>Welcome to dashboard</p>
            <button class="confirm-btn" id="confirm-btn">Confirm</button>
        </div>
    </div>

    <div class="pie-announcement-container">
    <div id="piechart-container" style="position: relative;">
        <div id="piechart"></div> 
        <div style="position: absolute; top: 30%; left: 20%; transform: translate(-50%, -50%); font-size: 18px; font-weight: bold;">
            Students Registered: <?php echo $totalStudents; ?>
        </div>
        <div style="position: absolute; top: 40%; left: 20%; transform: translate(-50%, -50%); font-size: 16px; font-weight: bold;">
            Currently Sit-in: <?php echo $_SESSION['totalstudents']; ?>
        </div>
        <div style="position: absolute; top: 50%; left: 20%; transform: translate(-50%, -50%); font-size: 16px; font-weight: bold;">
            Total Sit-in: <?php echo $_SESSION['totalstudents']; ?>
        </div>
    </div>

    <div class="announcement-container-admin">
    <div class="student-info">
        <label>Announcement</label>
    </div>
    
    <h4>CCS Admin | <?php echo date("Y-m-d") ?></h4>

   <!-- NEW ANNOUNCEMENT SECTION -->
<div class="new-announcement-box">
    <h4>New Announcement</h4>
    <form action="announcementdb.php" method="post">
        <div class="announcement-form">
            <textarea style="resize: none" name="text" rows="4" cols="50" placeholder="Type your announcement..."></textarea><br>
            <input class="announcement-submitbtn" type="submit" name="update_announcement" value="Post Announcement">
        </div>
    </form>
</div>

<!-- ALL ANNOUNCEMENTS SECTION -->
<div class="all-announcement-box">
    <h4>All Announcements</h4>
    <div class="announcement-list-scroll">
        <?php
            $announcementQuery = "SELECT * FROM announcements ORDER BY created_at DESC";
            $announcementResult = mysqli_query($connection, $announcementQuery);
            while($row = mysqli_fetch_assoc($announcementResult)) {
        ?>
            <div class="announcement-card">
                <p><?php echo nl2br(htmlspecialchars($row['announcement_text'])); ?></p>
                <small><em>Posted on: <?php echo $row['created_at']; ?></em></small><br>
                <form action="javascript:void(0);" method="get" style="display: inline;" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['announcement_text']); ?>')">
                    <input type="button" value="Edit">
                </form>

                <form action="delete-announcement.php" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="submit" value="Delete">
                </form>
            </div>
        <?php } ?>
    </div>
</div>

    </div>
</div>

<div class="modal-container-edit" id="edit-modal" style="display:none;">
    <div class="modal-content-edit">
        <form action="edit-announcement.php" method="POST">
            <div class="modal-header">
                <span class="close-btn" onclick="closeEditModal()">×</span>
                <h2>Edit Announcement</h2>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="announcement-id">
                <textarea name="text" id="announcement-text" rows="5" cols="50" placeholder="Edit your announcement..."></textarea>
            </div>
            <div class="modal-footer">
                <input type="submit" name="update" value="Update Announcement">
            </div>
        </form>
    </div>
</div>



</body>

<script>
    const logoutbtn = document.getElementById('logoutbtn');
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.getElementById('nav-links');
    const homeNav = document.getElementById('home-nav');
    const confirmbtn = document.getElementById('confirm-btn');
    const registerModal = document.getElementById('register-modal');
    const sitIn = document.getElementById('sit-in');
    const searchBtn = document.getElementById('search-btn');
    const searchContainer = document.getElementById('search-container');
    const searchbg = document.getElementById('search-bg');
    const searchClosebtn = document.getElementById('search-closebtn');
    const reservationBtn = document.getElementById('reservation-btn');
    
    function openEditModal(id, text) {
    const modal = document.getElementById('edit-modal');
    const announcementId = document.getElementById('announcement-id');
    const announcementText = document.getElementById('announcement-text');
    
    // Set values to the modal form
    announcementId.value = id;
    announcementText.value = text;

    // Show the modal
    modal.style.display = 'flex';
}

function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    modal.style.display = 'none';
}



    function reservationClosebtn(){
        reservationBtn.addEventListener("click", ()=>{
            sitIn.style.display = "none";
        });
    }

    function searchclosebtn(){
        searchClosebtn.addEventListener("click", ()=> {
            searchbg.style.display = "none";
            searchContainer.style.display = "none";
        });
    }

    searchclosebtn();

    menuIcon.addEventListener("click", ()=>{
        navLinks.classList.toggle("active");
    });

    homeNav.addEventListener("click", ()=>{
        window.location.href = "admindashboard.php";
    });

    confirmbtn.addEventListener("click", ()=> {
        registerModal.style.display = "none";
    });
    
    function searchFunction(){
        searchBtn.addEventListener("click", ()=>{
            searchContainer.style.display = "block";
            searchbg.style.display = "block"
        });
    }

    searchFunction();

    window.onload = function(){
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('idnum')){
            sitIn.style.display = "block";
        }
    }
</script>
</html>
