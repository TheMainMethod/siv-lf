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
    <title>Inventario</title>

    <script src="js/jquery-3.5.1.js"></script>
    <script src="js/jquery.hotkeys.js"></script>
    <script src="js/JsBarcode.all.js"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="backend">
        <header id="logo">
            aqu iría el logo
	   </header>
        <aside id="barraLateral">
            <nav>
                <ul style="display:inline">
                    <li><button class="backend" onclick="toSales()">Ventas</button></li>
                    <li><button class="backend" onclick="">Productos</button></li>
                    <li><button class="backend" onclick="">Pedidos</button></li>
                    <li><button class="strong-button-backend">Inventario</button></li>
                    <li><button class="backend" onclick="">Corte</button></li>
                    <?php
                    if($_SESSION["role"] == 'dueño')
                    {
                        echo '<li><button class="backend" onclick="">Empleados</button></li>';
                    }
                    ?>
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

        <div style="width:100%">

            <form id="inv-query-form">
                <div class="form-group">
                        <label class="blackend">término de búsqueda</label>
                        <input type="text" id="inv-query" class="backend">
                </div> 
                <div class="form-group">
                        <label class="blackend">elementos por página</label>
                        <input type="number" id="inv-row-max" class="backend" value="<?php echo $_SESSION["inv_max_rows"]; ?>">
                        <span id="inv-row-max-err"></span>
                </div> 
                <div class="form-group" hidden> <!-- iría mejor en la página de configuración -->
                    
                    <label class="backend">Número de páginas</label>
                    <select id="inv-page-max" class="campo" >
                        <?php 

                        $seleccionado = $_SESSION["inv_max_pages"];
                        
                        for($i = 9; $i <= 21; $i += 2)
                        {
                            $actual = "";
                            if($i == $seleccionado) $actual = 'selected="selected"';
                            echo '<option value='.$i.' '.$actual.'>'.$i.'</option>';
                        }

                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <input type="submit" class="backend" value="Buscar">
                </div>
            </form>
        </div>

        <p>Tabla con el inventario</p>
        <div id="inv-table-div"></div>



        <section>
            <div id="inv-page-buttons"></div>
            <div id="inv-table-buttons" class="form-group" hidden>
                
                <div>
                    <button id="add-product-line-button" class="backend" onclick="">Agregar nuevo producto</button>
                    <button id="peek-individual-button" class="backend" onclick="" disabled>Ver ejemplares</button> <!-- inhabilitar si no hay indice seleccionado -->
                    <button id="delete-product-line-button" class="backend" onclick="" disabled>Eliminar seleccionado</button>
                </div>
            </div>       
        </section>


        <div id="modal-nuevo-producto" class="modal">
            <div class="modal-content">
                <button class="backend" id='cerrar-modal-nuevo-producto'>cerrar</button>
                <form id="inv-add-product-form">
                    <div class="form-group">
                            <label class="blackend">Nombre</label>
                            <input type="text" id="add-product-name" disabled class="backend">
                    </div> 
                    <div class="form-group">
                            <label class="blackend">Descripción</label>
                            <input type="text" id="add-product-desc" disabled class="backend">
                    </div> 
                    <div class="form-group">
                            <label class="blackend">Precio unitario</label>
                            <input type="number" id="add-product-price" disabled class="backend">
                            <span ></span>
                    </div> 
                    <div class="form-group">
                            <label class="blackend">Cantidad inicial</label>
                            <input type="number" id="add-product-stock" disabled class="backend">
                            <span ></span>
                    </div> 

                    <div class="form-group">
                        <input type="submit" id="add-product" class="backend" disabled value="Agregar">
                    </div>
                </form>
            </div>
        </div>

        


        <div id="modal-productos" class="modal">
            <div class="modal-content">
                <button class="backend" id='cerrar-modal-productos'>cerrar</button>
                <p>Tabla con los codigos de barra de un producto</p> <!-- tabla con el nombre, y codigo de barras y una checkbox -->
                <form id="product-form">
                    <div class="form-group">
                            <label class="blackend">elementos por página</label>
                            <input type="number" id="products-row-max" class="backend" disabled value="<?php echo $_SESSION["products_max_rows"]; ?>">
                            <span id="products-row-max-err"></span>
                    </div> 
                    <div class="form-group">
                        <input type="submit" id="products-refresh" class="backend" disabled value="Refrescar">
                    </div>
                </form>
            <div id="products-table-div"></div>
            

                <section>
                    <div id="products-page-buttons"></div>
                    <div class="form-group">
                        <button class="backend" onclick="">Aumentar existencias</button>
                        <button class="backend" onclick="" disabled>Imprimir seleccionados</button>
                        <button class="backend" onclick="" disabled>Eliminar seleccionados</button>
                    </div>       
                </section>
            </div>

        </div>


    </div>
    <small>SIV-LF</small>
    <script src="js/paginator.js"></script>
    <script src="js/inventory.js"></script>
    <script src="js/navigation.js"></script>
</body>