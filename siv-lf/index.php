<?php
// inicializa sesión
session_start();
 
//si el usuario ya inició sesión, redirígelo a la página de bienvenida
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: sales.php");
    exit;
}
else
{
    header("location: login.php");
    exit;
}