<?php
include('db.php');

if (isset($_POST['reset_points'])) {
    $idno = $_POST['idno'];

    // Reset points to 0 (or another base value)
    $resetPointsQuery = "UPDATE userstbl SET points = 0 WHERE idno = ?";
    $stmt = $connection->prepare($resetPointsQuery);
    $stmt->bind_param("s", $idno);
    $stmt->execute();

    // Reset sessions and claimed rewards
    $resetSessionQuery = "UPDATE userstbl SET claimed_rewards = 0 WHERE idno = ?";
    $stmt = $connection->prepare($resetSessionQuery);
    $stmt->bind_param("s", $idno);
    $stmt->execute();

    // Redirect after resetting
    header("Location: student_management.php");
    exit();
}
?>
