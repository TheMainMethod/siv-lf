$(document).ready(function()
{

    $('#signupform').submit(function(e)
    {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: 'php/signup_procedure.php',
            data: $(this).serialize(),
            success: function(response)
            {
                //console.log(response);
                var jsonData = JSON.parse(response);
  
                //si pudo iniciar sesión, redirígelo a la página de inicio
                if (jsonData.success == 1)
                {
                    alert("Usuario agregado con éxito!");
                    location.href = 'login.php';
                }
                else
                {
                    $('#username_err').html(jsonData.username_err);
                    $('#password_err').html(jsonData.password_err);
                    $('#confirm_password_err').html(jsonData.confirm_password_err);
                    $('#name_err').html(jsonData.name_err);
                    $('#last_name_err').html(jsonData.last_name_err);
                    $('#mid_name_err').html(jsonData.mid_name_err);
                    $('#owner_pass_err').html(jsonData.owner_pass_err);
                }
           }
       });
     });
});