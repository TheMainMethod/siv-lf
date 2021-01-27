$(document).ready(function()
{
    //var modal = document.getElementById("myModal");
    //busca TODOS los registros al cargar la página
    searchInventory(1);
});

$('#inv-query-form').submit(function(e)
{
    e.preventDefault();
    searchInventory(1);
});

$('#product-form').submit(function(e)
{
    e.preventDefault();
    searchProducts(1);
});

function searchInventory(empieza_pag)
{
    //desactiva los botones si estaban activados, remueve el estilo que marca la selección
    let elements = [$("#peek-individual-button"),
    $("#delete-product-line-button")];
    disableInputs(elements);

    $("#inv-table tr.inv-table-row").removeClass('table-row-selected');

    let filas = parseInt($("#inv-row-max").val());
    let consulta = $("#inv-query").val();
    
    empieza_pag = parseInt(empieza_pag);

    $.post("php/search_inventory.php",
    {
        query: consulta,
        limit: filas,
        page: empieza_pag
    },
    function(response)
    {
        //console.log(response);
        let jsonData = JSON.parse(response);
        
        let max_botones = parseInt(jsonData.inv_pag_max);

        if(parseInt(jsonData.elements) > 0)
        {
            let tabla = '<table id="inv-table">'+
            '<tr id="inv-table-header">'+
            '<th>id de producto</th>'+
            '<th>nombre del producto</th>'+
            '<th>descripción</th>'+
            '<th>precio unitario</th>'+
            '<th>existencia</th>'+
            '</tr>';
            for (row in jsonData.table)
            {
                let estilo_extra = 'style="border: 1px solid black"';
                tabla += '<tr class="inv-table-row">'+
                '<td '+estilo_extra+'>'+jsonData.table[row].id_producto+'</td>'+
                '<td '+estilo_extra+'>'+jsonData.table[row].nombre_producto+'</td>'+
                '<td '+estilo_extra+'>'+jsonData.table[row].descripcion+'</td>'+
                '<td '+estilo_extra+'>$'+jsonData.table[row].precio_unitario+'</td>'+
                '<td '+estilo_extra+'>'+jsonData.table[row].existencia+'</td>'+
                '</tr>';
            }
            tabla += '</table>'

            $("#inv-table-div").html(tabla);
            $("#inv-table-div table").addClass("table-backend");
            $("#inv-table th").addClass("table-data-backend");
            $("#inv-table td").addClass("table-data-backend");

            $("#inv-table-buttons").prop("hidden", false);
            
            if(jsonData.inv_row_max_err !== '') filas = parseInt(jsonData.inv_row_max_default);
            construirBotonesPag(paginadores.INVENTARIO, empieza_pag,
                parseInt(jsonData.elements), filas, max_botones, $("#inv-page-buttons"));

        }
        else
        {
            let aviso_ninguno = '<p>¡Aquí no hay nada!</p>'
            $("#inv-table-div").html(aviso_ninguno);
            $("#inv-table-buttons").prop("hidden", true);
        }
        $('#inv-row-max-err').html(jsonData.inv_row_max_err);
    });
}

