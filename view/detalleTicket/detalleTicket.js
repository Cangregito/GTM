function init() {}

// Función para formatear fechas en formato más legible
function formatFecha(fechaStr) {
    try {
        const fecha = new Date(fechaStr);
        return fecha.toLocaleString('es-ES', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit', 
            minute: '2-digit'
        });
    } catch(e) {
        return fechaStr; // Si hay un error, devuelve el string original
    }
}

function renderTicketInfo(ticket) {
    let estadoHtml = '';
    let evidenciaBtn = '';
    
    if (ticket.tik_estado === "Abierto") {
        estadoHtml = '<span style="background:#28a745;color:#fff;padding:4px 16px;border-radius:16px;font-size:14px;font-weight:600;">Abierto</span>';
    } else {
        estadoHtml = '<span style="background:#dc3545;color:#fff;padding:4px 16px;border-radius:16px;font-size:14px;font-weight:600;">Cerrado</span>';
        
        // Botón para ir a la evidencia si el ticket está cerrado
        evidenciaBtn = `
            <div class="mt-3" style="margin-top:15px;">
                <a href="/ESTADIAS/view/VerCerrado/evidencia/index.php?ID=${ticket.ticket_id}" class="btn btn-info">
                    <i class="fa fa-upload"></i> Subir/Ver Evidencias
                </a>
            </div>`;
        
        // Si el ticket está cerrado, actualizamos la interfaz
        setTimeout(function() {
            updateUIForClosedTicket();
        }, 300);
    }
    let html = `
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid #e3e3e3; padding: 28px 32px 20px 32px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
                <h3 style="margin: 0; font-size: 1.7rem; font-weight: 700; color: #222;">${ticket.ticket_titulo}</h3>
                ${estadoHtml}
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 30px; margin-bottom: 12px;">
                <div><strong>Categoría:</strong> ${ticket.cat_nomb}</div>
                <div><strong>Fecha:</strong> <i class="fa fa-calendar"></i> ${formatFecha(ticket.fech_crea)}</div>
                <div style="display: flex; align-items: center;">
                    <strong>Usuario:</strong> 
                    <span style="margin-left: 5px; display: flex; align-items: center;">
                        <img src="/ESTADIAS/public/img/${parseInt(ticket.rol_id) === 1 ? 'Gerente.png' : 'Soporte.png'}?v=${new Date().getTime()}" 
                            alt="${parseInt(ticket.rol_id) === 1 ? 'Gerente de Tienda' : 'Soporte'}" 
                            class="profile-image" 
                            style="width: 24px; height: 24px; object-fit: contain; margin-right: 5px; background-color:#f9f9f9;"
                            onerror="this.onerror=null; this.src='/ESTADIAS/public/img/user.jpg';">
                        ${ticket.user_nom} ${ticket.user_ape}
                    </span>
                </div>
            </div>
            <div style="background: #f8f9fa; border-radius: 8px; padding: 18px 20px; border-left: 4px solid #007bff;">
                <strong style="font-size: 1.1rem;">Descripción:</strong>
                <div style="margin:8px 0 0 0; font-size: 1.05rem; color: #333;">${ticket.ticket_descripcion}</div>
            </div>
            ${evidenciaBtn}
        </div>
    `;
    $('#ticket-info').html(html);
}

