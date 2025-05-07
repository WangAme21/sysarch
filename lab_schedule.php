<?php
session_start();
include('db.php');

// Handle schedule deletion
if (isset($_POST['delete_schedule']) && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM lab_schedules WHERE id = ?";
    $stmt = $connection->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $message = $stmt->execute() ? "Schedule deleted successfully." : "Error: " . $connection->error;
}

// Handle status toggle (disable/activate)
if (isset($_POST['toggle_status']) && isset($_POST['toggle_id']) && isset($_POST['current_status'])) {
    $toggle_id = $_POST['toggle_id'];
    $current_status = $_POST['current_status'];
    $new_status = $current_status === 'active' ? 'inactive' : 'active';

    $toggle_sql = "UPDATE lab_schedules SET status = ? WHERE id = ?";
    $stmt = $connection->prepare($toggle_sql);
    $stmt->bind_param("si", $new_status, $toggle_id);
    $message = $stmt->execute() ? "Schedule status updated to '$new_status'." : "Error updating status: " . $connection->error;
}

// Handle the form submission for schedule upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_schedule']) && !isset($_POST['toggle_status'])) {
    if (isset($_POST['title'], $_POST['description'], $_POST['start_date'], $_POST['end_date'], $_FILES['schedule_file'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $file = $_FILES['schedule_file'];

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($file["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_file_types = ['pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'];
        if (in_array($file_type, $allowed_file_types)) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $sql = "INSERT INTO lab_schedules (title, description, start_date, end_date, file_path, status) 
                        VALUES ('$title', '$description', '$start_date', '$end_date', '$target_file', 'active')";
                $message = $connection->query($sql) ? "Schedule uploaded successfully." : "Error: " . $connection->error;
            } else {
                $message = "Error uploading the file.";
            }
        } else {
            $message = "Invalid file type. Only PDF, DOCX, XLSX, JPG, JPEG, PNG are allowed.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
    <title>Lab Schedule Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            font-size: 28px;
            color: #2c3e50;
        }

        .section-title {
            font-size: 20px;
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-left: 5px solid #3498db;
            margin-bottom: 20px;
            font-weight: 600;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        form input[type="text"],
        form input[type="date"],
        form input[type="file"],
        form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        form textarea {
            resize: vertical;
            height: 100px;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: white;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #1e65d9;
            color: white;
        }

        .action-btn {
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 0.9em;
            margin-right: 5px;
        }

        .action-btn.delete {
            background-color: #e74c3c;
        }

        .action-btn.delete:hover {
            background-color: #c0392b;
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
        <a href="admin_computer_control.php">PC Control</a>
        <a href="admin.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div> 
</nav>

<div class="container">
    <div class="header">
        <h2>Lab Schedule Management</h2>
    </div>

    <?php if (!empty($message)): ?>
        <p style="margin-bottom: 20px; color: <?php echo strpos($message, 'Error') === false ? 'green' : 'red'; ?>; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="section-title">Upload New Schedule</div>

    <form method="POST" enctype="multipart/form-data" action="lab_schedule.php">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>

        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" required>
        </div>

        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" required>
        </div>

        <div class="form-group">
            <label>Schedule File</label>
            <input type="file" name="schedule_file" required>
        </div>

        <button type="submit">Upload Schedule</button>
    </form>

    <div class="section-title" style="margin-top: 40px;">Current Schedules</div>

    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Date Range</th>
            <th>File</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $result = $connection->query("SELECT * FROM lab_schedules ORDER BY start_date DESC");
        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo $row['start_date'] . " to " . $row['end_date']; ?></td>
            <td>
                <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank" style="text-decoration: underline; color: #007bff;">
                    <?php echo htmlspecialchars(basename($row['file_path'])); ?>
                </a>
            </td>
            <td style="text-transform: capitalize;"><?php echo htmlspecialchars($row['status']); ?></td>
            <td style="text-align: center;">
                <form method="POST" action="lab_schedule.php" style="display:inline;">
                    <input type="hidden" name="toggle_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="current_status" value="<?php echo $row['status']; ?>">
                    <button type="submit" name="toggle_status" class="action-btn"
                        style="background-color: <?php echo $row['status'] === 'active' ? '#e67e22' : '#27ae60'; ?>;">
                        <?php echo $row['status'] === 'active' ? 'Disable' : 'Activate'; ?>
                    </button>
                </form>

                <form method="POST" action="lab_schedule.php" onsubmit="return confirm('Are you sure you want to delete this schedule?');" style="display: inline;">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_schedule" class="action-btn delete">Delete</button>
                </form>
            </td>
        </tr>
        <?php
            endwhile;
        else:
            echo "<tr><td colspan='6' style='text-align:center;'>No schedules uploaded yet.</td></tr>";
        endif;
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
