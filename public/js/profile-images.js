/**
 * Funciones para manejar las imágenes de perfil en toda la aplicación
 */

// Función para obtener la URL de la imagen de perfil según el rol
function getProfileImageByRole(roleId) {
    if (roleId == 1) {
        return "/ESTADIAS/public/img/Gerente.png";
    } else {
        return "/ESTADIAS/public/img/Soporte.png";
    }
}

// Función para obtener el texto alternativo según el rol
function getProfileAltByRole(roleId) {
    if (roleId == 1) {
        return "Gerente de Tienda";
    } else {
        return "Soporte";
    }
}

// Función para actualizar todas las imágenes de perfil en la página
function updateAllProfileImages(roleId) {
    var imgSrc = getProfileImageByRole(roleId);
    var imgAlt = getProfileAltByRole(roleId);
    
    // Actualiza todas las imágenes de perfil con la clase profile-image
    $('.profile-image').each(function() {
        $(this).attr('src', imgSrc + '?' + new Date().getTime());
        $(this).attr('alt', imgAlt);
    });
}

// Ejecutar cuando el documento esté listo
$(document).ready(function() {
    // Si hay un cambio de rol en la sesión, actualizar las imágenes
    $(document).on('roleChanged', function(e, roleId) {
        updateAllProfileImages(roleId);
    });
});
