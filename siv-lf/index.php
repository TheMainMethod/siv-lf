<?php
include 'passw.php';
// inicializa sesión
session_start();
 
//si el usuario ya inició sesión, redirígelo a la página de bienvenida
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

//conexión a la BD
include 'connection.php';
$conn = OpenCon();
//echo "Connected Successfully";

// variables a usar para el inicio de sesión
$username = $password = $role = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // checa si el nombre de usuario está vacío
    if(empty(trim($_POST["username"])))
    {
        $username_err = "Ingresar un usuario";
    }
    else
    {
        $username = trim($_POST["username"]);
    }
    
    // checa si la contraseña está vacía
    if(empty(trim($_POST["password"])))
    {
        $password_err = "Ingresar una contraseña";
    }
    else
    {
        $password = trim($_POST["password"]);
    }
    
    // valida los datos de inicio de sesión
    if(empty($username_err) && empty($password_err))
    {
        // prepara la sentencia
        $sql = "SELECT id_empleado, n_usuario, contra, rol FROM empleados WHERE n_usuario = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // enlaza variables a la sentencia preparada como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // asigna los parámetros
            $param_username = $username;
            
            // intenta ejecutar la sentencia preparada
            if(mysqli_stmt_execute($stmt)){
                // guarda el resultado
                mysqli_stmt_store_result($stmt);

                // si el usuario existe, verifica la contraseña
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $user_id, $username, $hashed_password, $role);
                    if(mysqli_stmt_fetch($stmt))
                    {
                        if(Password::verify($password, $hashed_password))
                        {
                            // si la contraseña es correcta, inicia sesión
                            session_start();
                            
                            // datos de la sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $user_id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;                            
                            
                            // redirige a la página de bienvenida
                            
                            echo '<script>alert("a")</script>'; 
                            header("location: welcome.php");
                        } 
                        else
                        {
                            // si la contraseña es incorrecta, muestra un mensaje
                            $password_err = "usuario o contraseña incorrectos";
                        }
                    }
                }
                else
                {
                    // lo mismo si no existe el usuario. no des indicios de cúal fue incorrecto.
                    $password_err = "usuario o contraseña incorrectos";
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
    
}

// cierra la conexión
CloseCon($conn);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inicio de Sesión</title>
    <meta name="author" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/normalize.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet"> 

<body class="obscuro">
    <div class="inicio-de-sesion">

        <img src="img/logo.svg" alt="Logo" class="inicio-de-sesion__logo">

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?> inicio-de-sesion__campo">
                <label class="inicio-de-sesion__campo__texto">Usuario:</label>
                <input type="text" name="username" value="<?php echo $username; ?>" placeholder="Ingresa tu usuario" class="inicio-de-sesion__campo__registro">
                <span><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?> inicio-de-sesion__campo">
                <label class="inicio-de-sesion__campo__texto">Contraseña:</label>
                <input type="password" name="password" placeholder="Ingresa tu contraseña" class="inicio-de-sesion__campo__registro">
                <span><?php echo $password_err; ?></span>
            </div>
            <div class="form-group centrar">
                <input type="submit" value="Iniciar sesión" class="inicio-de-sesion__btn centrar">
            </div>
        </form>
        <a href="signup.php" class="inicio-de-sesion__registrarse">Registrarse</a>
    </div>
    
  <script src="js/script.js"></script>

</body>

</html>