<?php
// inicializa la sesión
session_start();
 
//si el usuario no ha iniciado sesión, redirígelo a la página de inicio de sesión
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>bienvenido</title>

    <script src="js/jquery-3.5.1.js"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="backend">
        <p>bienvenido, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</p>
        <p>
            <button class="backend" onclick="logout()">cerrar sesión</button>
        </p>
        <br/>
        <?php
        if($_SESSION["role"] == 'dueño')
        {
            echo '<p>Ehorabuena, eres el dueño</p>';
        }
        
        ?>

        <form id="searchform">
            <div class="form-group">
                <label class="backend">termino de busqueda</label>
                <input type="text" name="query" class="backend">
            </div>
            <div class="form-group">
                <input type="submit" class="backend" value="buscar"/>
            </div>
        </form>
        
        <div id="products-table"></div>

        <div id ="print-button">
            <button class="backend" onclick="printJS({
                    printable: 'products-table',
                    type: 'html',
                    css: 'css/style.css',
                    documentTitle: 'prueba de reporte',
                    header: 'reporte jaj' })">
                Imprimir a PDF
            </button>
        </div>
    </div>

    <script src="js/welcome.js"></script>
    <script src="js/print.min.js"></script>
    
</body>
</html>