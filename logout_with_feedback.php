<?php
session_start();
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Optional: mark the student as logged out in DB
    $query = "UPDATE userstbl SET status = 'Logged Out' WHERE idno = '$id'";
    mysqli_query($connection, $query);

    // Store this student ID to show feedback form in sitin_history
    if (!isset($_SESSION['feedback_ids'])) {
        $_SESSION['feedback_ids'] = [];
    }

    if (!in_array($id, $_SESSION['feedback_ids'])) {
        $_SESSION['feedback_ids'][] = $id;
    }

    header("Location: sitin_history.php");
    exit();
}
?>

