$('#agregarProducto').submit(function(e)
{
    
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: 'php/consultaTablaMain.php',
        data: $(this).serialize(),
        success: function(response)
        {
            //console.log(response);
            var jsonData = JSON.parse(response);
            let tabla = "";
            if(jsonData.elements > 0)
            {
                tabla = '<table style="border: 1px solid black;border-collapse:collapse">'+
                '<tr>'+
                '<th style="border: 1px solid black">Código del Producto</th>'+
                '<th style="border: 1px solid black">Nombre del Producto</th>'+
                '<th style="border: 1px solid black">Precio Unitario</th>'+
                '<th style="border: 1px solid black">Cantidad</th>'+
                '<th style="border: 1px solid black">Total</th>'+
                '</tr>';
                for (row in jsonData.table)
                {
                    tabla += '<tr>'+
                    '<td style="border: 1px solid black">'+jsonData.table[row].CODIGO_DEL+PRODUCTO+'</td>'+ //Revisar como se llama esto
                    '<td style="border: 1px solid black">'+jsonData.table[row].nombre_producto+'</td>'+
                    '<td style="border: 1px solid black">'+jsonData.table[row].precio_unitario+'</td>'+
                        
                    '<td style="border: 1px solid black">'+.+'</td>'+
                    '<td style="border: 1px solid black">'+.+'</td>'+
                    '</tr>';
                }
                tabla += '</table>'
            }
            
            $("#tablaMain").html(tabla);


        }
    });
});