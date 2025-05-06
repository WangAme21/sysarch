<?php
include('db.php');
if (isset($_POST['idno'])) {
    $idno = $_POST['idno'];
    $stmt = $connection->prepare("UPDATE userstbl SET sessions = 30 WHERE idno = ?");
    $stmt->bind_param("s", $idno);
    $stmt->execute();
}
header("Location: student_management.php");
exit;

?>
