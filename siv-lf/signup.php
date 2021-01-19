<?php

// inicializa sesión
session_start();
 
//si el usuario ya inició sesión, redirígelo a la página de bienvenida
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: welcome.php");
    exit;
}


?>
 
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>registrarse</title>
    <script src="js/jquery-3.5.1.js"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <p class="backend">nuevo empleado</p>

        <form id="signupform">
            <div class="form-group">
                <label class="backend">nombre usuario</label>
                <input type="text" name="username" class="backend">
                <label class="backend" id="username_err"></label>
            </div>    
            <div class="form-group">
                <label class="backend">contraseña</label>
                <input type="password" name="password" class="backend">
                <label class="backend" id="password_err"></label>
            </div>
            <div class="form-group">
                <label class="backend">repite contraseña</label>
                <input type="password" name="confirm_password" class="backend">
                <label class="backend" id="confirm_password_err"></label>
            </div>
            <div class="form-group">
                <label class="backend">nombre</label>
                <input type="text" name="name" class="backend">
                <label class="backend" id="name_err"></label>
            </div>    
            <div class="form-group">
                <label class="backend">apellido paterno</label>
                <input type="text" name="last_name" class="backend">
                <label class="backend" id="last_name_err"></label>
            </div>    
            <div class="form-group">
                <label class="backend">apellido materno</label>
                <input type="text" name="mid_name" class="backend">
                <label class="backend" id="mid_name_err"></label>
            </div>
            <div class="form-group">
                <label class="backend">turno</label>
                <select name="shift" class="backend">
                    <option value=1>Matutino</option>
                    <option value=0>Vespertino</option>
                </select>
            </div> 
            <div class="form-group">
                <label class="backend">autorización</label>
                <input type="password" name="owner_pass" class="backend">
                <label class="backend" id="owner_pass_err"></label>
            </div>
            <div>
                <input type="submit" class="backend" value="agregar">

            </div>
            <p class="backend">ya tienes cuenta? <button class="backend" onclick="toLogin()">inicia sesión</button></p>
        </form>

    </div>   

    <script src="js/signup.js"></script> 
</body>
</html>