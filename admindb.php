<?php
include('db.php');

if(isset($_POST['login-admin'])){
    $name = $_POST['name'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admintbl WHERE name = '$name' and password = '$password'";

    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result)){
        $user = mysqli_fetch_assoc($result);

        header('location:admindashboard.php');
    }else{
        header('location:admin.php?invalid password');
    }
}

?>