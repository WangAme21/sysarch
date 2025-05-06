<?php
include('db.php');

$query = "UPDATE userstbl SET points = 0";
mysqli_query($connection, $query);

header("Location: student_management.php");
exit();
?>
