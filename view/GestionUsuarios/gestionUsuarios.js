var tabla;
var usuario_id;
var operacion = 'crear'; // Puede ser 'crear' o 'editar'

function init() {
    // Inicializa la tabla cuando se carga la página
    cargarTabla();
    
    // Manejador del formulario principal
    $("#usuario_form").on("submit", function(e) {
        e.preventDefault();
        guardarUsuario();
    });
    
    // Manejador del botón de guardar
    $("#btnGuardar").click(function() {
        $("#usuario_form").submit();
    });
    
    // Manejador del botón de cambiar contraseña
    $("#btnCambiarPass").click(function() {
        cambiarPassword();
    });
    
    // Configuración para nuevo usuario
    $("#btnNuevo").click(function() {
        operacion = 'crear';
        limpiarFormulario();
        $("#modalUsuarioLabel").html("Nuevo Usuario");
        $("#div_password").show();
        $("#user_pass").attr("required", true);
    });
}

function cargarTabla() {
    tabla = $('#usuario_data').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: '/ESTADIAS/controller/usuario.php?op=listar',
            type: "POST",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}

function editar(user_id) {
    operacion = 'editar';
    limpiarFormulario();
    
    // Cambiar el título del modal
    $("#modalUsuarioLabel").html("Editar Usuario");
    
    // Ocultar campo de contraseña para edición
    $("#div_password").hide();
    $("#user_pass").attr("required", false);
    
    // Obtener datos del usuario para editar
    $.post("/ESTADIAS/controller/usuario.php?op=get_usuario_x_id", {user_id: user_id}, function(data) {
        if (!data.error) {
            $("#user_id").val(data.user_id);
            $("#user_nom").val(data.user_nom);
            $("#user_ape").val(data.user_ape);
            $("#user_correo").val(data.user_correo);
            $("#rol_id").val(data.rol_id);
            $("#modalUsuario").modal("show");
        } else {
            swal("Error", "No se pudo cargar la información del usuario", "error");
        }
    }, "json");
}

function eliminar(user_id) {
    swal({
        title: "¿Estás seguro?",
        text: "Una vez eliminado, no podrás recuperar este usuario",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.post("/ESTADIAS/controller/usuario.php?op=delete", {user_id: user_id}, function(data) {
                if (data.status == "success") {
                    swal("Eliminado", "El usuario ha sido eliminado correctamente", "success");
                    tabla.ajax.reload();
                } else {
                    swal("Error", "No se pudo eliminar el usuario", "error");
                }
            }, "json");
        }
    });
}

function cambiarPasswordModal(user_id) {
    $("#user_id_pass").val(user_id);
    $("#new_password").val("");
    $("#confirm_password").val("");
    $("#modalPassword").modal("show");
}

function cambiarPassword() {
    var new_password = $("#new_password").val();
    var confirm_password = $("#confirm_password").val();
    var user_id = $("#user_id_pass").val();
    
    if (new_password === "") {
        swal("Error", "Debe ingresar una nueva contraseña", "error");
        return;
    }
    
    if (new_password !== confirm_password) {
        swal("Error", "Las contraseñas no coinciden", "error");
        return;
    }
    
    $.post("/ESTADIAS/controller/usuario.php?op=update_password", {
        user_id: user_id,
        new_password: new_password
    }, function(data) {
        if (data.status == "success") {
            swal("Éxito", "Contraseña actualizada correctamente", "success");
            $("#modalPassword").modal("hide");
        } else {
            swal("Error", data.message || "Error al actualizar la contraseña", "error");
        }
    }, "json");
}

function guardarUsuario() {
    // Validación básica
    if ($("#user_nom").val() === "" || $("#user_ape").val() === "" || 
        $("#user_correo").val() === "" || $("#rol_id").val() === "") {
        swal("Advertencia", "Todos los campos son obligatorios", "warning");
        return;
    }
    
    if (operacion === 'crear' && $("#user_pass").val() === "") {
        swal("Advertencia", "La contraseña es obligatoria para nuevos usuarios", "warning");
        return;
    }
    
    var url = operacion === 'crear' ? 
        "/ESTADIAS/controller/usuario.php?op=insert" : 
        "/ESTADIAS/controller/usuario.php?op=update";
    
    $.ajax({
        url: url,
        type: "POST",
        data: $('#usuario_form').serialize(),
        dataType: "json",
        success: function(data) {
            if (data.status == "success") {
                swal("Éxito", operacion === 'crear' ? "Usuario creado correctamente" : "Usuario actualizado correctamente", "success");
                $('#modalUsuario').modal('hide');
                tabla.ajax.reload();
            } else {
                swal("Error", data.message || "Error al procesar la solicitud", "error");
            }
        },
        error: function(xhr, status, error) {
            swal("Error", "Error en el servidor", "error");
            console.log(error);
        }
    });
}

function limpiarFormulario() {
    $('#usuario_form')[0].reset();
    $("#user_id").val("");
}

init();
