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
    <title>Ventas</title>

    <script src="js/jquery-3.5.1.js"></script>
    <script src="js/jquery.hotkeys.js"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="backend">
        <header id="logo">
            aqu iría el logo
	   </header>
	<aside id="barraLateral">
        <nav>
            <ul>
                <li><button class="strong-button-backend">Ventas</button></li>
                <li><button class="backend" onclick="">Productos</button></li>
                <li><button class="backend" onclick="">Pedidos</button></li>
                <li><button class="backend" onclick="">Inventario</button></li>
                <li><button class="backend" onclick="">Corte</button></li>
            </ul>
        </nav>

        <div><button class="backend" onclick="">Ajustes</button></div>
        <time></time>
        <p></p>
    </aside>

    <p>bienvenido, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</p>
    <p>
        <button class="backend" onclick="logout()">cerrar sesión</button>
    </p>

        <section id="barra de busqueda"  style="width:100%">
             <div class="form-group">
            <input type="text" name="nombre producto" class="backend">
            <label class="backend" id="nombre_err"></label>
            <input type="submit" class="backend" value="Agregar producto">
            <input type="submit" class="backend" value="Buscar">
            </div>
        </section>
         <section id="botones" style="width:100%">
             <div class="form-group">
            <button class="backend" onclick="">Art. Común</button>&nbsp;CTRL+P&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Mayoreo</button>&nbsp;F10&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Entradas</button>&nbsp;F7&nbsp;&nbsp;&nbsp;&nbsp;
            <button class="backend" onclick="">Salidas</button>&nbsp;F8&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
        </section>
                <div id="table"></div>
        <table style="width:100%">
          <tr>
            <th>Código de Barras</th>
            <th>Descripción del Producto</th> 
            <th>Precio Venta</th>
            <th>Cantidad</th>
            <th>Importe</th>
            <th>Existencia</th>
          </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </table>
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
    <script src="js/sales.js"></script> 
</body>