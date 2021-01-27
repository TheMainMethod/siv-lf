const paginadores = {
    INVENTARIO: 0,
    PRODUCTOS: 1
}

function construirBotonesPag(selector, pagina, elementos, max_filas, max_botones, html_obj)
{
    let clase_estilo = 'class="backend" '
    let botones_paginas = "";
    let paginas = 1;

    let pag_previa = eventoBotonPagina(selector, pagina-1);
    let pag_posterior = eventoBotonPagina(selector, pagina+1);
    
    if(elementos > max_filas) paginas = Math.ceil(elementos/max_filas); 

    let margen = Math.ceil(max_botones/2);
    let paginas_restantes = 1+ paginas - pagina;

    if(pagina === 1) pag_previa = "disabled ";
    if(pagina === paginas) pag_posterior = "disabled ";

    let puntos_suspensivos = '<button ' + clase_estilo + 'disabled>...</button>';
    let primera_pagina = '<button '+ clase_estilo + eventoBotonPagina(selector, 1) + '>1</button>';
    let ultima_pagina = '<button '+ clase_estilo + eventoBotonPagina(selector, paginas) + '>'+paginas+'</button>';

    botones_paginas += '<button ' + clase_estilo + pag_previa + '>&lt;</button>';
    
    if(paginas <= max_botones - 2) botones_paginas += iteraCreacionBotonesPag(selector, 1, paginas, pagina);

    else
    {
        let espacio_botones = max_botones - 4;
        let despl_botones = Math.floor(espacio_botones/2)-1; //botones a los lados del boton de la pagina actual

        if(pagina < margen) botones_paginas += iteraCreacionBotonesPag(selector, 1, max_botones-4, pagina) +
        puntos_suspensivos + ultima_pagina;
        
        else if(paginas_restantes < margen) botones_paginas += primera_pagina +
        puntos_suspensivos + iteraCreacionBotonesPag(selector, paginas-max_botones+5, paginas, pagina);

        else botones_paginas += primera_pagina + puntos_suspensivos +
        iteraCreacionBotonesPag(selector, pagina-despl_botones, pagina+despl_botones, pagina) +
        puntos_suspensivos + ultima_pagina;
    }

    botones_paginas += '<button ' + clase_estilo + pag_posterior + '>&gt;</button>';

    html_obj.html(botones_paginas);

}


function iteraCreacionBotonesPag(selector, inicio, fin, pagina)
{
    let cadena_html = "";
    for(i = inicio; i <= fin; i++)
    {
        let clase_estilo ='class="backend" ';
        let clase_estilo_activo = 'class="strong-button-backend" ';

        let efecto = eventoBotonPagina(selector, i);
        if(pagina === i)
        {
            clase_estilo = clase_estilo_activo;
            efecto = "";
        }
        cadena_html += '<button '+clase_estilo+efecto+'>'+i+'</button>';
    }
    return cadena_html;
}


function eventoBotonPagina(selector, pagina)
{
    switch(selector)
    {
        case paginadores.INVENTARIO:
            return 'onclick="searchInventory('+pagina+')" ';
        case paginadores.PRODUCTOS:
            return 'onclick="searchProducts('+pagina+')" ';
        default:
            return '';
    }
}