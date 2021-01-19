<?php

include 'connection.php';
$conn = OpenCon();



$number_results = 0;
$response = array();

$sql = "SELECT id_producto, nombre, descripcion, precio_unitario, cantidad FROM productos WHERE nombre like ? or descripcion like ?";
    
if($stmt = mysqli_prepare($conn, $sql))
{

    // asigna los parámetros
    $param1 = $param2 = '%'.trim($_POST["query"]).'%';

    // enlaza variables a la sentencia preparada como parámetros
    mysqli_stmt_bind_param($stmt, "ss", $param1, $param2);

    
    // intenta ejecutar la sentencia preparada
    if(mysqli_stmt_execute($stmt))
    {
        // guarda el resultado
        mysqli_stmt_store_result($stmt);

        $number_results = mysqli_stmt_num_rows($stmt);

        if($number_results >= 1)
        {                   
            // Bind result variables
            $table = array();
            
            $response['elements'] = $number_results;

            mysqli_stmt_bind_result($stmt, $product_id, $name, $desc, $price, $stock);
            while(mysqli_stmt_fetch($stmt))
            {

                $row = array('codigo_barras' => $product_id,
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
        }
    } 
    else
    {
        echo "oopsie whoopsie";
    }

    // finaliza la sentencia
    mysqli_stmt_close($stmt);
}

// cierra la conexión
CloseCon($conn);

echo json_encode($response, JSON_PRETTY_PRINT);

?>