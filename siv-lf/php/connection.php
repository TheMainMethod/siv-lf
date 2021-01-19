<?php
function OpenCon()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "1234"; //cambiar al menos la contraseña al instalarlo
    $db = "siv-lf";
    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
    
    return $conn;
}
 
function CloseCon($conn)
{
    $conn -> close();
}
   
?>