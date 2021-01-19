$(document).ready(function()
{

    $('#loginform').submit(function(e)
    {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: 'php/login_procedure.php',
            data: $(this).serialize(),
            success: function(response)
            {
                var jsonData = JSON.parse(response);
  
                //si pudo iniciar sesión, redirígelo a la página de inicio
                if (jsonData.success == 1)
                {
                    location.href = 'welcome.php';
                }
                else
                {
                    $('#username_err').html(jsonData.username_err);
                    $('#password_err').html(jsonData.password_err);
                }
           }
       });
     });
});

function toSignup()
{
    location.href = 'signup.php';
}