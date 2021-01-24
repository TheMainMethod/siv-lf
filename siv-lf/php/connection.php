<?php
function OpenCon()
{
    $dbhost = "localhost";
    $dbuser = "siv-lf";
    $dbpass = "EmeraldBadgeGang";
    $db = "siv-lf";
    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
    
    return $conn;
}
 
function CloseCon($conn)
{
    $conn -> close();
}
   
?>