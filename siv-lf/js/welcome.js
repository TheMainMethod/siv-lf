function logout()
{
    location.href = 'logout.php';
}


$('#searchform').submit(function(e)
{
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: 'php/search_products.php',
        data: $(this).serialize(),
        success: function(response)
        {
            //console.log(response);
            var jsonData = JSON.parse(response);
            let tabla = "";
            if(jsonData.elements > 0)
            {
                tabla = '<table>'+
                '<tr>'+
                '<th>código de barras</th>'+
                '<th>nombre del producto</th>'+
                '<th>descripción</th>'+
                '<th>precio unitario</th>'+
                '<th>existencia</th>'+
                '</tr>';
                for (row in jsonData.table)
                {
                    tabla += '<tr>'+
                    '<td style="border: 1px solid black">'+jsonData.table[row].codigo_barras+'</td>'+
                    '<td style="border: 1px solid black">'+jsonData.table[row].nombre_producto+'</td>'+
                    '<td style="border: 1px solid black">'+jsonData.table[row].descripcion+'</td>'+
                    '<td style="border: 1px solid black">$'+jsonData.table[row].precio_unitario+'</td>'+
                    '<td style="border: 1px solid black">'+jsonData.table[row].existencia+'</td>'+
                    '</tr>';
                }
                tabla += '</table>'
            }
            
            $("#products-table").html(tabla);
            $("table").addClass("table-backend");
            $("th").addClass("table-data-backend");
            $("td").addClass("table-data-backend");


        }
    });
});