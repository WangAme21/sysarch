<?php
// Simulating a list of labs (Lab1, Lab2, Lab3, Lab4)
$labs = [
    ['id' => 1, 'name' => 'Lab1'],
    ['id' => 2, 'name' => 'Lab2'],
    ['id' => 3, 'name' => 'Lab3'],
    ['id' => 4, 'name' => 'Lab4']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>Computer Control V2</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome for PC Icon -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .pc-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, 120px); 
            gap: 15px; 
            margin-top: 100px;
        }

        .pc-item {
            width: 120px; 
            height: 120px; 
            border: 1px solid #ccc; 
            text-align: center; 
            line-height: 30px;
            font-size: 14px; 
            cursor: pointer; 
            border-radius: 8px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: background-color 0.3s ease;
        }

        .available { background-color: #d4edda; }
        .in_use { background-color: #f8d7da; }
        .offline { background-color: #d6d8d9; }
        .maintenance { background-color: #fff3cd; }

        .status-label {
            font-size: 10px;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 2px 5px;
            border-radius: 4px;
            position: absolute;
            bottom: 5px;
            right: 5px;
        }

        .pc-number {
            font-size: 18px;
            font-weight: bold;
        }

        .pc-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .pc-item:hover {
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }

        h2{
            text-align: center;
        }

        #labSelect{
            position: absolute;
            left: 50%;
            top: 30%;
            transform: translate(-50%, -50%);
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
        <a href="view-reservations.php">Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin_computer_control.php">PC Control</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>
    <h2>Computer Control Panel</h2>

    <select id="labSelect" onchange="loadComputers()">
        <option value="">Select Lab</option>
        <?php foreach ($labs as $lab): ?>
            <option value="<?= $lab['id'] ?>"><?= htmlspecialchars($lab['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <div id="computers" class="pc-grid"></div>

    <script>
        function loadComputers() {
            const labId = document.getElementById("labSelect").value;
            if (!labId) return;

            // Simulating fetching 30 PCs for each lab (Lab1, Lab2, Lab3, Lab4), all set to 'offline'
            const totalPCs = 30;
            const computers = [];
            for (let i = 1; i <= totalPCs; i++) {
                computers.push({
                    id: i,
                    pc_number: i,
                    status: 'offline'  // Set all computers to "offline" by default
                });
            }

            const grid = document.getElementById("computers");
            grid.innerHTML = "";

            computers.forEach(pc => {
                const div = document.createElement("div");
                div.className = "pc-item " + pc.status;

                const pcNumber = document.createElement("div");
                pcNumber.className = "pc-number";
                pcNumber.innerText = "PC-" + pc.pc_number;

                const pcIcon = document.createElement("div");
                pcIcon.className = "pc-icon";
                pcIcon.innerHTML = "<i class='fas fa-desktop'></i>";  // FontAwesome PC Icon

                const statusLabel = document.createElement("div");
                statusLabel.className = "status-label";
                statusLabel.innerText = "Offline";  // Label for offline status

                div.appendChild(pcIcon);
                div.appendChild(pcNumber);
                div.appendChild(statusLabel);
                div.onclick = () => toggleSession(pc.id, pc.status);

                grid.appendChild(div);
            });
        }

        function toggleSession(pcId, currentStatus) {
            const action = currentStatus === "in_use" ? "end" : "start";
            const studentId = action === "start" ? prompt("Enter Student ID:") : null;
            if (action === "start" && !studentId) return;

            // Simulating toggling session (without backend in this example)
            console.log(`Toggling session for PC-${pcId}: ${action}, Student ID: ${studentId || "N/A"}`);
            loadComputers();  // Reload computers after toggling session
        }
    </script>
</body>
</html>
