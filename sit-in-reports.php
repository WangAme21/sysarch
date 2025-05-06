<?php
    session_start();
    include('db.php');

    // Query for purpose
    $query_purpose = "SELECT purpose, count(*) as number FROM userstbl GROUP BY purpose";
    $result_purpose = mysqli_query($connection, $query_purpose);

    // Query for labs
    $query_labs = "SELECT labs, count(*) as number FROM userstbl GROUP BY labs";
    $result_labs = mysqli_query($connection, $query_labs);

    // Query to fetch all sit-in records for export
    $query_all_records = "SELECT * FROM sit_in_records ORDER BY date_removed DESC";
    $result_all_records = mysqli_query($connection, $query_all_records);
    $all_records = mysqli_fetch_all($result_all_records, MYSQLI_ASSOC);
    mysqli_data_seek($result_all_records, 0); // Reset pointer for table display
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sit-in Records</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'charteditor']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            var data_purpose = google.visualization.arrayToDataTable([
                ['Purpose', 'Number'],
                <?php
                mysqli_data_seek($result_purpose, 0);
                while($row = mysqli_fetch_array($result_purpose)){
                    echo "['".$row["purpose"]."', ".$row["number"]. "], ";
                }
                ?>
            ]);

            var options_purpose = {
                title: 'Sit-in Purposes Distribution',
                is3D: true,
                pieSliceText: 'percentage',
                legend: { position: 'bottom', alignment: 'center' },
                tooltip: { isHtml: true, trigger: 'both' },
                pieSliceTextStyle: { color: 'black', fontSize: 16 },
                colors: ['#ff6f61', '#6b8e23', '#8a2be2', '#ff6347', '#20b2aa', '#ff1493'],
            };

            var chart_purpose = new google.visualization.PieChart(document.getElementById('piechart'));
            chart_purpose.draw(data_purpose, options_purpose);

            var data_labs = google.visualization.arrayToDataTable([
                ['Labs', 'Number'],
                <?php
                mysqli_data_seek($result_labs, 0);
                while($row = mysqli_fetch_array($result_labs)){
                    echo "['".$row["labs"]."', ".$row["number"]. "], ";
                }
                ?>
            ]);

            var options_labs = {
                title: 'Sit-in Labs Distribution',
                is3D: true,
                pieSliceText: 'percentage',
                legend: { position: 'bottom', alignment: 'center' },
                tooltip: { isHtml: true, trigger: 'both' },
                pieSliceTextStyle: { color: 'black', fontSize: 16 },
                colors: ['#ff6f61', '#6b8e23', '#8a2be2', '#ff6347', '#20b2aa', '#ff1493'],
            };

            var chart_labs = new google.visualization.PieChart(document.getElementById('piechart2'));
            chart_labs.draw(data_labs, options_labs);
        }
    </script>

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

        #piechart, #piechart2 {
            width: 100%;
            height: 400px;
            border-radius: 10px;
        }

        .pie-chart-space {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        table.dataTable {
            margin: 20px auto;
            width: 95%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        /* Style for the export buttons */
        .export-buttons {
            margin: 20px;
        }

        .export-buttons button {
            padding: 10px 15px;
            margin-right: 10px;
            background-color: #4CAF50; /* Green color */
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .export-buttons button:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .pie-chart-space {
                flex-direction: column;
                align-items: center;
            }
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
        <a href="view-sit-in-records.php"> Sit-in Records</a>
        <a href="sit-in-reports.php"> Sit-in Reports</a>
        <a href="feedback-reports.php"> Feedback Reports</a>
        <a href="view-reservations.php"> Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div>
</nav>

<div class="pie-chart-space">
    <div id="piechart-container">
        <div id="piechart"></div>
    </div>
    <div id="piechart-container">
        <div id="piechart2"></div>
    </div>
</div>

<div class="export-buttons">
    <button onclick="exportFullTableToCSV('sit_in_full_report.csv')">Export All to CSV</button>
    <button onclick="exportFullTableToExcel('sit_in_full_report.xls')">Export All to Excel</button>
    <button onclick="exportFullTableToPDF('sit_in_full_report.pdf')">Export All to PDF</button>
    <button onclick="exportFullTableToWord('sit_in_full_report.doc')">Export All to Word</button>
</div>

<table id="sitInTable" class="display">
    <thead>
        <tr>
            <th>Sit-in Number</th>
            <th>ID Number</th>
            <th>Name</th>
            <th>Purpose</th>
            <th>Lab</th>
            <th>Login</th>
            <th>Logout</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php
        while ($row = mysqli_fetch_assoc($result_all_records)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['student_id']}</td>
                    <td>{$row['student_name']}</td>
                    <td>{$row['purpose']}</td>
                    <td>{$row['lab']}</td>
                    <td>{$row['login_time']}</td>
                    <td>{$row['logout_time']}</td>
                    <td>{$row['date_removed']}</td>
                </tr>";
        }
    ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#sitInTable').DataTable({
            
        });

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

        if(confirmbtn){
            confirmbtn.addEventListener("click", ()=> {
                registerModal.style.display = "none";
            });
        }

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
    });

    // Function to trigger download with specified filename and data
    function downloadFile(filename, data, mimeType) {
        const blob = new Blob([data], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    // Function to export full table data to CSV
    function exportFullTableToCSV(filename) {
        const headers = Array.from(document.querySelectorAll('#sitInTable thead th')).map(th => th.innerText).join(',');
        const rows = Array.from(document.querySelectorAll('#sitInTable tbody tr')).map(row =>
            Array.from(row.querySelectorAll('td')).map(td => td.innerText).join(',')
        ).join('\n');
        const csvData = headers + '\n' + rows;
        downloadFile(filename, csvData, 'text/csv;charset=utf-8');
    }

    // Function to export full table data to Excel
    function exportFullTableToExcel(filename) {
        const headers = Array.from(document.querySelectorAll('#sitInTable thead th')).map(th => th.innerText).join('\t');
        const rows = Array.from(document.querySelectorAll('#sitInTable tbody tr')).map(row =>
            Array.from(row.querySelectorAll('td')).map(td => td.innerText).join('\t')
        ).join('\n');
        const excelData = headers + '\n' + rows;
        downloadFile(filename, excelData, 'application/vnd.ms-excel');
    }

    // Function to export full table data to PDF
    function exportFullTableToPDF(filename) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const head = [Array.from(document.querySelectorAll('#sitInTable thead th')).map(th => th.innerText)];
        const body = Array.from(document.querySelectorAll('#sitInTable tbody tr')).map(row =>
            Array.from(row.querySelectorAll('td')).map(td => td.innerText)
        );

        doc.autoTable({ head: head, body: body });
        doc.save(filename);
    }

    // Function to export full table data to Word
    function exportFullTableToWord(filename) {
        let tableHTML = `<table border="1"><thead><tr><th>${Array.from(document.querySelectorAll('#sitInTable thead th')).map(th => th.innerText).join('</th><th>')}</th></tr></thead><tbody>`;
        const rows = document.querySelectorAll('#sitInTable tbody tr');
        rows.forEach(row => {
            tableHTML += '<tr><td>' + Array.from(row.querySelectorAll('td')).map(td => td.innerText).join('</td><td>') + '</td></tr>';
        });
        tableHTML += '</tbody></table>';
        const blob = new Blob(['<html><head><meta charset="utf-8"></head><body>' + tableHTML + '</body></html>'], {
            type: 'application/msword'
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>
</body>
</html>