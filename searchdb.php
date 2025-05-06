<?php
session_start();
include('db.php');

if(isset($_GET['idnum'])){
    $idno = $_GET['idnum'];

    $query = "SELECT * FROM userstbl WHERE idno = '$idno'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);

        if(!isset($_SESSION['sit_in_students'])) {
            $_SESSION['sit_in_students'] = [];
        }

        if(!in_array($idno, $_SESSION['sit_in_students'])) {
            $_SESSION['sit_in_students'][] = $idno;
        }

        header('location:admindashboard.php?idnum='. $row['idno']);   
        exit();
    } else {
        echo "<script>alert('ID not found'); window.location.href='admindashboard.php';</script>";
    }
}
?>
