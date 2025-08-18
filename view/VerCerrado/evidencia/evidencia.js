$(document).ready(function() {
    // Cargar información del ticket
    cargarInfoTicket();
    
    // Cargar evidencia existente
    cargarEvidencia();
    
    // Manejar previsualización de archivos
    $("#archivo").on("change", function() {
        mostrarPrevisualizacion(this);
    });
    
    // Manejar el estilo de arrastrar y soltar
    // No usamos click en el contenedor, sino un botón específico
    $("#file-selector").on("click", function(e) {
        e.preventDefault();
        $("#archivo").click();
    });
    
    // Manejar envío del formulario
    $("#evidencia-form").on("submit", function(e) {
        e.preventDefault();
        subirEvidencia();
    });
});

// Función para cargar la información del ticket
function cargarInfoTicket() {
    $.post('/ESTADIAS/controller/ticket.php?op=ver_ticket', { tick_id: ticket_id }, function(data) {
        try {
            var ticket = JSON.parse(data);
            if (ticket.error) {
                $('#ticket-info').html('<div class="alert alert-danger">No se pudo cargar la información del ticket.</div>');
                return;
            }
            renderTicketInfo(ticket);
        } catch (e) {
            $('#ticket-info').html('<div class="alert alert-danger">Error al procesar la información del ticket.</div>');
        }
    });
}

// Renderizar la información del ticket
function renderTicketInfo(ticket) {
    let estadoHtml = '<span style="background:#dc3545;color:#fff;padding:4px 16px;border-radius:16px;font-size:14px;font-weight:600;">Cerrado</span>';
    let html = `
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid #e3e3e3; padding: 20px 25px 15px 25px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #222;">${ticket.ticket_titulo}</h3>
                ${estadoHtml}
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 12px;">
                <div><strong>Categoría:</strong> ${ticket.cat_nomb}</div>
                <div><strong>Fecha:</strong> <i class="fa fa-calendar"></i> ${ticket.fech_crea}</div>
                <div><strong>Usuario:</strong> <i class="fa fa-user"></i> ${ticket.user_nom} ${ticket.user_ape}</div>
            </div>
        </div>
    `;
    $('#ticket-info').html(html);
}

// Cargar evidencia existente
function cargarEvidencia() {
    $.post('/ESTADIAS/controller/evidencia.php?op=listar', { ticket_id: ticket_id }, function(response) {
        $("#loading-evidencia").hide();
        
        try {
            var data = JSON.parse(response);
            
            if (data.error) {
                $("#evidencia-list").html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i> ${data.error}
                    </div>
                `);
                return;
            }
            
            if (data.length === 0) {
                $("#evidencia-list").html(`
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No hay evidencia subida para este ticket.
                    </div>
                `);
                return;
            }
            
            let html = '';
            data.forEach(function(item) {
                let iconClass = getFileIconClass(item.tipo_evidencia, item.archivo_extension);
                html += `
                    <div class="evidencia-item" id="evidencia-${item.evidencia_id}">
                        <div class="evidencia-icon">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="evidencia-info">
                            <div class="evidencia-title">${item.tipo_evidencia.charAt(0).toUpperCase() + item.tipo_evidencia.slice(1)}: ${item.descripcion}</div>
                            <div class="evidencia-meta">
                                <span><i class="fa fa-clock-o"></i> ${item.fecha_subida}</span>
                                <span class="ml-2"><i class="fa fa-user"></i> ${item.user_nom} ${item.user_ape}</span>
                                <span class="ml-2"><i class="fa fa-file"></i> ${item.archivo_nombre}</span>
                            </div>
                        </div>
                        <div class="evidencia-actions">
                            <a href="/ESTADIAS/public/uploads/evidencia/${item.archivo_ruta}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i> Ver
                            </a>
                            ${rol_id === 2 ? `
                                <button class="btn btn-sm btn-danger btn-delete-evidencia" data-id="${item.evidencia_id}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
            
            $("#evidencia-list").html(html);
            
            // Agregar manejadores de eventos para eliminar
            $(".btn-delete-evidencia").on("click", function() {
                let evidenciaId = $(this).data("id");
                eliminarEvidencia(evidenciaId);
            });
            
        } catch (e) {
            console.error(e);
            $("#evidencia-list").html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle"></i> Error al procesar la respuesta del servidor.
                </div>
            `);
        }
    }).fail(function(xhr, status, error) {
        $("#loading-evidencia").hide();
        $("#evidencia-list").html(`
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> Error al cargar la evidencia: ${error}
            </div>
        `);
    });
}

// Función para obtener la clase de icono según el tipo y extensión
function getFileIconClass(tipo, extension) {
    if (tipo === 'factura') return 'fa fa-file-text-o';
    if (tipo === 'recibo') return 'fa fa-receipt';
    if (tipo === 'documento') return 'fa fa-file-word-o';
    
    // Por extensión
    if (extension === 'pdf') return 'fa fa-file-pdf-o';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(extension.toLowerCase())) return 'fa fa-file-image-o';
    
    return 'fa fa-file-o';
}

