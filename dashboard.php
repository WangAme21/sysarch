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
    <title>Document</title>
</head>
<body>
<nav>
    <h1>Dashboard</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links" id="nav-links">
        <a href=""> Notification</a>
        <a id="home-nav" href="#"> Home</a>
        <a href="editprofile.php"> Edit Profile</a>
        <a href="sitin-history.php">Sit-in History</a>
        <a href="reservation.php"> Reservation</a>
        <a href="lab_schedule_student.php">Lab Schedules</a>
        <a href="lab_resources_view.php">Lab Resources</a>
        <a href="view_points.php">View Points</a>
        <a href="#" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="student-info-container">
    <div class="student-info-content">
        <div class="student-info">
            <label>Student Information</label>
        </div> 
            <img src="assets/logo.jpeg" alt="logo" class="profile-pic">
            <label>Name: <?php echo $_SESSION['firstname'] ?? 'N/A'; ?> <?php echo $_SESSION['lastname'] ?? '';?></label>
            <label>Course: <?php echo $_SESSION['course'];?></label>
            <label>Year: <?php echo $_SESSION['level'];?></label>
            <label>Email Address: <?php echo $_SESSION['email'];?></label>
            <label>Session: <?php echo isset($_SESSION['sessions']) ? $_SESSION['sessions'] : 'Not set'; ?></label>
    </div>
    
    <div class="announcement-container">
        <div class="student-info">
            <label>Announcement</label>
        </div>

        <?php
            $query = "SELECT * FROM announcements ORDER BY created_at DESC";
            $result = mysqli_query($connection, $query);

            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<h4>CCS Admin | " . date("Y-m-d H:i:s", strtotime($row['created_at'])) . "</h4>";
                    echo "<p>" . htmlspecialchars($row['announcement_text']) . "</p>";
                    echo "<p>_______________________________________________</p>";
                }
            } else {
                echo "<p>No announcements available</p>";
            }
        ?>
    </div>

    
    <div class="rules-content">
        <div class="student-info">
            <label>Rules and Regulation</label>
        </div>
        <div class="rules-title">
            <h2>University of Cebu</h2>
            <h3>COLLEGE OF INFORMATION & COMPUTER STUDIESS</h3>
        </div>
        <div>
            <h3>LABORATORY RULES AND REGULATIONS</h3>
            <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            <p>1. Maintain silence, proper decorum, and discipline inside the laboratory, Mobile phones, walkmans and other personal pieces of equipment must be switched off.</p>
            <p>2. Games are not allowed inside the lab. This includes computer-related games, card games and other games that may disturb the operation of the lab</p>
            <p>3. Surfing the Internet is allowed only with the permission of the Instructor. Downloading and installing of software are strictly prohibited.</p>
            <p>4. Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohbited.</p>
            <p>5. Deleting computer files and changing the set-up of the computer is a major offense.</p>
            <p>6. Observe computer time usage carefully. A fifteen-minute allowance is given for each use. Otherwise, the unit will be given to those who wish to "sit-in".</p>
            <p>7. Observe proper decorum while Inside the laboratory.</p>
            <p class="letters">a. Do not get inside the lab unless the instructor is present.</p>
            <p class="letters">b. All the bags, knapsacks, and the likes must be deposited at the counter</p>
            <p class="letters">c. Follow the seating arrangement of your instructor.</p>
            <p class="letters">d. At the end of class, all software programs must be closed.</p>
            <p class="letters">e. Return all chains to their proper places after using.</p>
            <p>8. Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</p>
            <p>9. Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</p>
            <p>10. Persons exhibiting hostile or threatening behavior such as yelling, swearing or disregarding requests made by lab personnel will be asked to leave the lab.</p>
            <p>11. For serious offense, the lab personnel may call the Civil Security Office (CSU) for assistance</p>
            <p>12. Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant or instructor immediately.</p>
        </div>
    </div>
</div>

<?php
    if(isset($_GET['success']) && $_GET['success'] = 1){
        echo "<script>
            window.onload = ()=>{
                const registerModal = document.getElementById('register-modal');    
                registerModal.style.display = 'block';
            };
        </script>";
    }
?>


<div id="background">
    <div class="register-modal" style="display:none" id="register-modal">
        <div class="register-modal-content">
            <img src="assets/check.png" alt="check" class="check-logo">
            <h3>Success</h3>
            <p>Welcome to dashboard</p>
            <button class="confirm-btn" id="confirm-btn">Confirm</button>
        </div>
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
    const backgroundModal = document.getElementById('background');

    logoutbtn.addEventListener("click", ()=> {
        window.location.href = "index.php";
    });

    menuIcon.addEventListener("click", ()=>{
        navLinks.classList.toggle("active");
    });

    homeNav.addEventListener("click", ()=>{
        if(localStorage.getItem("modalConfirmed") == "true"){
            backgroundModal.style.display = "none";
            registerModal.style.display = "none";
        }
        
        window.location.href = "dashboard.php";
    });

    confirmbtn.addEventListener("click", ()=> {
        backgroundModal.style.display = "none";
        registerModal.style.display = "none";

        localStorage.setItem("modalConfirmed", "true");
    });

</script>
</html>
