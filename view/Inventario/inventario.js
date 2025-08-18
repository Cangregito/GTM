var tabla;

// Inicialización de la página
$(document).ready(function() {
    // Inicializar DataTable con los datos del inventario
    tabla = $('#inventario_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'
        ],
        "ajax": {
            url: '/ESTADIAS/controller/inventario.php?op=listar',
            type: "get",
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
        "order": [0, "desc"]
    }).DataTable();
    
    // Cargar las estadísticas del inventario
    cargarEstadisticas();
    
    // Inicializar el formulario de inventario
    $('#inventario_form').on('submit', function(e) {
        guardar(e);
    });
    
    // Botón para nuevo elemento
    $('#btnnuevo').click(function() {
        $('#inv_id').val('');
        $('#myModalLabel').html('Nuevo Elemento');
        $('#inventario_form')[0].reset();
        $('#modalInventario').modal('show');
    });
});

// Función para cargar las estadísticas del inventario
function cargarEstadisticas() {
    $.ajax({
        url: '/ESTADIAS/controller/inventario.php?op=stats',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === 'success') {
                let totalItems = 0;
                let totalActivos = 0;
                let totalInactivos = 0;
                let totalDescompuestos = 0;
                
                // Sumar totales de todas las categorías
                data.data.forEach(function(categoria) {
                    totalItems += parseInt(categoria.total_items) || 0;
                    totalActivos += parseInt(categoria.total_activos) || 0;
                    totalInactivos += parseInt(categoria.total_inactivos) || 0;
                    totalDescompuestos += parseInt(categoria.total_descompuestos) || 0;
                });
                
                // Actualizar contadores en la interfaz
                $('#total-items').text(totalItems);
                $('#total-activos').text(totalActivos);
                $('#total-inactivos').text(totalInactivos);
                $('#total-descompuestos').text(totalDescompuestos);
                
                // Si no hay elementos, mostrar mensaje
                if (totalItems === 0) {
                    $('#stats-container').after('<div class="empty-message">No hay elementos en el inventario. ¡Comienza agregando uno!</div>');
                }
            }
        },
        error: function(error) {
            console.error('Error al cargar estadísticas:', error);
        }
    });
}

// Función para guardar o actualizar un elemento
function guardar(e) {
    e.preventDefault();
    
    // Validar campos obligatorios
    if ($('#inv_nombre').val() === '' || $('#inv_cantidad').val() === '' || $('#inv_estado').val() === '') {
        swal({
            title: "Error",
            text: "Por favor completa todos los campos obligatorios",
            type: "error",
            confirmButtonClass: "btn-danger"
        });
        return;
    }
    
    // Deshabilitar botón para evitar doble envío
    $('#btnGuardar').prop('disabled', true);
    var formData = new FormData($('#inventario_form')[0]);
    
    $.ajax({
        url: $('#inv_id').val() ? 
            "/ESTADIAS/controller/inventario.php?op=update" : 
            "/ESTADIAS/controller/inventario.php?op=insert",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            try {
                var response = JSON.parse(datos);
                
                if (response.status === "success") {
                    swal({
                        title: "¡Éxito!",
                        text: response.message,
                        type: "success",
                        confirmButtonClass: "btn-success"
                    });
                    
                    $('#modalInventario').modal('hide');
                    tabla.ajax.reload();
                    cargarEstadisticas();
                    $('.empty-message').remove();
                } else {
                    swal({
                        title: "Error",
                        text: response.message,
                        type: "error",
                        confirmButtonClass: "btn-danger"
                    });
                }
            } catch (e) {
                swal({
                    title: "Error",
                    text: "Error en la respuesta del servidor",
                    type: "error",
                    confirmButtonClass: "btn-danger"
                });
                console.error(e);
            }
            
            $('#btnGuardar').prop('disabled', false);
        },
        error: function(xhr, status, error) {
            swal({
                title: "Error",
                text: "Error de comunicación con el servidor",
                type: "error",
                confirmButtonClass: "btn-danger"
            });
            console.error(xhr.responseText);
            $('#btnGuardar').prop('disabled', false);
        }
    });
}

// Función para editar un elemento
function editar(inv_id) {
    $('#myModalLabel').html('Editar Elemento');
    
    $.ajax({
        url: "/ESTADIAS/controller/inventario.php?op=get_inventario_x_id",
        type: "POST",
        data: { inv_id: inv_id },
        dataType: "json",
        success: function(data) {
            $('#inv_id').val(data.inv_id);
            $('#inv_nombre').val(data.inv_nombre);
            $('#inv_descripcion').val(data.inv_descripcion);
            $('#inv_cantidad').val(data.inv_cantidad);
            $('#inv_estado').val(data.inv_estado);
            $('#inv_categoria').val(data.inv_categoria);
            $('#modalInventario').modal('show');
        },
        error: function(xhr, status, error) {
            swal({
                title: "Error",
                text: "Error al cargar los datos del elemento",
                type: "error",
                confirmButtonClass: "btn-danger"
            });
            console.error(xhr.responseText);
        }
    });
}

// Función para eliminar un elemento
function eliminar(inv_id) {
    swal({
        title: "¿Estás seguro?",
        text: "Una vez eliminado, no podrás recuperar este elemento",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: "/ESTADIAS/controller/inventario.php?op=delete",
                type: "POST",
                data: { inv_id: inv_id },
                success: function(datos) {
                    try {
                        var response = JSON.parse(datos);
                        
                        if (response.status === "success") {
                            swal({
                                title: "¡Eliminado!",
                                text: "El elemento ha sido eliminado correctamente",
                                type: "success",
                                confirmButtonClass: "btn-success"
                            });
                            
                            tabla.ajax.reload();
                            cargarEstadisticas();
                        } else {
                            swal({
                                title: "Error",
                                text: response.message,
                                type: "error",
                                confirmButtonClass: "btn-danger"
                            });
                        }
                    } catch (e) {
                        swal({
                            title: "Error",
                            text: "Error en la respuesta del servidor",
                            type: "error",
                            confirmButtonClass: "btn-danger"
                        });
                        console.error(e);
                    }
                },
                error: function(xhr, status, error) {
                    swal({
                        title: "Error",
                        text: "Error de comunicación con el servidor",
                        type: "error",
                        confirmButtonClass: "btn-danger"
                    });
                    console.error(xhr.responseText);
                }
            });
        }
    });
}
