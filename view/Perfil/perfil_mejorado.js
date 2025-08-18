$(document).ready(function() {
    // Configurar validaciones
    $('#btnUpdatePass').on('click', function() {
        var pass = $('#user_pass').val();
        var confirmPass = $('#confirm_pass').val();
        var user_id = $('#user_id').val();
        
        // Verificar si se ingresó contraseña
        if (pass.trim() === '') {
            swal({
                title: "Advertencia",
                text: "Por favor ingrese una nueva contraseña",
                type: "warning",
                confirmButtonClass: "btn-warning"
            });
            return;
        }
        
        // Verificar que las contraseñas coincidan
        if (pass !== confirmPass) {
            swal({
                title: "Error",
                text: "Las contraseñas no coinciden",
                type: "error",
                confirmButtonClass: "btn-danger"
            });
            return;
        }
        
        // Verificar longitud mínima
        if (pass.length < 6) {
            swal({
                title: "Advertencia",
                text: "La contraseña debe tener al menos 6 caracteres",
                type: "warning",
                confirmButtonClass: "btn-warning"
            });
            return;
        }
        
        // Si todo está bien, enviar la solicitud
        $.ajax({
            url: "/ESTADIAS/controller/usuario.php?op=update_password",
            type: "POST",
            data: {
                user_id: user_id,
                user_pass: pass
            },
            success: function(response) {
                try {
                    var jsonResponse = JSON.parse(response);
                    
                    if (jsonResponse.status === "success") {
                        swal({
                            title: "¡Éxito!",
                            text: "Contraseña actualizada correctamente",
                            type: "success",
                            confirmButtonClass: "btn-success"
                        });
                        
                        // Limpiar campos
                        $('#user_pass').val('');
                        $('#confirm_pass').val('');
                    } else {
                        swal({
                            title: "Error",
                            text: jsonResponse.message || "Hubo un problema al actualizar la contraseña",
                            type: "error",
                            confirmButtonClass: "btn-danger"
                        });
                    }
                } catch (e) {
                    swal({
                        title: "Error",
                        text: "Error inesperado en la respuesta del servidor",
                        type: "error",
                        confirmButtonClass: "btn-danger"
                    });
                    console.error("Error parsing JSON response:", e);
                }
            },
            error: function(xhr, status, error) {
                swal({
                    title: "Error",
                    text: "Error de comunicación con el servidor",
                    type: "error",
                    confirmButtonClass: "btn-danger"
                });
                console.error("AJAX error:", error);
            }
        });
    });
    
    // Añadir animación para mejorar la experiencia de usuario
    $(".profile-card").addClass("animated fadeIn");
    $(".user-info-card").addClass("animated fadeIn");
    
    // Efectos hover para las estadísticas
    $(".stat-box").hover(
        function() {
            $(this).css("transform", "translateY(-5px)");
            $(this).find("h4").css("color", "#2d6284");
        }, 
        function() {
            $(this).css("transform", "translateY(0)");
            $(this).find("h4").css("color", "#3c8dbc");
        }
    );
});
