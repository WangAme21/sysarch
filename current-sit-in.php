<?php
    session_start();
    include('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>
<body>
<nav>
    <h1>CCS Admin</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links-admin" id="nav-links">
        <a id="home-nav" href="admindashboard.php"> Home</a>
        <a href="#" onclick="searchFunction()" id="search-btn"> Search Students</a>
        <a href="current-sit-in.php"> Sit-in</a>
        <a href="view-sit-in-records.php"> View Sit-in Records</a>
        <a href="#"> Sit-in Reports</a>
        <a href="#"> Feedback Reports</a>   
        <a href="#"> Reservation</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<center><h1>Current Sit in</h1></center>
<table>
    <td clas="tablenav"></td>
    <thead>
    <tr>
        <th>ID Number</th>
        <th>Name</th>
        <th>Purpose</th>
        <th>Laboratory</th>
        <th>Login</th>
        <th>Logout</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
            <?php 
                $query = "SELECT * FROM userstbl";
                $result = mysqli_query($connection, $query);

                if($result){
                    while($row = mysqli_fetch_assoc($result)){
                        $idno = $row['idno'];
                        $firstname = $row['firstname'];
                        $lastname = $row['lastname'];
                        $purpose = $row['purpose'];
                        $labs = $row['labs'];  
                        echo ' 
                        <tr>
                            <td>'.$idno.'</th>
                            <td>'.$firstname.' '.$lastname.'</th>
                            <td>'.$purpose.'</th>
                            <td>'.$labs.'</td>
                        </tr>';
                    }
                }
            ?>
    </tbody>
</table>

<div class="modal-container-reservation" id="sit-in" style="display:none">
    <div class="modal-content-reservation">
        <form>
            <h1>Student Information</h1>
            
                <label>ID Number: <?php echo $_SESSION['idno'];?></label>
                <label>Student Name: <?php echo $_SESSION['firstname'];?> <?php echo $_SESSION['lastname']?></label>
                <label>Course: <?php echo $_SESSION['course'];?></label>
                <label>Year: <?php echo $_SESSION['level'];?></label>
                <label>Email Address: <?php echo $_SESSION['email'];?></label>
                <label>Remaining Sessions: <?php echo $_SESSION['sessions']?></label>
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
            <img src="check.png" alt="check" class="check-logo">
            <h3>Success</h3>
            <p>Welcome to dashboard</p>
            <button class="confirm-btn" id="confirm-btn">Confirm</button>
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
    const searchbg = document.getElementById('search-bg')
    const searchClosebtn = document.getElementById('search-closebtn');

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
