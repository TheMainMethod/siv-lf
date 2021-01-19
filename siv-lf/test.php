<?php

//muestra una única cadena, que es el hash de la variable $pass

$pass = 'prueba';
$hash = Password::hash($pass);
echo $hash;

?>