$(document).ready(function() {
    var id = getUrlParameter('ID');
    if (id) {
        // 1. Carga la información del ticket
        $.post('/ESTADIAS/controller/ticket.php?op=ver_ticket', { tick_id: id }, function(data) {
            try {
                var ticket = JSON.parse(data);
                if (ticket.error) {
                    $('#ticket-info').html('<div class="alert alert-danger">No se pudo cargar la información del ticket.</div>');
                    return;
                }
                renderTicketInfo(ticket);
                
                // Guardamos el estado del ticket como variable global
                window.ticketEstado = ticket.tik_estado;
                
            } catch (e) {
                $('#ticket-info').html('<div class="alert alert-danger">Error al procesar la información del ticket.</div>');
            }
        });

        // 2. Carga la conversación (como ya lo tienes)
        $.post('/ESTADIAS/controller/ticket.php?op=listardetalle', { tick_id: id }, function(data) {
            try {
                var detalles = JSON.parse(data);
                if (detalles.error) {
                    console.error('Error SQL:', detalles.error);
                    alert('Error SQL: ' + detalles.error);
                    return;
                }
                if (!Array.isArray(detalles)) {
                    console.error('Respuesta inesperada:', detalles);
                    return;
                }
                var html = '';
                // Depuración: imprime los datos para verificar
                console.log('Datos de la conversación:', detalles);
                
                detalles.forEach(function(row) {
                    let rolNombre = '';
                    let rolClass = '';
                    // Asegurarse de que rol_id sea un número
                    let rolId = parseInt(row.rol_id);
                    console.log('Usuario:', row.user_nom, 'Rol ID:', rolId);
                    
                    if (rolId === 1) {
                        rolNombre = 'Gerente';
                        rolClass = 'background: #28a745; color: #fff;';
                    } else if (rolId === 2) {
                        rolNombre = 'Soporte';
                        rolClass = 'background: #17a2b8; color: #fff;';
                    } else {
                        rolNombre = 'Otro';
                        rolClass = 'background: #6c757d; color: #fff;';
                    }
                    html += `
                    <article class="activity-line-item box-typical" style="margin-bottom: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); border: 1px solid #e3e3e3;">
                        <div class="activity-line-date" style="font-size: 13px; color: #888; margin-bottom: 8px;">
                            <i class="fa fa-calendar"></i> ${formatFecha(row.fech_crea)}
                        </div>
                        <header class="activity-line-item-header" style="display: flex; align-items: center; margin-bottom: 10px;">
                            <div class="activity-line-item-user-photo" style="margin-right: 12px;">
                                <a href="#">
                                    <img 
                                        src="/ESTADIAS/public/img/${rolId === 1 ? 'Gerente.png' : 'Soporte.png'}?v=${new Date().getTime()}" 
                                        alt="${rolId === 1 ? 'Gerente de Tienda' : 'Soporte'}" 
                                        class="profile-image" 
                                        style="width:48px; height:48px; border-radius:50%; border:2px solid #f1f1f1; object-fit:contain; background-color:#f9f9f9;"
                                        onerror="this.onerror=null; this.src='/ESTADIAS/public/img/user.jpg';"
                                    >
                                </a>
                            </div>
                            <div>
                                <div class="activity-line-item-user-name" style="font-weight: bold; font-size: 16px;">
                                    ${row.user_nom} ${row.user_ape}
                                    <span style="display:inline-block; margin-left:10px; padding:2px 10px; border-radius:12px; font-size:12px; ${rolClass}">
                                        ${rolNombre}
                                    </span>
                                </div>
                            </div>
                        </header>
                        <div class="activity-line-action-list">
                            <section class="activity-line-action">
                                <div class="cont">
                                    <div class="cont-in" style="background: #f8f9fa; border-radius: 6px; padding: 14px 18px; font-size: 15px;">
                                        ${row.tickd_descrip}
                                    </div>
                                </div>
                            </section>
                        </div>
                    </article>
                    `;
                });
                $('#detalle-lista').html(html);
            } catch (e) {
                console.error('No es JSON válido:', data);
            }
        });
    }

    // Inicializa Summernote al cargar la página
    $('.summernote').summernote({
        height: 120,
        placeholder: 'Escribe tu respuesta aquí...',
        callbacks: {
            onImageUpload: function(files) {
                for (let i = 0; i < files.length; i++) {
                    uploadImage(files[i], this);
                }
            }
        }
    });

    function uploadImage(file, editor) {
        var data = new FormData();
        data.append("file", file);
        $.ajax({
            url: '/ESTADIAS/controller/upload_summernote.php', // Debes crear este endpoint
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            type: "POST",
            success: function(url) {
                // Inserta la imagen en el editor
                $(editor).summernote('insertImage', url);
            },
            error: function() {
                alert('No se pudo subir la imagen');
            }
        });
    }
});

$(document).on('submit', '#form-respuesta', function(e) {
    e.preventDefault();
    var id = getUrlParameter('ID');
    var respuesta = $('#respuesta').val().trim();
    if (!respuesta) {
        alert('Por favor, escribe una respuesta.');
        return;
    }
    $.post('/ESTADIAS/controller/ticket.php?op=insertar_respuesta', {
        tick_id: id,
        user_id: user_id,
        respuesta: respuesta
    }, function(data) {
        try {
            var res = JSON.parse(data);
            if (res.success) {
                $('#respuesta').val('');
                // Recargar la conversación
                $('html, body').animate({ scrollTop: $('#detalle-lista').offset().top }, 400);
                // Vuelve a cargar los mensajes
                $.post('/ESTADIAS/controller/ticket.php?op=listardetalle', { tick_id: id }, function(data) {
                    // ...tu render de mensajes aquí...
                    // (puedes extraer el render a una función para reutilizar)
                    location.reload(); // O recarga solo la conversación si prefieres
                });
            } else {
                alert('No se pudo enviar la respuesta.');
            }
        } catch (e) {
            alert('Error al procesar la respuesta.');
        }
    });
});

// Función para actualizar la interfaz cuando un ticket está cerrado
function updateUIForClosedTicket() {
    // Ocultar elementos de respuesta
    $('#responder').hide();
    $('#scroll-to-reply').hide();
    
    // Mostrar mensaje de ticket cerrado si no existe ya
    if ($('.ticket-closed-message').length === 0) {
        $('<div class="ticket-closed-message" style="background:#f8d7da; color:#721c24; padding:15px 20px; border-radius:6px; margin-top:30px; text-align:center; border:1px solid #f5c6cb;">' +
          '<i class="fa fa-lock" style="margin-right:8px;"></i>' +
          'Este ticket está cerrado. No se pueden añadir más respuestas.' +
          '</div>').insertAfter('#detalle-lista');
    }
}

