<?php
    include('header.php');

?>

<nav>
    <h1>Sit-in Management System</h1> 
    <a href="admin.php">Admin</a>
</nav>

<form action="usersdb.php" method="post">
    <div class="form-container">
        <div class="form-content">
            <h1>Log in</h1>
            <label class="login-idno">Idno</label>
            <input type="number" name="idno" placeholder="Idno" class="input-fields">
            <label class="login-password">Password</label>
            <input type="password" name="password" placeholder="Password" class="input-fields">
            <input type="submit" name="login-user" value="Log in" class="input-field">
            <div class="register-btn-container">
                <input type="button" id="register-btn" value="Register account" class="register-btn">
            </div>
        </div>
    </div>
</form>

<script>
    const registerbtn = document.getElementById("register-btn");

    registerbtn.addEventListener("click", () => {
        window.location.href = "register.php";
    });
</script>