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
    $time_in = $_POST['time_in'];
    $date = $_POST['date'];
    $session = $_POST['session'];

    $stmt = $connection->prepare("INSERT INTO reservations (id_number, student_name, purpose, lab, time_in, date, remaining_session) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $id_number, $student_name, $purpose, $lab, $time_in, $date, $session);

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
    body{
        margin: 0;
        padding: 0;
    }
    h1 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 30px;
}

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

input[type="text"],
input[type="time"],
input[type="date"] {
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
        <a href="#"> Notification</a>
        <a id="home-nav" href="dashboard.php"> Home</a>
        <a href="editprofile.php"> Edit Profile</a>
        <a href="sitin-history.php">Sit-in History</a>
        <a href="reservation.php"> Reservation</a>
        <a href="lab_schedule_student.php">Lab Schedules</a>
        <a href="lab_resources_view.php">Lab Resources</a>
        <a href="index.php" class="logout-btn" id="logoutbtn"> Log out </a>
    </div>
</nav>
    <h1 style="text-align: center;">Reservation</h1>
    <form method="POST" style="width: 300px; margin: auto;">
        <label>ID Number:</label>
        <input type="text" name="id_number" value="<?php echo $_SESSION['idno'] ?? ''; ?>" required>

        <label>Student Name:</label>
        <input type="text" name="student_name" value="<?php echo ($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''); ?>" required>

        <label>Purpose:</label>
        <input type="text" name="purpose" required>

        <label>Lab:</label>
        <input type="text" name="lab" required>

        <label>Time In:</label>
        <input type="time" name="time_in" required>

        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Remaining Session:</label>
        <input type="text" name="session" value="<?php echo $_SESSION['sessions'] ?? '0'; ?>" readonly>

        <button type="submit" style="margin-top: 20px; background-color: green; color: white; width: 100%; padding: 10px;">Reserve</button>
    </form>

    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo "<script>alert('Reservation submitted successfully!');</script>";
    }
    ?>
</body>
</html>