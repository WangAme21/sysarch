<?php
    include('header.php');
    include('usersdb.php');
?>

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
        <a href="index.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<form action="editprofiledb.php" method="post">
    <div class="form-container">
        <div class="form-content">
            <div class="x-button-container">
                <button class="x-button" type="button" id="x-button">X</button>
            </div>
            <h1>Edit Profile</h1>
            <div>
                <input type="number" name="idno" readonly class="input-fields" placeholder="<?php echo $_SESSION['idno'];?>">
                <input type="text" name="lastname" class="input-fields" placeholder="lastname">
            </div>
            <div>
                <input type="text" name="firstname" class="input-fields" placeholder="firstname">
                <input type="text" name="middlename" class="input-fields" placeholder="middlename">
            </div>
            <div>
                <input type="email" name="email" class="input-fields" placeholder="email">
                <input type="text" name="course" class="input-fields" placeholder="course">
            </div>
            <div>
                <input type="number" name="level" class="input-fields" placeholder="level">
            </div>

            <input type="submit" class="input-field" value="Confirm" name="update-user">
        </div>
    </div>
</form>

<script>
    const xbutton = document.getElementById('x-button');
    const menuIcon = document.getElementById('menu-icon');
    const navLinks = document.getElementById('nav-links');

    xbutton.addEventListener("click", ()=>{
        window.location.href = "dashboard.php";
    });

    menuIcon.addEventListener("click", ()=>{        
        navLinks.classList.toggle("active");
    });
    
</script>