<?php
// inicializa la sesión
session_start();
 
//si el usuario no ha iniciado sesión, redirígelo a la página de inicio de sesión
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>

    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="backend">
        <p>bienvenido, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</p>
        <p>
            <a href="logout.php">cerrar sesión</a>
        </p>
        <br/>
        <?php
        if($_SESSION["role"] == 'dueño')
        {
            echo '<p>Ehorabuena, eres el dueño</p>';
        }
        
        ?>
    </div>
    
</body>
</html>