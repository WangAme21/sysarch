<?php
include('db.php');
session_start();

if (isset($_GET['id']) && isset($_SESSION['sit_in_students'])) {
    $id = $_GET['id'];

    // Fetch student record from userstbl
    $query = "SELECT * FROM userstbl WHERE idno = '$id'";
    $result = mysqli_query($connection, $query);

    if (!$result || mysqli_num_rows($result) === 0) {
        die("Student not found or query failed: " . mysqli_error($connection));
    }

    $row = mysqli_fetch_assoc($result);

    // Fetch latest login_time from sit_in_records
    $query_login  = "SELECT login_time FROM sit_in_records WHERE student_id = '$id' ORDER BY id DESC LIMIT 1";
    $result_login = mysqli_query($connection, $query_login);

    if ($row_login = mysqli_fetch_assoc($result_login)) {
        $login_time = $row_login['login_time'];
    } else {
        date_default_timezone_set("America/New_York");
        $login_time = date("h:i:s");
    }

    // Prepare session data
    $student_id = $row['idno'];
    $student_name = $row['firstname'] . ' ' . $row['lastname'];
    $purpose = $row['purpose'];
    $lab = $row['labs'];
    $sessions = (int) $row['sessions'];

    date_default_timezone_set("America/New_York");
    $logout_time = date("h:i:s");

    // Decrement sessions
    if ($sessions > 0) {
        $new_sessions = $sessions - 1;
        $update_query = "UPDATE userstbl SET sessions = $new_sessions WHERE idno = '$id'";
        if (!mysqli_query($connection, $update_query)) {
            die("Failed to update sessions: " . mysqli_error($connection));
        }
    }

    // Insert into sit_in_records
    $insert_record = "INSERT INTO sit_in_records (student_id, student_name, purpose, lab, login_time, logout_time)
                      VALUES ('$student_id', '$student_name', '$purpose', '$lab', '$login_time', '$logout_time')";
    if (!mysqli_query($connection, $insert_record)) {
        die("Failed to insert into sit_in_records: " . mysqli_error($connection));
    }

    // Insert into sit_in_history
    $insert_history = "INSERT INTO sit_in_history (idno, date, status, laboratory) 
                       VALUES ('$id', NOW(), 'Completed', '$lab')";
    if (!mysqli_query($connection, $insert_history)) {
        die("Failed to insert into sit_in_history: " . mysqli_error($connection));
    }

    // Update user status
    $update_status = "UPDATE userstbl SET status = 'Completed' WHERE idno = '$id'";
    if (!mysqli_query($connection, $update_status)) {
        die("Failed to update status: " . mysqli_error($connection));
    }

    // Remove student from session list
    $_SESSION['sit_in_students'] = array_filter(
        $_SESSION['sit_in_students'],
        fn($val) => $val !== $id
    );

    header("Location: current-sit-in.php?success=1");
    exit();
} else {
    echo "Invalid request.";
}
?>
