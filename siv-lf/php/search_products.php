<?php
//REWORKEAR
include 'connection.php';
$conn = OpenCon();

session_start();
$filas_por_pagina = $_POST["limit"];
$filas_por_pagina_err = "";
$filas_default = $_SESSION["products_max_rows"];
if($filas_por_pagina == 0) 
{
    $filas_por_pagina_err = "Introduce un parámetro válido";
    $filas_por_pagina = $filas_default;
}
$pagina = $_POST["page"] -1;
$number_results = 0;

$number_pages = $_SESSION["products_max_pages"];
//
$response = array();

// dandole valor a los los parámetros
$product_id = trim($_POST["product_id"]);
//realiza la búsqueda completa
$unlimited = "SELECT codigo_de_barras FROM ejemplares WHERE productos_id_producto = ?";

if($stmt = mysqli_prepare($conn, $unlimited))
{
    // enlaza variables a la sentencia preparada como parámetros
    mysqli_stmt_bind_param($stmt, "i", $product_id);
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

$sql = "SELECT codigo_de_barras, fecha_adicion FROM ejemplares WHERE productos_id_producto = ? ".
'limit ? offset ?';
    
if($stmt = mysqli_prepare($conn, $sql))
{
    $offset = $filas_por_pagina * $pagina;

    // enlaza variables a la sentencia preparada como parámetros
    mysqli_stmt_bind_param($stmt, "iii", $product_id, $filas_por_pagina, $offset);

    
    // intenta ejecutar la sentencia preparada
    if(mysqli_stmt_execute($stmt))
    {
        // guarda el resultado
        mysqli_stmt_store_result($stmt);

        $table = array();
            
        $response['elements'] = $number_results;
        $response['products_pag_max'] = $number_pages;
        $response['products_row_max_default'] = $filas_default;

        mysqli_stmt_bind_result($stmt, $barcode, $date_of_addition);
        while(mysqli_stmt_fetch($stmt))
        {
            //echo $barcode.', '.$date_of_addition;

            $row = array('codigo_barras' => $barcode,
            'fecha_adicion' => $date_of_addition); 
            mysqli_stmt_bind_result($stmt, $barcode, $date_of_addition);

            $table[] = $row;
        }
        $response['table'] = $table;
    } 
    else
    {
        die ('oopsie whoopsie. 2.<br>'.mysqli_error($conn));
    }

    // finaliza la sentencia
    mysqli_stmt_close($stmt);
}

$response['products_row_max_err'] = $filas_por_pagina_err;

// cierra la conexión
CloseCon($conn);

echo json_encode($response);

?>