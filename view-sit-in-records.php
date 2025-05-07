<?php
    session_start();
    include('db.php');

    $query_purpose = "SELECT purpose, count(*) as number FROM userstbl GROUP BY purpose";
    $result_purpose = mysqli_query($connection, $query_purpose);

    $query_labs = "SELECT labs, count(*) as number FROM userstbl GROUP BY labs";
    $result_labs = mysqli_query($connection, $query_labs);

    $query_records = "SELECT * FROM sit_in_records ORDER BY date_removed DESC";
    $result_records = mysqli_query($connection, $query_records);
    $all_records = mysqli_fetch_all($result_records, MYSQLI_ASSOC);
    mysqli_data_seek($result_records, 0); // Reset the result set pointer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>View Sit-in Records</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <style>
        .export-buttons {
            margin: 20px;
        }

        .export-buttons button {
            padding: 10px 15px;
            margin-right: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }

        .export-buttons button:hover {
            background-color: #45a049;
        }

        #searchInput {
            margin: 10px 20px;
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<nav>
    <h1>CCS Admin</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links-admin" id="nav-links">
        <a href="admindashboard.php">Home</a>
        <a href="#" onclick="searchFunction()" id="search-btn">Search Students</a>
        <a href="current-sit-in.php">Sit-in</a>
        <a href="view-sit-in-records.php">Sit-in Records</a>
        <a href="sit-in-reports.php">Sit-in Reports</a>
        <a href="feedback-reports.php">Feedback Reports</a>
        <a href="view-reservations.php">Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin_computer_control.php">PC Control</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn">Log out</a>
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

<input type="text" id="searchInput" placeholder="Search by ID or Name...">

<div class="export-buttons">
    <button onclick="exportTableToCSV('sit_in_full_records.csv')">Export to CSV</button>
    <button onclick="exportTableToExcel('sit_in_full_records.xls')">Export to Excel</button>
    <button onclick="exportTableToPDF()">Export to PDF</button>
    <button onclick="exportToWord()">Export to Word</button>
    <button onclick="exportAllToCSV('sit_in_full_records.csv')">Export All Records (CSV)</button>
</div>

<table>
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
        while ($row = mysqli_fetch_assoc($result_records)) {
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
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById("searchInput");
    const tableRows = document.querySelectorAll("table tbody tr");

    searchInput.addEventListener("keyup", function () {
        const input = this.value.toLowerCase();

        tableRows.forEach(row => {
            const idCell = row.cells[1]; // ID Number is the second column (index 1)
            const nameCell = row.cells[2]; // Name is the third column (index 2)

            if (idCell && nameCell) {
                const idText = idCell.innerText.toLowerCase();
                const nameText = nameCell.innerText.toLowerCase();
                row.style.display = (idText.includes(input) || nameText.includes(input)) ? "" : "none";
            } else {
                console.error("Error: Could not find ID or Name cell in row:", row);
            }
        });
    });
});

google.charts.load('current', {'packages':['corechart', 'charteditor']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
    var data_purpose = google.visualization.arrayToDataTable([
        ['Purpose', 'Number'],
        <?php
        mysqli_data_seek($result_purpose, 0);
        while($row = mysqli_fetch_array($result_purpose)){
            echo "['".$row["purpose"]."', ".$row["number"]."], ";
        }
        ?>
    ]);

    var options_purpose = {
        title: 'Sit-in Purposes Distribution',
        is3D: true,
        pieSliceText: 'percentage',
        legend: { position: 'bottom' },
        colors: ['#ff6f61', '#6b8e23', '#8a2be2']
    };

    var chart_purpose = new google.visualization.PieChart(document.getElementById('piechart'));
    chart_purpose.draw(data_purpose, options_purpose);

    var data_labs = google.visualization.arrayToDataTable([
        ['Labs', 'Number'],
        <?php
        mysqli_data_seek($result_labs, 0);
        while($row = mysqli_fetch_array($result_labs)){
            echo "['".$row["labs"]."', ".$row["number"]."], ";
        }
        ?>
    ]);

    var options_labs = {
        title: 'Sit-in Labs Distribution',
        is3D: true,
        pieSliceText: 'percentage',
        legend: { position: 'bottom' },
        colors: ['#ff6f61', '#6b8e23', '#8a2be2']
    };

    var chart_labs = new google.visualization.PieChart(document.getElementById('piechart2'));
    chart_labs.draw(data_labs, options_labs);
}

function download(filename, text) {
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function getAllTableData() {
    const tableData = <?php echo json_encode($all_records); ?>;
    const headers = Object.keys(tableData[0] || {});
    const data = tableData.map(row => Object.values(row));
    return [headers, ...data];
}

function exportTableToCSV(filename) {
    const fullData = getAllTableData();
    const csvRows = fullData.map(row => row.join(",")).join("\n");
    download(filename, csvRows);
}

function exportAllToCSV(filename) { // Keeping the explicit "Export All" button
    const fullData = getAllTableData();
    const csvRows = fullData.map(row => row.join(",")).join("\n");
    download(filename, csvRows);
}

function exportTableToExcel(filename) {
    const fullData = getAllTableData();
    let html = `<table border="1"><tr><th>${fullData[0].join("</th><th>")}</th></tr>`;
    for (let i = 1; i < fullData.length; i++) {
        html += `<tr><td>${fullData[i].join("</td><td>")}</td></tr>`;
    }
    html += `</table>`;
    const uri = 'data:application/vnd.ms-excel;base64,' + btoa(unescape(encodeURIComponent(html)));
    const link = document.createElement("a");
    link.href = uri;
    link.download = filename;
    document.body.appendChild(link);
    document.body.removeChild(link);
}

function exportTableToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const fullData = getAllTableData();
    const head = [fullData[0]];
    const body = fullData.slice(1);
    doc.autoTable({ head: head, body: body });
    doc.save('sit_in_full_records.pdf');
}

function exportToWord() {
    const fullData = getAllTableData();
    let tableHTML = `<table border="1"><tr><th>${fullData[0].join("</th><th>")}</th></tr>`;
    for (let i = 1; i < fullData.length; i++) {
        tableHTML += `<tr><td>${fullData[i].join("</td><td>")}</td></tr>`;
    }
    tableHTML += `</table>`;
    const blob = new Blob(['<html><head><meta charset="utf-8"></head><body>' + tableHTML + '</body></html>'], {
        type: 'application/msword'
    });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'sit_in_full_records.doc';
    link.click();
}
</script>
</body>
</html>