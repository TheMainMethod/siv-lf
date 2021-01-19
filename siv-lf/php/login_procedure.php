<?php
include 'util.php';

//conexión a la BD
include 'connection.php';
$conn = OpenCon();

// variables a usar para el inicio de sesión
$role = "";

$login_success = 0;

//valida nombre de usuario y contraseña
//nombre de usuario
$username = new FormStringElement(trim($_POST["username"]));
$username->validateEmpty("por favor proporciona un nombre de usuario");

//contraseña
$password = new FormStringElement(trim($_POST["password"]));
$password->validateEmpty("por favor proporciona una contraseña");
 

// valida los datos de inicio de sesión
if($username->noErrors() && $password->noErrors())
{
    // prepara la sentencia
    $sql = "SELECT id_empleado, n_usuario, contra, rol FROM empleados WHERE n_usuario = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // enlaza variables a la sentencia preparada como parámetros
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        
        // asigna los parámetros
        $param_username = $username->getFinalValue();
        
        // intenta ejecutar la sentencia preparada
        if(mysqli_stmt_execute($stmt))
        {
            // guarda el resultado
            mysqli_stmt_store_result($stmt);

            // si el usuario existe, verifica la contraseña
            if(mysqli_stmt_num_rows($stmt) == 1){                    
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $user_id, $session_username, $hashed_password, $role);
                if(mysqli_stmt_fetch($stmt))
                {
                    if(Password::verify($password->getFinalValue(), $hashed_password))
                    {
                        // si la contraseña es correcta, inicia sesión
                        session_start();
                        
                        // datos de la sesión
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $user_id;
                        $_SESSION["username"] = $session_username;
                        $_SESSION["role"] = $role;                            
                        
                        // redirige a la página de bienvenida
                        $login_success = 1;
                        //header("location: welcome.php");
                    } 
                    else
                    {
                        // si la contraseña es incorrecta, muestra un mensaje
                        $password->setErrorValue("usuario o contraseña incorrectos");
                    }
                }
            }
            else
            {
                // lo mismo si no existe el usuario. no des indicios de cúal fue incorrecto.
                $password->setErrorValue("usuario o contraseña incorrectos");
            }
        } 
        else
        {
            echo "oopsie whoopise!";
        }

        // finaliza la sentencia
        mysqli_stmt_close($stmt);
    }
}

// cierra la conexión
CloseCon($conn);

echo json_encode(array('success' => $login_success,
'username_err' => $username->getErrorValue(),
'password_err' => $password->getErrorValue()));

?>