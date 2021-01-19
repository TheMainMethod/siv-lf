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
    <title>Inicio de Sesión</title>
    <meta name="author" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="js/jquery-3.5.1.js"></script>
    <link href="css/style.css" rel="stylesheet">

    <link href="css/normalize.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
</head>


<body class="obscuro">
    <div class="inicio-de-sesion">

        <img src="img/logo.svg" alt="Logo" class="inicio-de-sesion__logo">

        <form id="loginform">
            <div class="form-group">
                <label class="inicio-de-sesion__campo__texto">Usuario:</label>
                <input type="text" name="username" placeholder="Ingresa tu usuario" class="inicio-de-sesion__campo__registro">
                <span id="username_err"></span>
            </div>    
            <div class="form-group">
                <label class="inicio-de-sesion__campo__texto">Contraseña:</label>
                <input type="password" name="password" placeholder="Ingresa tu contraseña" class="inicio-de-sesion__campo__registro">
                <span id="password_err"></span>
            </div>
            <div class="form-group centrar">
                <input type="submit" value="Iniciar sesión" class="inicio-de-sesion__btn centrar">
            </div>
        </form>
        <button onclick="toSignup()" class="inicio-de-sesion__registrarse">Registrarse</button>
    </div>
    
    <script src="js/login.js"></script>

</body>

</html>


