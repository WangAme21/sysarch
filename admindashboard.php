<?php
    session_start();
    include('db.php');
    $query = "SELECT purpose, count(*) as number FROM userstbl GROUP BY purpose";
    $result = mysqli_query($connection, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
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
            title: 'Sit-in Purposes Distribution',  // More descriptive title
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

    @media (max-width: 768px) {
        #piechart-container {
            padding: 15px;
        }

        #piechart {
            height: 300px;
        }
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
        <a href="view-sit-in-records.php"> View Sit-in Records</a>
        <a href="#"> Sit-in Reports</a>
        <a href="#"> Feedback Reports</a>   
        <a href="#"> Reservation</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="modal-container-reservation" id="sit-in" style="display:none">
    <div class="modal-content-reservation">
        <form action="sit-in-db.php" method="post">
            <h1>Sit-in Form</h1>
                
                    <label>ID Number:</label>
                    <input type="idno" readonly value="<?php echo $_SESSION['idno'];?>">
                
                    <label>Student Name: </label>
                    <input type="text" readonly value="<?php echo $_SESSION['firstname'];?> <?php echo $_SESSION['lastname']?>">
                
                    <label>Purpose: </label>
                    <select name="purpose">
                        <option value="C Programming">C Programming</option>
                        <option value="Java Programming">Java Programming</option>
                        <option value="C++ Programming">C++ Programming</option>
                    </select>
                
                    <label>Labs: </label>
                    <select name="labs">
                        <option value="524">524</option>
                        <option value="544">544</option>
                        <option value="542">542</option>
                        <option value="MAC Laboratory">MAC Laboratory</option>
                    </select>
                
                    <label>Remaining Sessions: </label>
                    <input type="number" readonly value="<?php echo $_SESSION['sessions']?>">

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
            <img src="check.png" alt="check" class="check-logo">
            <h3>Success</h3>
            <p>Welcome to dashboard</p>
            <button class="confirm-btn" id="confirm-btn">Confirm</button>
        </div>
    </div>

    <div class="pie-announcement-container">
    <div id="piechart-container">
        <div id="piechart"></div> 
    </div>

    <div class="announcement-container-admin">
        <div class="student-info">
            <label>Announcement</label>
        </div>
        <!-- Display current announcement -->
        <h4>CCS Admin | <?php echo date("Y-m-d") ?></h4>
        <p></p>

        <form action="" method="">
            <h4>Edit Announcement</h4>
            <textarea name="" rows="4" cols="50"></textarea><br>
            <button type="submit" name="t">Update Announcement</button>
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
