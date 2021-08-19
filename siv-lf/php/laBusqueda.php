<?php
//código para realizar la búsqueda de artículos en base a su nombre o a su código de barras mediante la pantalla en "busqueda.php" después de haber seleccionado "Buscar" en el menú principal (main).
//La referencia a este documento se encuentra en busqueda.php en la línea 37

include 'connection.php';
include 'util.php';
$conn = OpenCon();


$number_results = 0;
$response = array();

// Validar que el campo no esté vacío
$busqueda = new FormStringElement(trim($_POST["buscar1"]));
$busqueda->validateEmpty("Introduce el nombre de un producto o su código de barras");

if($busqueda->noErrors()){
    // Prepare a select statement
    $sql = "SELECT id_producto, nombre, precio_unitario, cantidad FROM productos WHERE id_producto like ? or nombre like ? limit 5";// Sí se escribía así para delimitar el resultado a 5 filas?
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // asigna los parámetros
        $param1 = $param2 = trim($_POST["buscar1"]).'%';
        // enlaza variables a la sentencia preparada como parámetros
        mysqli_stmt_bind_param($stmt, "ss", $param1, $param2);

    
        // intenta ejecutar la sentencia preparada
        if(mysqli_stmt_execute($stmt)){
            // guarda el resultado
            mysqli_stmt_store_result($stmt);

        $number_results = mysqli_stmt_num_rows($stmt);

        if($number_results >= 1)
        {                   
            // Bind result variables
            $table = array();
            
            $response['elements'] = $number_results;

            mysqli_stmt_bind_result($stmt, $product_id, $name, $price, $stock);
            while(mysqli_stmt_fetch($stmt)){

                $row = array('codigo_barras' => $product_id,
                'nombre_producto' => $name,
                'precio_unitario' => $price,
                'existencia' => $stock); 
                mysqli_stmt_bind_result($stmt, $product_id, $name, $price, $stock);

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
}
// Close connection
//mysqli_close($conn);

// cierra la conexión
CloseCon($conn);
echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
    
?>
