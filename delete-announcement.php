<?php
    include('db.php');
    session_start();

    if(isset($_POST['id'])) {
        $id = $_POST['id'];
        $deleteQuery = "DELETE FROM announcements WHERE id = $id";
        mysqli_query($connection, $deleteQuery);
    }

    header("Location: admindashboard.php");
    exit();
?>
