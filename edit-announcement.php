<?php
    include('db.php');
    session_start();

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM announcements WHERE id = $id";
        $result = mysqli_query($connection, $query);
        $announcement = mysqli_fetch_assoc($result);
    }

    if(isset($_POST['update'])) {
        $id = $_POST['id'];
        $text = $_POST['text'];

        $updateQuery = "UPDATE announcements SET announcement_text = '$text' WHERE id = $id";
        mysqli_query($connection, $updateQuery);
        header("Location: admindashboard.php");
        exit();
    }
?>
