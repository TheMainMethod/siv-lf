<?php
// inicializa la sesión
session_start();
 
//if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
//{
//    header("location: login.php");
//    exit;
//}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pantalla Principal</title>

    <script src="js/jquery-3.5.1.js"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="backend">
        <header id="logo">
            .
	   </header>
	<aside id="barraLateral">
        <div><button class="backend" onclick="">Ventas</button></div>
        <div><button class="backend" onclick="">Cliente</button></div>
        <div><button class="backend" onclick="">Productos</button></div>
        <div><button class="backend" onclick="">Inventario</button></div>
        <div><button class="backend" onclick="">Corte</button></div>
        <p></p>
        <div><button class="backend" onclick="">Settings</button></div>
        <time></time>
        <p></p>
    </aside>
        <section id="barra de busqueda"  style="width:100%">

             <div class="form-group">
            <input type="text" name="NameCode" class="backend" value="Nombre o código del producto">
            <!--<label class="backend" id="nombre_err"></label>-->
                 
                 <form id="agregarProducto" action="GENERAR Y GUARDAR COOKIE"><!--Action: generar y guardar cookie-->    
            <button  type="submit" class="backend" onclick="LLAMAR (revisar) COOKIES" value="">Añadir el producto (ENTER)</button></form><!--ON CLICK LLAMAR (REVISAR) COOKIES-->
                <form id="searchformMain" action="GENERAR Y GUARDAR COOKIE"><!--x2-->
            <button type=button class="backend" onclick= "toBusqueda()"> Buscar un producto</button></form>
            </div> 
        </section>
         <section id="botones" style="width:100%">
             <div class="form-group">
            <button class="backend" onclick="">Varios</button>&nbsp;INSERT&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Art. Común</button>&nbsp;CTRL+P&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Mayoreo</button>&nbsp;F10&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Entradas</button>&nbsp;F7&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Salidas</button>&nbsp;F8&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
        </section>
        <form id="a" action="LLAMAR "><!--Revisar cookies y utilizar esos ID para generar la tabla-->
            <div id="tablaMain"></div>
        </form>
        <p></p>
        <section id="totales">
        <div class="form-group">
            <label class="backend" id="carrito"> Productos en el carrito</label>&nbsp;&nbsp;&nbsp;&nbsp;Total:
            <label class="backend" id="total"> $0.00</label>&nbsp;&nbsp;Pagó con:
            <label class="backend" id="pago"> $0.00</label>&nbsp;&nbsp;Cambio:
            <label class="backend" id="cambio"> $0.00</label>
        </div>
        </section>
        <section>
            <div class="form-group">
            <button class="backend" onclick="">Cambiar</button>&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Reimprimir Último Ticket</button>
            <button class="backend" onclick="">Ventas y Devoluciones</button>
            </div>       
        </section>
    </div>
    <small>SIV-LF</small>
</body>
 <script src="js/buscar.js"></script> 
</html>
 