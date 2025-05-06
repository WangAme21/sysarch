<?php
    include('db.php');
    session_start();

    if(isset($_POST['sit-in'])){
        $idno = $_SESSION['idno'];
        $firstname = $_SESSION['firstname'];
        $lastname = $_SESSION['lastname'];
        $sessions = $_SESSION['sessions'];
        $purpose = $_POST['purpose'];
        $labs = $_POST['labs'];
        $status = "active";

        date_default_timezone_set("America/New_York");
        $login_time = date("h:i:s");

        $query = "UPDATE userstbl
                  SET purpose = '$purpose', labs = '$labs', status = '$status'
                  WHERE idno = '$idno'";
        $result = mysqli_query($connection, $query);

        $update_query = "UPDATE sit_in_records
                         SET login_time = '$login_time'
                         WHERE student_id = '$idno'";
        $result_query = mysqli_query($connection, $update_query);

        if($result && $result_query){
            $_SESSION['purpose'] = $purpose;
            $_SESSION['labs'] = $labs;
            $_SESSION['status'] = $status;
            $_SESSION['login_time'] = $login_time;

            header('location:admindashboard.php?idnum'. $idno);
        }else{
            echo "<script>alert('Error Submitting Record'); window.location.href = 'admindashboard.php';</script>";
        }
    }
?>