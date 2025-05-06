<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idno'])) {
    $idno = $_POST['idno'];
    $query = "UPDATE userstbl SET points = 0 WHERE idno = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $idno);
    $stmt->execute();
}

header("Location: student_management.php");
exit();
?>
