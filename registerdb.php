<?php
    include('db.php');
    
    if(isset($_POST['register-user'])){
        $idno = $_POST['idno'];
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'];
        $email = $_POST['email'];
        $course = $_POST['course'];
        $level = $_POST['level'];
        $password = $_POST['password'];

        if(empty($idno) || empty($lastname) || empty($firstname) || empty($middlename) || empty($email) || empty($course) || empty($level) || empty($password)) {
            header('location:register.php?message=register failed');
            exit();
        }

        $query = "INSERT INTO userstbl (idno, lastname, firstname, middlename, email, course ,level, password) 
        VALUES ('$idno', '$lastname', '$firstname', '$middlename', '$email', '$course', '$level', '$password')";

        $result = mysqli_query($connection, $query);

        if(!$result){
            die('register failed'.mysqli_error($connection));
        }else{
            header('location:register.php?success=1');
        }
    }
?>