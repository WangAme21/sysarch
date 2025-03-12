<?php
    include('db.php');
    session_start();

    if(isset($_POST['login-user'])){
        $idno = $_POST['idno'];
        $password = $_POST['password'];

        $query = "SELECT * FROM userstbl WHERE idno='$idno' and password = '$password'";
        $result = mysqli_query($connection, $query);

        if(mysqli_num_rows($result)){
            $user = mysqli_fetch_assoc($result);

            $_SESSION['idno'] = $user['idno'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['course'] = $user['course'];
            $_SESSION['level'] = $user['level'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['sessions'] = $user['sessions'];
            
            header('location:dashboard.php?success=1');
        }else{
            header('location:index.php?message=Invalid idno or password');
        }
    }
?>