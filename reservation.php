<?php
session_start();
include('db.php');
include('header.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $_POST['id_number'];
    $student_name = $_POST['student_name'];
    $purpose = $_POST['purpose'];
    $lab = $_POST['lab'];
    $pc_number = $_POST['pc_number'];
    $time_in = $_POST['time_in'];
    $date = $_POST['date'];
    $session = $_POST['session'];

    // Check if PC exists and is available (not reserved or in use)
    $checkPC = $connection->prepare("SELECT id, status FROM pcs WHERE lab = ? AND pc_number = ?");
    $checkPC->bind_param("si", $lab, $pc_number);
    $checkPC->execute();
    $checkPC->store_result();

    if ($checkPC->num_rows > 0) {
        $checkPC->bind_result($pcId, $status);
        $checkPC->fetch();
        
        // If the PC is in use or reserved, show an error
        if ($status == 'in_use' || $status == 'reserved') {
            echo "<script>alert('This PC is already in use or reserved. Please select another one.');</script>";
        } else {
            // Proceed with reserving the PC
            // Update PC status to 'reserved'
            $updatePCStatus = $connection->prepare("UPDATE pcs SET status = 'reserved' WHERE lab = ? AND pc_number = ?");
            $updatePCStatus->bind_param("si", $lab, $pc_number);
            $updatePCStatus->execute();

            // Insert into reservations table
            $stmt = $connection->prepare("INSERT INTO reservations (id_number, student_name, purpose, lab, pc_number, time_in, date, remaining_session, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("sssssssi", $id_number, $student_name, $purpose, $lab, $pc_number, $time_in, $date, $session);

            if ($stmt->execute()) {
                header("Location: reservation.php?success=1");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    } else {
        echo "<script>alert('The PC does not exist. Please select a valid PC.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation</title>
    <link rel="stylesheet" href="assets/styles.css?v=<?php echo time(); ?>">
</head>
<style>
    body { margin: 0; padding: 0; }
    h1 { text-align: center; font-size: 2rem; margin-bottom: 30px; }

    form {
        max-width: 400px;
        margin: auto;
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    button {
        margin-top: 25px;
        padding: 10px;
        width: 100%;
        background-color: #28a745;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }

    button:hover {
        background-color: #218838;
    }
</style>
<body>

<nav>
    <h1>Dashboard</h1>
    <div class="menu-icon" id="menu-icon">☰</div>
    <div class="nav-links" id="nav-links">
        <a href="#">Notification</a>
        <a href="dashboard.php">Home</a>
        <a href="editprofile.php">Edit Profile</a>
        <a href="sitin-history.php">Sit-in History</a>
        <a href="reservation.php">Reservation</a>
        <a href="lab_schedule_student.php">Lab Schedules</a>
        <a href="lab_resources_view.php">Lab Resources</a>
        <a href="index.php" class="logout-btn" id="logoutbtn">Log out</a>
    </div>
</nav>

<h1>Reservation</h1>
<form method="POST">
    <label>ID Number:</label>
    <input type="text" name="id_number" value="<?php echo $_SESSION['idno'] ?? ''; ?>" required>

    <label>Student Name:</label>
    <input type="text" name="student_name" value="<?php echo ($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''); ?>" required>

    <label>Purpose:</label>
    <select name="purpose" required>
        <option value="">Select Purpose</option>
        <option value="C Programming">C Programming</option>
        <option value="Java Programming">Java Programming</option>
        <option value="C++ Programming">C++ Programming</option>
        <option value="C# Programming">C# Programming</option>
        <option value="Php Programming">Php Programming</option>
        <option value="Python Programming">Python Programming</option>
    </select>

    <label>Lab Room:</label>
<select name="lab" id="lab-dropdown" required>
    <option value="">Select Lab</option>
    <option value="524">524</option>
    <option value="544">544</option>
    <option value="542">542</option>
    <option value="530">530</option>
    <option value="528">528</option>
    <option value="526">526</option>
    <option value="MAC Laboratory">MAC Laboratory</option>
</select>

    <label>PC Number:</label>
    <select name="pc_number" id="pc-dropdown" required>
    <option value="">Select PC</option>
        <?php for ($i = 1; $i <= 30; $i++): ?>
            <option value="<?= $i ?>">PC-<?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label>Time In:</label>
    <input type="time" name="time_in" required>

    <label>Date:</label>
    <input type="date" name="date" required>

    <label>Remaining Session:</label>
    <input type="text" name="session" value="<?php echo $_SESSION['sessions'] ?? '0'; ?>" readonly>

    <button type="submit">Reserve</button>
</form>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div style="text-align:center; color: green;">Reservation submitted successfully!</div>
<?php endif; ?>


</body>
<script>
document.getElementById('lab-dropdown').addEventListener('change', function() {
  const labId = this.value;

  if (labId) {
    fetch(`get_available_pcs.php?lab=${encodeURIComponent(labId)}`)
      .then(res => res.json())
      .then(data => {
        const dropdown = document.getElementById('pc-dropdown');
        dropdown.innerHTML = ''; // Clear previous options

        if (data.length === 0) {
          const option = document.createElement('option');
          option.text = 'No available PCs';
          option.disabled = true;
          dropdown.appendChild(option);
        } else {
          // Add available PCs to the dropdown
          data.forEach(pc => {
            const option = document.createElement('option');
            option.value = pc.pc_number;
            option.text = `PC #${pc.pc_number}`;
            dropdown.appendChild(option);
          });
        }
      })
      .catch(error => {
        console.error('Error fetching available PCs:', error);
      });
  }
});

</script>

</html>
