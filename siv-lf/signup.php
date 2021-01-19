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
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

$name = $mid_name = $last_name = $owner_pass = "";
$name_err = $mid_name_err = $last_name_err = $owner_pass_err = "";

 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validar que los campos no estén vacíos
    if(empty(trim($_POST["username"])))
    {
        $username_err = "introduce un nombre de usuario";
    }
    else
    {
        $username = trim($_POST["username"]);
    }

    // contraseña

    if(empty(trim($_POST["password"])))
    {
        $password_err = "introduce una contraseña";
    }
    else if(empty(trim($_POST["confirm_password"])))
    {
        $confirm_password_err = "vuelve a introducir la contraseña";
    } 
    else
    {
        if(strlen(trim($_POST["password"])) < 8)
        {
            $password_err = "la contraseña debe tener al menos 8 caracteres";
        }
        else if(trim($_POST["password"]) != trim($_POST["confirm_password"]))
        {
            $confirm_password_err = "las contraseñas no coinciden";
        }
        else
        {
            $password = $_POST["password"];
            $confirm_password = $_POST["confirm_password"];
        }
    }

    //nombre completo
    if(empty(trim($_POST["name"])))
    {
        $name_err = "introduce tu nombre";
    }
    else
    {
        $name = trim($_POST["name"]);
    }

    if(empty(trim($_POST["last_name"])))
    {
        $last_name_err = "introduce tu apellido paterno";
    }
    else
    {
        $last_name = trim($_POST["last_name"]);
    }

    if(empty(trim($_POST["mid_name"])))
    {
        $mid_name_err = "introduce tu apellido materno";
    }
    else
    {
        $mid_name = trim($_POST["mid_name"]);
    }

    if(empty(trim($_POST["owner_pass"])))
    {
        $owner_pass_err = "introduce la contraseña del dueño para continuar";
    }
    else
    {
        $owner_pass = trim($_POST["owner_pass"]);
    }
    $shift = $_POST["shift"];

    if(empty($username_err))
    {
        // Prepare a select statement
        $sql = "SELECT n_usuario FROM empleados WHERE n_usuario = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "nombre de usuario tomado. prueba otro";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "oopsie whoopsie";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    if(empty($owner_pass_err))
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
                        if(! Password::verify($owner_pass, $hashed_owner_pass))
                        {
                            // si la contraseña es incorrecta, muestra un mensaje
                            $owner_pass_err = "no autorizado para agregar usuarios";
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
    
    $nada_vacio = empty($username_err) &&
                empty($password_err) &&
                empty($confirm_password_err) &&
                empty($name_err) &&
                empty($last_name_err) &&
                empty($mid_name_err) &&
                empty($owner_pass_err);

    
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
            $param_username = $username;
            $param_password = Password::hash($password);
            $param_name = $name;
            $param_last_name = $last_name;
            $param_mid_name = $mid_name;
            $param_shift = $shift;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt))
            {
                // Redirect to login page
                header("location: index.php");
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
}
?>
 
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <p class="backend">nuevo empleado</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label class="backend">nombre usuario</label>
                <input type="text" name="username" class="backend" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label class="backend">contraseña</label>
                <input type="password" name="password" class="backend" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label class="backend">repite contraseña</label>
                <input type="password" name="confirm_password" class="backend" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label class="backend">nombre</label>
                <input type="text" name="name" class="backend" value="<?php echo $name; ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                <label class="backend">apellido paterno</label>
                <input type="text" name="last_name" class="backend" value="<?php echo $last_name; ?>">
                <span class="help-block"><?php echo $last_name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($mid_name_err)) ? 'has-error' : ''; ?>">
                <label class="backend">apellido materno</label>
                <input type="text" name="mid_name" class="backend" value="<?php echo $mid_name; ?>">
                <span class="help-block"><?php echo $mid_name_err; ?></span>
            </div>
            <div class="form-group">
                <label class="backend">turno</label>
                <select name="shift" class="backend">
                    <option value=1>Matutino</option>
                    <option value=0>Vespertino</option>
                </select>
            </div> 
            <div class="form-group <?php echo (!empty($owner_pass_err)) ? 'has-error' : ''; ?>">
                <label class="backend">autorización</label>
                <input type="password" name="owner_pass" class="backend" value="<?php echo $owner_pass; ?>">
                <span class="help-block"><?php echo $owner_pass_err; ?></span>
            </div>
            <div>
                <input type="submit" class="backend" value="agregar">

            </div>
            <p class="backend">ya tienes cuenta? <a href="index.php">inicia sesión</a></p>
        </form>
        
    </div>    
</body>
</html>