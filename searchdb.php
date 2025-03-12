<?php
    session_start();
    include('db.php');

    if(isset($_GET['idnum'])){
        $idno = $_GET['idnum'];

        $query = "SELECT * FROM userstbl WHERE idno = '$idno'";
        $result = mysqli_query($connection, $query);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            
            $_SESSION['idno'] = $row['idno'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['course'] = $row['course'];
            $_SESSION['level'] = $row['level'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['sessions'] = $row['sessions'];
            $_SESSION['purpose'] = $row['purpose'];
            $_SESSION['labs'] = $row['labs'];
            header('location:admindashboard.php?idnum='. $row['idno']);  
            exit();            
        }else {
            echo "<script>alert('ID not found'); window.location.href='admindashboard.php';</script>";
        }

    }

?>