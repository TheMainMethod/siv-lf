<?php
// inicializa la sesión
session_start();
 
//Página que se genera al seleccionar "Buscar" en la página principal.
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Búsqueda de artículos</title>

    <script src="js/jquery-3.5.1.js"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="backend">
     <form id="searchform2" method="post">
          <div class="form-group">
              <label class="backend">Nombre o código del artículo</label>
              <input type="text" name="buscar1" id="buscar1" class="backend"/>
              <input type="submit" name="searchBTN" id="searchBTN" class="backend" value="buscar"/>
        </div>
    </form>
        <form action="/siv-lf/php/ResultadoBusqueda.php" method="post">
            <div id="tabla2"></div>
        </form>
       <p>
           <button class="backend" onclick="">Registrar un producto</button>
           <button class="backend" onclick="back2Main()">Cancelar</button>
           </p> 
    </div>
    <script type="text/javascript">
$(document).ready(function(){
    $('#searchform2').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: 'php/laBusqueda.php',
            data: $(this).serialize(),
            success: function(response){  
            console.log(response);
            var jsonData = JSON.parse(response);
   
                let tabla = "";
                if(jsonData.elements > 0){
                   // let i=0;
                        tabla = '<table style="border: 1px solid black;border-collapse:collapse">'+
                        '<tr>'+
                        '<th style="border: 1px solid black">Código de barras</th>'+
                        '<th style="border: 1px solid black">Nombre del producto</th>'+
                        '<th style="border: 1px solid black">Precio Unitario</th>'+
                        '<th style="border: 1px solid black">Existencia</th>'+
                        '<th style="border: 1px solid black">¿Agregar?</th>'+
                        '</tr>';
                    for (row in jsonData.table){
                        //i=i+1;
                        let codigo=jsonData.table[row].codigo_barras;
                        let nombreP=jsonData.table[row].nombre_producto;
                        let price=jsonData.table[row].precio_unitario;
                        let Existencia=jsonData.table[row].existencia;
                        //let clave= "C"+i.toString;
                        tabla += '<tr>'+
                        '<td style="border: 1px solid black">'+codigo+'</td>'+
                        '<td style="border: 1px solid black">'+nombreP+'</td>'+
                        '<td style="border: 1px solid black">'+price+'</td>'+
                        '<td style="border: 1px solid black">'+Existencia+'</td>'+
                        '<td><input type="submit" name="clave" value="'+codigo+'"/>Agregar</td>'
                        '</tr>';
                    }//Todos los botones se llamarán clave para que no haya problema al momento de generar las cookies, espero que no haya problema, ya que solamente se le podrá hacer clic a uno.
                tabla += '</table>'
                }  
            $("#tabla2").html(tabla);
            }
        });
    });
});
function back2Main()
{
    location.href = '/siv-lf/main_v1.php';//tal vez hacer que abra una ventana nueva.
}

    </script>
</body>
</html>