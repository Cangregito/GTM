function init(){}

$(document).on("click", "#btnsoporte", function () {
    if ($("#rol_id").val() == 1) {
        $("#lbltitulo").html("Acceso Soporte");
        $("#btnsoporte").html("Acceso gerente");
        $("#rol_id").val(2);
    } else {
        $("#lbltitulo").html("Acceso Usuario");
        $("#btnsoporte").html("Acceso gerente");
        $("#rol_id").val(1);
    }
});

init();