// Mostrar previsualización de archivo
function mostrarPrevisualizacion(input) {
    var preview = $("#file-preview");
    preview.empty();
    
    if (input.files && input.files[0]) {
        var file = input.files[0];
        
        // Validar tamaño del archivo (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert("El archivo es demasiado grande. El tamaño máximo permitido es 5MB.");
            input.value = '';
            return;
        }
        
        // Validar tipo de archivo
        var fileType = file.type.toLowerCase();
        var validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        
        if (!validTypes.includes(fileType)) {
            alert("Tipo de archivo no permitido. Por favor, seleccione una imagen (JPG, PNG) o un PDF.");
            input.value = '';
            return;
        }
        
        // Mostrar previsualización
        var reader = new FileReader();
        reader.onload = function(e) {
            var iconClass = 'fa fa-file-o';
            if (fileType.includes('image')) {
                iconClass = 'fa fa-file-image-o';
            } else if (fileType.includes('pdf')) {
                iconClass = 'fa fa-file-pdf-o';
            }
            
            preview.html(`
                <div class="file-preview-item">
                    <i class="${iconClass}"></i>
                    <span>${file.name} (${formatFileSize(file.size)})</span>
                </div>
            `);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Formatear tamaño de archivo
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' bytes';
    else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    else return (bytes / 1048576).toFixed(1) + ' MB';
}

// Subir evidencia
function subirEvidencia() {
    var formData = new FormData(document.getElementById("evidencia-form"));
    formData.append('user_id', user_id);
    
    $("#submit-btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Subiendo...');
    
    $.ajax({
        url: '/ESTADIAS/controller/evidencia.php?op=subir',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            try {
                var data = JSON.parse(response);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'La evidencia se ha subido correctamente.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Resetear formulario
                        $("#evidencia-form")[0].reset();
                        $("#file-preview").empty();
                        
                        // Recargar lista de evidencia
                        cargarEvidencia();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'No se pudo subir la evidencia.'
                    });
                }
            } catch (e) {
                console.error(e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la respuesta del servidor.'
                });
            }
            
            $("#submit-btn").prop('disabled', false).html('Subir Evidencia');
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al comunicarse con el servidor: ' + error
            });
            
            $("#submit-btn").prop('disabled', false).html('Subir Evidencia');
        }
    });
}

// Eliminar evidencia
function eliminarEvidencia(evidenciaId) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('/ESTADIAS/controller/evidencia.php?op=eliminar', { 
                evidencia_id: evidenciaId 
            }, function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        Swal.fire(
                            '¡Eliminado!',
                            'La evidencia ha sido eliminada.',
                            'success'
                        );
                        
                        // Eliminar elemento de la interfaz
                        $("#evidencia-" + evidenciaId).fadeOut(300, function() {
                            $(this).remove();
                            
                            // Si no hay más evidencia, mostrar mensaje
                            if ($("#evidencia-list .evidencia-item").length === 0) {
                                $("#evidencia-list").html(`
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No hay evidencia subida para este ticket.
                                    </div>
                                `);
                            }
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            data.error || 'No se pudo eliminar la evidencia.',
                            'error'
                        );
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire(
                        'Error',
                        'Error al procesar la respuesta del servidor.',
                        'error'
                    );
                }
            }).fail(function(xhr, status, error) {
                Swal.fire(
                    'Error',
                    'Error al comunicarse con el servidor: ' + error,
                    'error'
                );
            });
        }
    });
}
