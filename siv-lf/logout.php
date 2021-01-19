<?php
// inicializa sesión
session_start();
 
// desasigna todas las variables de sesión
$_SESSION = array();
 
// destruye la sesión
session_destroy();
 
// de vuelta a la página de inicio de sesión
header("location: index.php");
exit;
?>