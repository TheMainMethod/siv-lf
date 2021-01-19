<?php
// inicializa sesión
session_start();
 
//si el usuario ya inició sesión, redirígelo a la página de bienvenida
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>iniciar sesion</title>
  <meta name="author" content="">
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <script src="js/jquery-3.5.1.js"></script>
  <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div>
        <p class="backend">SIV-LF (xd)</p>
    </div>

    <form id="loginform">
        <div class="form-group">
            <label class="backend">nombre de usuario</label>
            <input type="text" name="username" class="backend">
            <label class="backend" id="username_err"></label>
        </div>    
        <div class="form-group">
            <label class="backend">contraseña</label>
            <input type="password" name="password" class="backend">
            <label class="backend" id="password_err"></label>
        </div>
        <div class="form-group">
            <input type="submit" class="backend" value="iniciar sesión">
        </div>
    </form>
    <button class="backend" onclick="toSignup()">registrarse</button>

  <script src="js/login.js"></script>

</body>

</html>