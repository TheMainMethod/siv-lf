<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">+
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <title>SIV-LF</title>
</head>

<body>
    <div class="container">
        <div class="bg">
            <div class="box inicio-sesion">
                <h2>¿Te encuentras registrado?</h2>
                <button class="inicio-sesion-btn">Inicio de Sesión</button>
            </div>

            <div class="box registro">
                <h2>¿No tienes cuenta?</h2>
                <button class="registro-btn">Registro</button>
            </div>
        </div>

        <div class="formBox">
            <div class="form inicio-sesion-form">
                <form method="post">
                    <h3>Inicio de Sesión</h3>
                    <input type="text" placeholder="Usuario">
                    <input type="password" placeholder="Contraseña">
                    <input type="submit" value="Entrar">
                </form>
            </div>

            <div class="form registro-form">
                <form method="post">
                    <h3>Registro</h3>
                    <input type="text" placeholder="Nombre">
                    <input type="text" placeholder="Apellido">
                    <input type="text" placeholder="Teléfono">
                    <input type="text" placeholder="Usuario">
                    <input type="password" placeholder="Contraseña">
                    <input type="password" placeholder="Confirmar contraseña">
                    <input type="submit" value="Registrar">
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="js/login.js"></script>
</body>

</html>