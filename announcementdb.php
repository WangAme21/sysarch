<?php
    include('db.php');
    session_start();

    if(isset($_POST['text']) && !empty(trim($_POST['text']))){
        $text = $_POST['text'];
        $timestamp = date("Y-m-d H:i:s");

        $query = "INSERT INTO announcements (announcement_text, created_at) VALUES ('$text', '$timestamp')";
        $result = mysqli_query($connection, $query);

        if($result){
            header('location:admindashboard.php');
        } else {
            header('location:admindashboard.php');
        }
    }
?>
