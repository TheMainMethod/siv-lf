<?php

//COOKIE

$codigo= $_POST['clave'];
?>
<html>
<head>
</head>
<body>
     <script>
        function setCookie(cname, cvalue, exdays){
            var d= new Date();
            d.setTime(d.getTime()+(exdays*24*60*60*60*1000));
            var expires = "expires="+d.toUTCString();
            document.cookie=cname+"="+cvalue +";"+expires+";path=/"; //colocará en la cookie el valor del código de barras para que después la página principal pueda añadirlo a su 
        }
        setCookie("buscado",<?php echo $codigo?>,0.01); //Si en el último se colocan los días entonces esta cookie no debe de durar tanto tiempo viva.
        location.href = '/siv-lf/main_v1.php';
    </script> 
</body>
</html>
