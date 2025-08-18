function init(){
    // Preprocesar las imágenes para evitar parpadeos
    $('<img/>').attr('src', '/ESTADIAS/public/img/Soporte.png').on('load', function() {
        $(this).remove(); // Elimina el elemento después de cargar la imagen
    });
    $('<img/>').attr('src', '/ESTADIAS/public/img/Gerente.png').on('load', function() {
        $(this).remove(); // Elimina el elemento después de cargar la imagen
    });
}

$(document).on("click", "#btnsoporte", function () {
    const imgElement = $(".sign-box .sign-avatar img");
    
    if ($("#rol_id").val() == 1) {
        $("#lbltitulo").html("Acceso Soporte");
        $("#btnsoporte").html("Acceso Gerente");
        $("#rol_id").val(2);
        
        // Aplicar una transición de opacidad suave
        imgElement.fadeOut(200, function() {
            $(this).attr("src", "/ESTADIAS/public/img/Soporte.png?" + new Date().getTime());
            $(this).attr("alt", "Soporte");
        }).fadeIn(200);
    } else {
        $("#lbltitulo").html("Acceso Gerente");
        $("#btnsoporte").html("Acceso Soporte");
        $("#rol_id").val(1);
        
        // Aplicar una transición de opacidad suave
        imgElement.fadeOut(200, function() {
            $(this).attr("src", "/ESTADIAS/public/img/Gerente.png?" + new Date().getTime());
            $(this).attr("alt", "Gerente de Tienda");
        }).fadeIn(200);
    }
});

// Asegurar que jQuery está completamente cargado antes de ejecutar
$(document).ready(function() {
    init();
});