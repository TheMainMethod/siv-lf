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
    <title>Registro</title>
    <script src="js/jquery-3.5.1.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="css/normalize.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
</head>
<body class="grisacio">
    <header class="encabezado">
        <img src="img/logo.svg" alt="Logo" class="encabezado__logo">
    </header>
    <div class="wrapper contenedor">
        <p class="titulo-principal">nuevo empleado</p>

        <form id="signupform">
            <div class="form-group">
                <label class="">nombre usuario</label>
                <input type="text" name="username" class="">
                <label class="" id="username_err"></label>
            </div>    
            <div class="form-group">
                <label class="">contraseña</label>
                <input type="password" name="password" class="">
                <label class="" id="password_err"></label>
            </div>
            <div class="form-group">
                <label class="">repite contraseña</label>
                <input type="password" name="confirm_password" class="">
                <label class="" id="confirm_password_err"></label>
            </div>
            <div class="form-group">
                <label class="">nombre</label>
                <input type="text" name="name" class="">
                <label class="" id="name_err"></label>
            </div>    
            <div class="form-group">
                <label class="">apellido paterno</label>
                <input type="text" name="last_name" class="">
                <label class="" id="last_name_err"></label>
            </div>    
            <div class="form-group">
                <label class="">apellido materno</label>
                <input type="text" name="mid_name" class="">
                <label class="" id="mid_name_err"></label>
            </div>
            <div class="form-group">
                <label class="">turno</label>
                <select name="shift" class="">
                    <option value=1>Matutino</option>
                    <option value=0>Vespertino</option>
                </select>
            </div> 
            <div class="form-group">
                <label class="">autorización</label>
                <input type="password" name="owner_pass" class="">
                <label class="" id="owner_pass_err"></label>
            </div>
            <div>
                <input type="submit" class="" value="agregar">

            </div>
            <p class="">ya tienes cuenta? <button class="" onclick="toLogin()">inicia sesión</button></p>
        </form>

    </div>   

    <script src="js/signup.js"></script> 
</body>
</html>