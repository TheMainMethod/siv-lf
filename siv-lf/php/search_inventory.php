<?php
include 'connection.php';
$conn = OpenCon();

session_start();
//
$filas_por_pagina = $_POST["limit"];
$filas_por_pagina_err = "";
$filas_default = $_SESSION["inv_max_rows"];
if($filas_por_pagina == 0) 
{
    $filas_por_pagina_err = "Introduce un parámetro válido"; 
    $filas_por_pagina = $filas_default;
}
$pagina = $_POST["page"] -1;
$number_results = 0;

$number_pages = $_SESSION["inv_max_pages"];
//
$response = array();

//dandole valor a los parametros
$param1 = $param2 = '%'.trim($_POST["query"]).'%';

//realiza la búsqueda completa
$unlimited = "SELECT id_producto FROM productos WHERE nombre like ? or descripcion like ?";

if($stmt = mysqli_prepare($conn, $unlimited))
{
    // enlaza variables a la sentencia preparada como parámetros
    mysqli_stmt_bind_param($stmt, "ss", $param1, $param2);

    
    // intenta ejecutar la sentencia preparada
    if(mysqli_stmt_execute($stmt))
    {
        // guarda el resultado
        mysqli_stmt_store_result($stmt);

        $number_results = mysqli_stmt_num_rows($stmt);
    } 
    else
    {
        die ('oopsie whoopsie 1.<br>'.mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
}

$sql = "SELECT id_producto, nombre, descripcion, precio_unitario,".
"cantidad FROM productos WHERE nombre like ? or descripcion like ? limit ? offset ?";
    
if($stmt = mysqli_prepare($conn, $sql))
{

    $offset = $filas_por_pagina * $pagina;

    // enlaza variables a la sentencia preparada como parámetros
    mysqli_stmt_bind_param($stmt, "ssii", $param1, $param2, $filas_por_pagina, $offset);

    
    // intenta ejecutar la sentencia preparada
    if(mysqli_stmt_execute($stmt))
    {
        // guarda el resultado
        mysqli_stmt_store_result($stmt);

        if($number_results >= 1)
        {                   
            
            $table = array();
            
            $response['elements'] = $number_results;
            $response['inv_pag_max'] = $number_pages;
            $response['inv_row_max_default'] = $filas_default;

            // Bind result variables
            mysqli_stmt_bind_result($stmt, $product_id, $name, $desc, $price, $stock);
            while(mysqli_stmt_fetch($stmt))
            {

                $row = array('id_producto' => $product_id,
                'nombre_producto' => $name,
                'descripcion' => $desc,
                'precio_unitario' => $price,
                'existencia' => $stock); 
                mysqli_stmt_bind_result($stmt, $product_id, $name, $desc, $price, $stock);

                $table[] = $row;
            }
            $response['table'] = $table;
            
            
        }
        else
        {
            $response['elements'] = $number_results;
            $response['inv_pag_max'] = $number_pages;
            $response['inv_row_max_default'] = $filas_default;
        }
    } 
    else
    {
        die ('oopsie whoopsie 2.<br>'.mysqli_error($conn));
    }

    // finaliza la sentencia
    mysqli_stmt_close($stmt);
}

// cierra la conexión
CloseCon($conn);

$response['inv_row_max_err'] = $filas_por_pagina_err;

//si se desea ver bonito
//echo json_encode($response, JSON_PRETTY_PRINT);
echo json_encode($response);

?>