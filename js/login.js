
    $(".registro-btn").click(() => {
        $(".formBox").addClass("active");
        $("body").addClass("active");
    });

    $(".inicio-sesion-btn").click(() => {
        $(".formBox").removeClass("active");
        $("body").removeClass("active");
    });