<?php
session_start();
include('db.php');

// Check if 'id' and 'status' are passed via POST
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($status === 'Accepted') {
        // Get the id_number, lab, and pc_number from reservations
        $queryUser = "SELECT id_number, lab, pc_number FROM reservations WHERE id = ?";
        $stmtUser = $connection->prepare($queryUser);
        $stmtUser->bind_param("i", $id);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $user = $resultUser->fetch_assoc();
            $idNumber = $user['id_number'];
            $lab = $user['lab'];
            $pcNumber = $user['pc_number'];

            // Decrease the session count for the user
            $decrementQuery = "UPDATE userstbl SET sessions = GREATEST(sessions - 1, 0) WHERE idno = ?";
            $stmtDecrement = $connection->prepare($decrementQuery);
            $stmtDecrement->bind_param("s", $idNumber);
            $stmtDecrement->execute();

            // Get updated session count
            $querySessions = "SELECT sessions FROM userstbl WHERE idno = ?";
            $stmtSessions = $connection->prepare($querySessions);
            $stmtSessions->bind_param("s", $idNumber);
            $stmtSessions->execute();
            $resultSessions = $stmtSessions->get_result();
            $userSessions = $resultSessions->fetch_assoc();

            $_SESSION['sessions'] = $userSessions['sessions'];

            // Update remaining_session and reservation status
            $updateRemaining = "UPDATE reservations SET remaining_session = ?, status = 'Accepted' WHERE id = ?";
            $stmtRemaining = $connection->prepare($updateRemaining);
            $stmtRemaining->bind_param("ii", $userSessions['sessions'], $id);
            $stmtRemaining->execute();

            // Update PC status to 'in_use'
            $updatePCStatus = "UPDATE pcs SET status = 'in_use' WHERE lab = ? AND pc_number = ?";
            $stmtUpdatePC = $connection->prepare($updatePCStatus);
            $stmtUpdatePC->bind_param("si", $lab, $pcNumber);
            $stmtUpdatePC->execute();
        } else {
            die('No reservation found for this ID.');
        }
    } elseif ($status === 'Declined') {
        // Update status to Declined
        $updateStatus = "UPDATE reservations SET status = 'Declined' WHERE id = ?";
        $stmtDecline = $connection->prepare($updateStatus);
        $stmtDecline->bind_param("i", $id);
        $stmtDecline->execute();
    }

    // Redirect back after handling
    $_SESSION['message'] = "Reservation updated successfully.";
    header("Location: view-reservations.php");
    exit;
} else {
    echo "Error: Missing parameters.";
    exit;
}
?>

get_pcs.php
<?php
header('Content-Type: application/json');
include('db.php');

$labId = $_GET['lab'] ?? null;

if (!$labId) {
    http_response_code(400);
    echo json_encode(['error' => 'Lab ID is required']);
    exit;
}

$query = "SELECT id, pc_number, status FROM pcs WHERE lab = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $labId);
$stmt->execute();
$result = $stmt->get_result();

$pcs = [];
for ($i = 1; $i <= 30; $i++) {
    $pcs[] = [
        'id' => $i,
        'pc_number' => $i,
        'status' => 'offline'  // This can be changed to 'available', 'in_use', etc.
    ];
}

echo json_encode($pcs);
?>

reservation.php
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

    // Check if PC exists, if not insert it
$checkPC = $connection->prepare("SELECT id FROM pcs WHERE lab = ? AND pc_number = ?");
$checkPC->bind_param("si", $lab, $pc_number);
$checkPC->execute();
$checkPC->store_result();

if ($checkPC->num_rows === 0) {
    $insertPC = $connection->prepare("INSERT INTO pcs (lab, pc_number, status) VALUES (?, ?, 'in_use')");
    $insertPC->bind_param("si", $lab, $pc_number);
    $insertPC->execute();
}

    // Insert into reservations
    $stmt = $connection->prepare("INSERT INTO reservations (id_number, student_name, purpose, lab, pc_number, time_in, date, remaining_session, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("sssssssi", $id_number, $student_name, $purpose, $lab, $pc_number, $time_in, $date, $session);

    if ($stmt->execute()) {
       

        header("Location: reservation.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <select name="lab" required>
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
    <select name="pc_number" required>
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
</html>