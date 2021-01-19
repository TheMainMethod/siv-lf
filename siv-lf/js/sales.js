$(document).ready(function()
{
    $(document).bind('keydown', "insert", function assets() {
        alert("varios");
        return false;
    });


    $(document).bind('keydown', "Ctrl+p", function assets() {
        alert("artículos comunes");
        return false;
    });

    $(document).bind('keydown', "f10", function assets() {
        alert("mayoreo");
        return false;
    });

    $(document).bind('keydown', "f7", function assets() {
        alert("entradas");
        return false;
    });

    $(document).bind('keydown', "f8", function assets() {
        alert("salidas");
        return false;
    });
});