// Manejar el botón para cerrar ticket
$(document).on('click', '#cerrar-ticket', function() {
    // Crear un diálogo personalizado en lugar de usar confirm()
    var overlayHTML = `
    <div id="custom-confirm-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 9999; display: flex; justify-content: center; align-items: center;">
        <div style="background-color: white; border-radius: 5px; padding: 25px; width: 90%; max-width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: bounceIn 0.5s;">
            <h4 style="margin-top: 0; margin-bottom: 15px; color: #d43f3a; font-weight: bold; font-size: 18px;">Confirmar cierre de ticket</h4>
            <hr style="margin: 15px 0; border-color: #eee;">
            <p style="margin: 15px 0; color: #555; font-size: 15px;">¿Estás seguro que deseas cerrar este ticket? Esta acción no se puede deshacer.</p>
            <div style="text-align: right; margin-top: 20px;">
                <button id="cancel-close" class="btn btn-default" style="margin-right: 15px; padding: 6px 15px;">Cancelar</button>
                <button id="confirm-close" class="btn btn-danger" style="padding: 6px 15px;">Sí, cerrar ticket</button>
            </div>
        </div>
    </div>
    `;
    
    // Añadir estilos de animación
    var styleHTML = `
    <style>
        @keyframes bounceIn {
            0% { transform: scale(0.8); opacity: 0; }
            70% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
    `;
    
    // Agregar el diálogo al cuerpo del documento
    $('body').append(styleHTML + overlayHTML);
    
    // Manejar el botón cancelar
    $('#cancel-close').click(function() {
        $('#custom-confirm-overlay').fadeOut(200, function() {
            $(this).remove();
        });
    });
    
    // Manejar el botón confirmar
    $('#confirm-close').click(function() {
        var id = getUrlParameter('ID');
        
        // Mostrar estado de cargando
        $(this).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
        $(this).prop('disabled', true);
        
        $.post('/ESTADIAS/controller/ticket.php?op=cerrar_ticket', {
            tick_id: id
        }, function(data) {
            try {
                var res = JSON.parse(data);
                if (res.success) {
                    // Cambiar el contenido del diálogo para mostrar éxito
                    $('#custom-confirm-overlay > div').html(`
                        <div style="text-align: center; padding: 10px;">
                            <i class="fa fa-check-circle" style="color: #5cb85c; font-size: 56px; margin-bottom: 15px; display: block;"></i>
                            <h4 style="margin: 15px 0; color: #5cb85c; font-size: 20px;">¡Ticket cerrado exitosamente!</h4>
                            <p style="color: #555; margin-bottom: 20px;">El ticket ha sido cerrado correctamente y ya aparecerá en la sección de tickets cerrados.</p>
                            <button id="close-success" class="btn btn-success" style="padding: 8px 20px; font-weight: 500;">Continuar</button>
                        </div>
                    `);
                    
                    $('#close-success').click(function() {
                        // Actualizar la interfaz sin recargar la página completa
                        $('#custom-confirm-overlay').fadeOut(200, function() {
                            $(this).remove();
                            // Actualizamos el estado del ticket
                            window.ticketEstado = "Cerrado";
                            // Actualizamos la etiqueta de estado en la info del ticket
                            $('#ticket-info .tbl-cell span').html('<span style="background:#dc3545;color:#fff;padding:4px 16px;border-radius:16px;font-size:14px;font-weight:600;">Cerrado</span>');
                            // Actualizamos la interfaz para un ticket cerrado
                            updateUIForClosedTicket();
                        });
                    });
                } else {
                    // Mostrar error
                    $('#custom-confirm-overlay > div').html(`
                        <div style="text-align: center; padding: 10px;">
                            <i class="fa fa-times-circle" style="color: #d9534f; font-size: 56px; margin-bottom: 15px; display: block;"></i>
                            <h4 style="margin: 15px 0; color: #d9534f; font-size: 20px;">Error</h4>
                            <p style="color: #555; margin-bottom: 20px;">No se pudo cerrar el ticket. Por favor, inténtelo de nuevo.</p>
                            <button id="close-error" class="btn btn-danger" style="padding: 8px 20px; font-weight: 500;">Cerrar</button>
                        </div>
                    `);
                    
                    $('#close-error').click(function() {
                        $('#custom-confirm-overlay').fadeOut(200, function() {
                            $(this).remove();
                        });
                    });
                }
            } catch (e) {
                // Mostrar error de procesamiento
                $('#custom-confirm-overlay > div').html(`
                    <div style="text-align: center; padding: 10px;">
                        <i class="fa fa-times-circle" style="color: #d9534f; font-size: 56px; margin-bottom: 15px; display: block;"></i>
                        <h4 style="margin: 15px 0; color: #d9534f; font-size: 20px;">Error</h4>
                        <p style="color: #555; margin-bottom: 20px;">Error al procesar la respuesta. Por favor, inténtelo de nuevo.</p>
                        <button id="close-error" class="btn btn-danger" style="padding: 8px 20px; font-weight: 500;">Cerrar</button>
                    </div>
                `);
                
                $('#close-error').click(function() {
                    $('#custom-confirm-overlay').fadeOut(200, function() {
                        $(this).remove();
                    });
                });
            }
        });
    });
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
    return false;
};

init();