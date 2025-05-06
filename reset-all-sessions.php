<?php
    include('db.php');
    mysqli_query($connection, "UPDATE userstbl SET sessions = 30");
    header("Location: student_management.php");
    exit;
?>
