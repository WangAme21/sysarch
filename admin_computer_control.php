<?php
include('db.php');

// Simulated labs
$labs = [
    ['id' => 524, 'name' => 'Lab524'],
    ['id' => 544, 'name' => 'Lab544'],
    ['id' => 542, 'name' => 'Lab542'],
    ['id' => 530, 'name' => 'Lab530'],
    ['id' => 528, 'name' => 'Lab528'],
    ['id' => 526, 'name' => 'Lab526'],
];

$selectedLabId = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pc_id'], $_POST['status'], $_POST['lab_id'])) {
    $pc_id = $_POST['pc_id'];
    $status = $_POST['status'];
    $selectedLabId = $_POST['lab_id']; // Preserve lab selection

    // Update the PC status
    $update = $connection->prepare("UPDATE pcs SET status = ? WHERE id = ?");
    $update->bind_param("si", $status, $pc_id);
    $update->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Computer Control V2</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; }
        #labSelect {
            display: block;
            margin: 0 auto 2rem auto;
            padding: 0.5rem;
            font-size: 1rem;
        }

        .pc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 2rem;
            padding: 3rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .pc-item {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem 1rem;
            text-align: center;
            transition: transform 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pc-item:hover { transform: scale(1.02); }

        .pc-icon i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #4b5563;
        }

        .pc-number {
            font-weight: bold;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            color: #111827;
        }

        .status-label {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            margin: 0.5rem 0;
            display: inline-block;
        }

        .pc-item.available .status-label {
            background-color: #d1fae5;
            color: #065f46;
        }

        .pc-item.in_use .status-label {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .pc-item.disabled .status-label {
            background-color: #e5e7eb;
            color: #4b5563;
        }

        .pc-item.maintenance .status-label {
            background-color: #fef3c7;
            color: #92400e;
        }

        .pc-item.offline .status-label {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .pc-item form {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            margin-top: 1rem;
        }

        .pc-item button {
            padding: 0.4rem;
            font-size: 0.85rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .pc-item button:hover { opacity: 0.85; }

        .pc-item button[name="status"][value="available"] {
            background-color: #10b981;
            color: white;
        }

        .pc-item button[name="status"][value="disabled"] {
            background-color: #6b7280;
            color: white;
        }

        .pc-item button[name="status"][value="maintenance"] {
            background-color: #f59e0b;
            color: white;
        }
    </style>
</head>
<body>

<nav>
    <h1>CCS Admin</h1>
    <div class="nav-links-admin">
        <a href="admindashboard.php">Home</a>
        <a href="#" onclick="searchFunction()">Search Students</a>
        <a href="current-sit-in.php">Sit-in</a>
        <a href="view-sit-in-records.php">Sit-in Records</a>
        <a href="sit-in-reports.php">Sit-in Reports</a>
        <a href="feedback-reports.php">Feedback Reports</a>
        <a href="view-reservations.php">Reservation</a>
        <a href="student_management.php">Student Info</a>
        <a href="lab_schedule.php">Lab Schedule</a>
        <a href="lab_resources.php">Lab Resources</a>
        <a href="admin_computer_control.php">PC Control</a>
        <a href="admin.php" class="logout-btn">Log out</a>
    </div>
</nav>

<h2>Computer Control Panel</h2>

<select id="labSelect" onchange="loadComputers()">
    <option value="">Select Lab</option>
    <?php foreach ($labs as $lab): ?>
        <option value="<?= $lab['id'] ?>" <?= ($selectedLabId == $lab['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($lab['name']) ?>
        </option>
    <?php endforeach; ?>
</select>

<div id="computers" class="pc-grid"></div>

<script>
    function loadComputers() {
        const labId = document.getElementById("labSelect").value;
        if (!labId) return;

        fetch(`get_pcs.php?lab=${labId}`)
            .then(res => res.json())
            .then(data => {
                const grid = document.getElementById("computers");
                grid.innerHTML = "";

                data.forEach(pc => {
                    const div = document.createElement("div");
                    div.className = "pc-item " + pc.status;

                    const pcNumber = document.createElement("div");
                    pcNumber.className = "pc-number";
                    pcNumber.innerText = "PC-" + pc.pc_number;

                    const pcIcon = document.createElement("div");
                    pcIcon.className = "pc-icon";
                    pcIcon.innerHTML = "<i class='fas fa-desktop'></i>";

                    const statusLabel = document.createElement("div");
                    statusLabel.className = "status-label";
                    statusLabel.innerText = pc.status.charAt(0).toUpperCase() + pc.status.slice(1);

                    const actionsDiv = document.createElement("div");
                    actionsDiv.innerHTML = `
                       <form class="status-form" data-pc-id="${pc.id}" data-lab-id="${labId}">
    <button type="button" data-status="available">Enable</button>
    <button type="button" data-status="disabled">Disable</button>
    <button type="button" data-status="maintenance">Maintenance</button>
</form>

                    `;

                    div.appendChild(pcIcon);
                    div.appendChild(pcNumber);
                    div.appendChild(statusLabel);
                    div.appendChild(actionsDiv);

                    grid.appendChild(div);
                });
            })
            .catch(err => {
                console.error("Failed to load PCs:", err);
            });
    }

    document.addEventListener('click', function(e) {
    if (e.target.closest('.status-form') && e.target.matches('button')) {
        const form = e.target.closest('.status-form');
        const status = e.target.getAttribute('data-status');
        const pcId = form.getAttribute('data-pc-id');
        const labId = form.getAttribute('data-lab-id');

        fetch('admin_computer_control.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `pc_id=${pcId}&status=${status}&lab_id=${labId}`
        })
        .then(res => res.ok ? loadComputers() : alert("Failed to update PC status."))
        .catch(err => {
            console.error("Error updating status:", err);
        });
    }
});


    // Reload PCs if a lab was previously selected
    document.addEventListener("DOMContentLoaded", () => {
        const selectedLab = "<?= $selectedLabId ?? '' ?>";
        if (selectedLab) {
            document.getElementById("labSelect").value = selectedLab;
            loadComputers();
        }
    });
</script>

</body>
</html>
