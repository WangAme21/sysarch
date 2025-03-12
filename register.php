<?php
    include('header.php');
    include('usersdb.php');
?>

<nav>
    <h1>Sit-in Management System</h1> 
</nav>

<form action="registerdb.php" method="post">
    <div class="form-container">
        <div class="form-content">
            <div class="x-button-container">
                <button class="x-button" type="button" id="x-button">X</button>
            </div>
            <h1>Register</h1>
            <div>
                <input type="number" name="idno" placeholder="Idno" class="input-fields">
                <input type="text" name="lastname" placeholder="Lastname" class="input-fields">
            </div>
            <div>
                <input type="text" name="firstname" placeholder="Firstname" class="input-fields">
                <input type="text" name="middlename" placeholder="Middlename" class="input-fields">
            </div>
            <div>
                <input type="email" name="email" placeholder="Email" class="input-fields">
                <input type="text" name="course" placeholder="Course" class="input-fields">
            </div>
            <div>
                <input type="number" name="level" placeholder="Level" class="input-fields">
                <input type="password" name="password" placeholder="password" class="input-fields">
            </div>
            <input type="submit" name="register-user" value="Register" class="input-field" id="registerbtn">
        </div>
    </div>
</form>

<?php
    if(isset($_GET['success']) && $_GET['success'] == 1){
        echo "<script>
                window.onload = ()=>{
                    const registerModal = document.getElementById('register-modal');
                    registerModal.style.display = 'block';
                    
                }
        
        </script>";
    }
?>

<div class="background-overlay"></div>
<div class="register-modal" style="display:none" id="register-modal">
    <div class="register-modal-content">
        <img src="check.png" alt="check" class="check-logo">
        <h3>Success</h3>
        <p>Your account has been successfully created</p>
        <button class="confirm-btn" id="confirm-btn">Confirm</button>
    </div>
</div>

<script>
    const xbutton = document.getElementById('x-button');
    const registerModal = document.getElementById('register-modal');
    const registerbtn = document.getElementById('registerbtn');
    const confirmbtn = document.getElementById('confirm-btn');

    xbutton.addEventListener("click", ()=>{
        window.location.href = "index.php";
    });

    confirmbtn.addEventListener("click", ()=>{
        window.location.href="index.php";
    });
</script>