function searchProducts(empieza_pag)
{
    let filas = parseInt($("#products-row-max").val());
    //let max_botones = parseInt($("#inv-page-max").val());
    
    empieza_pag = parseInt(empieza_pag);

    let id_producto = parseInt($("#inv-table tr.table-row-selected td:first").html());

    $.post("php/search_products.php",
    {
        product_id: id_producto,
        limit: filas,
        page: empieza_pag
    },
    function(response)
    {
        //console.log(response);
        let jsonData = JSON.parse(response);

        let max_botones = parseInt(jsonData.products_pag_max);
        
        if(parseInt(jsonData.elements) > 0)
        {
            let tabla = '<table id="products-table">'+
            '<tr id="products-table-header">'+
            '<th hidden>codigo</th>'+
            '<th>código de barras</th>'+
            '<th>fecha de adición</th>'+
            '</tr>';
            for (row in jsonData.table)
            {
                let estilo_extra = 'style="border: 1px solid black"';

                let codigo_num = jsonData.table[row].codigo_barras;
                //let codigo = 'LF-'+(codigo_num).toString().padStart(10, "0");

                let codigo_etiqueta = '<svg id="barcode'+codigo_num+'"></svg>';

                tabla += '<tr class="products-table-row">'+
                '<td class="product_id" hidden>'+codigo_num+'</td>'+
                '<td '+estilo_extra+'>'+codigo_etiqueta+'</td>'+
                '<td '+estilo_extra+'>'+jsonData.table[row].fecha_adicion+'</td>'+
                '</tr>';
            }

            tabla += '</table>'

            $("#products-table-div").html(tabla);
            $("#products-table-div table").addClass("table-backend");
            $("#products-table th").addClass("table-data-backend");
            $("#products-table td").addClass("table-data-backend");

            $("#products-table .product_id").each(function(){
                let codigo_num = $(this).html();
                $("#barcode"+codigo_num).JsBarcode('LF-'+(codigo_num).toString().padStart(10, "0"));
            });
            
            if(jsonData.products_row_max_err !== '') filas = parseInt(jsonData.products_row_max_default);
            construirBotonesPag(paginadores.PRODUCTOS, empieza_pag,
                parseInt(jsonData.elements), filas, max_botones, $("#products-page-buttons"));

        }
        else
        {
            //esto pasa cuado se busca con un id de producto inexistente.
            let aviso_ninguno = '<p>¡Aquí no hay nada!</p>'
            $("#products-table-div").html(aviso_ninguno);
            //$("#inv-table-buttons").prop("hidden", true);
        }
        $('#products-row-max-err').html(jsonData.products_row_max_err);
    });
}


$("#inv-table-div").on("click", "#inv-table tr.inv-table-row", function()
{
    $(this).addClass('table-row-selected').siblings().removeClass('table-row-selected'); 
    
    let elements = [$("#peek-individual-button"),
    $("#delete-product-line-button")];
    enableInputs(elements);
});

$("#add-product-line-button").click(function()
{
    let elements = [$("#add-product-name"),
    $("#add-product-desc"),
    $("#add-product-price"),
    $("#add-product-stock"),
    $("#add-product")];

    enableInputs(elements);
    abrirModal( $("#modal-nuevo-producto") );
});

$("#peek-individual-button").click(function()
{
    //alert( "código de producto: " +$("#inv-table tr.table-row-selected td:first").html());
    searchProducts(1);

    let elements = [$("#products-row-max"),
    $("#products-refresh")];
    enableInputs(elements);

    abrirModal( $("#modal-productos") );
});

$("#delete-product-line-button").click(function()
{
    alert( "¿Estás seguro? Esta operación NO se puede deshacer");
});

$("#cerrar-modal-nuevo-producto").click(function()
{
    cerrarModal( $("#modal-nuevo-producto") );

    let elements = [$("#add-product-name"),
    $("#add-product-desc"),
    $("#add-product-price"),
    $("#add-product-stock"),
    $("#add-product")];
    disableInputs(elements);
});

$("#cerrar-modal-productos").click(function()
{
    cerrarModal( $("#modal-productos") );

    $("#products-table-div").html("");

    let elements = [$("#products-row-max"),
    $("#products-refresh")];
    disableInputs(elements);
});


function enableInputs(elements)
{
    elements.forEach(function(input)
    {
        input.prop("disabled", false);
    });
}

function disableInputs(elements)
{
    elements.forEach(function(input)
    {
        input.prop("disabled", true);
    });
}

// modal ///////////////////


function abrirModal(elemento)
{
    elemento.css("display", "block");
}

function cerrarModal(elemento)
{
    elemento.css("display", "none");
}