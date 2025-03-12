<?php
    include('db.php');
    session_start();

    if(isset($_POST['update-user'])){
        $idno = $_SESSION['idno'];
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $email = $_POST['email'];
        $course = $_POST['course'];
        $level = $_POST['level'];

        $query = "UPDATE userstbl
                  SET lastname = '$lastname', firstname = '$firstname', middlename = '$middlename', email = '$email', course = '$course', level = '$level'
                  WHERE idno = '$idno'";

        $result = mysqli_query($connection, $query);

        if($result){
            $_SESSION['lastname'] = $lastname;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['middlename'] = $middlename;
            $_SESSION['email'] = $email;
            $_SESSION['course'] = $course;
            $_SESSION['level'] = $level;

            header('Location:dashboard.php?success=1');
        }else{
            header('Location:editprofile.php?Error updating profile');
        }

    }
?>