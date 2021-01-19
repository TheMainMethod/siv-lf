<?php
include 'util.php';

//conexión a la BD
include 'connection.php';
$conn = OpenCon();
 

$signup_success = 0;
 


// Validar que los campos no estén vacíos
//nombre de usuario
$username = new FormStringElement(trim($_POST["username"]));
$username->validateEmpty("introduce un nombre de usuario");

//contraseña
$password = new FormStringElement(trim($_POST["password"]));
$password->validateEmpty("introduce una contraseña");

$confirm_password = new FormStringElement(trim($_POST["confirm_password"]));
$confirm_password->validateEmpty("vuelve a introducir la contraseña");

if($password->getTempValue() !== $confirm_password->getTempValue())
{
    $confirm_password->setErrorValue("las contraseñas no coinciden");
}
if(strlen($password->getTempValue()) < 8)
{
    $password->setErrorValue("la contraseña debe tener al menos 8 caracteres");
}    
if($password->noErrors() && $confirm_password->noErrors())
{
    $password->setFinalValue($password->getTempValue());
}
  
//nombre completo
$name = new FormStringElement(trim($_POST["name"]));
$name->validateEmpty("introduce tu nombre");

$last_name = new FormStringElement(trim($_POST["last_name"]));
$last_name->validateEmpty("introduce tu apellido paterno");

$mid_name = new FormStringElement(trim($_POST["mid_name"]));
$mid_name->validateEmpty("introduce tu apellido materno");

//autorización
$owner_pass = new FormStringElement(trim($_POST["owner_pass"]));
$owner_pass->validateEmpty("introduce la contraseña del dueño para continuar");

$shift = $_POST["shift"];


if($username->noErrors())
{
    // Prepare a select statement
    $sql = "SELECT n_usuario FROM empleados WHERE n_usuario = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        
        // Set parameters
        $param_username = $username->getFinalValue();
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            /* store result */
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1){
                $username->setErrorValue("nombre de usuario tomado. prueba otro");
            }
            else
            {
                $username->setFinalValue($username->getTempValue());
            }
        }
        else
        {
            echo "oopsie whoopsie";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}

if($owner_pass->noErrors())
{
    // Prepare a select statement
    $sql = "SELECT contra FROM empleados WHERE rol = 'dueño'";
    
    if($stmt = mysqli_prepare($conn, $sql))
    {
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt))
        {
            /* store result */
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) == 1)
            {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $hashed_owner_pass);
                if(mysqli_stmt_fetch($stmt))
                {
                    if(! Password::verify($owner_pass->getFinalValue(), $hashed_owner_pass))
                    {
                        // si la contraseña es incorrecta, muestra un mensaje
                        $owner_pass->setErrorValue("no autorizado para agregar usuarios");
                    }
                }
            }
            else
            {
                echo "oopsie whoopsie, error en la base de datos";
            }
        } 
        else
        {
            echo "oopsie whoopsie, error sql";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}

$nada_vacio = $username->noErrors() &&
            $password->noErrors() &&
            $confirm_password->noErrors() &&
            $name->noErrors() &&
            $last_name->noErrors() &&
            $mid_name->noErrors() &&
            $owner_pass->noErrors();


// Check input errors before inserting in database
if($nada_vacio)
{
    
    // Prepare an insert statement
    $sql = "INSERT INTO empleados(n_usuario, contra, nombre, apellido_pat, apellido_mat, es_turno_matutino)".
    "VALUES (?, ?, ?, ?, ?, ?)";
    
        
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "sssssi", $param_username, $param_password,
        $param_name, $param_last_name, $param_mid_name, $param_shift);
        
        // Set parameters
        $param_username = $username->getFinalValue();
        $param_password = Password::hash($password->getFinalValue());
        $param_name = $name->getFinalValue();
        $param_last_name = $last_name->getFinalValue();
        $param_mid_name = $mid_name->getFinalValue();
        $param_shift = $shift;
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt))
        {
            // Redirect to login page
            $signup_success = 1;
        }
        else
        {
            echo "algo salio mal jaj";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}

// Close connection
mysqli_close($conn);

echo json_encode(array('success' => $signup_success,
'username_err' => $username->getErrorValue(),
'password_err' => $password->getErrorValue(),
'confirm_password_err' => $confirm_password->getErrorValue(),
'name_err' => $name->getErrorValue(),
'last_name_err' => $last_name->getErrorValue(),
'mid_name_err' => $mid_name->getErrorValue(),
'owner_pass_err' => $owner_pass->getErrorValue()
));

?>