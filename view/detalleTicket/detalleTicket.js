function init() {}

function renderTicketInfo(ticket) {
    let estadoHtml = '';
    if (ticket.tik_estado === "Abierto") {
        estadoHtml = '<span style="background:#28a745;color:#fff;padding:4px 16px;border-radius:16px;font-size:14px;font-weight:600;">Abierto</span>';
    } else {
        estadoHtml = '<span style="background:#dc3545;color:#fff;padding:4px 16px;border-radius:16px;font-size:14px;font-weight:600;">Cerrado</span>';
    }
    let html = `
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid #e3e3e3; padding: 28px 32px 20px 32px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
                <h3 style="margin: 0; font-size: 1.7rem; font-weight: 700; color: #222;">${ticket.ticket_titulo}</h3>
                ${estadoHtml}
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 30px; margin-bottom: 12px;">
                <div><strong>Categoría:</strong> ${ticket.cat_nomb}</div>
                <div><strong>Fecha:</strong> <i class="fa fa-calendar"></i> ${ticket.fech_crea}</div>
                <div><strong>Usuario:</strong> <i class="fa fa-user"></i> ${ticket.user_nom} ${ticket.user_ape}</div>
            </div>
            <div style="background: #f8f9fa; border-radius: 8px; padding: 18px 20px; border-left: 4px solid #007bff;">
                <strong style="font-size: 1.1rem;">Descripción:</strong>
                <div style="margin:8px 0 0 0; font-size: 1.05rem; color: #333;">${ticket.ticket_descripcion}</div>
            </div>
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
                detalles.forEach(function(row) {
                    let rolNombre = '';
                    let rolClass = '';
                    if (row.rol_id == 1) {
                        rolNombre = 'Gerente';
                        rolClass = 'background: #28a745; color: #fff;';
                    } else if (row.rol_id == 2) {
                        rolNombre = 'Soporte';
                        rolClass = 'background: #17a2b8; color: #fff;';
                    } else {
                        rolNombre = 'Otro';
                        rolClass = 'background: #6c757d; color: #fff;';
                    }
                    html += `
                    <article class="activity-line-item box-typical" style="margin-bottom: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); border: 1px solid #e3e3e3;">
                        <div class="activity-line-date" style="font-size: 13px; color: #888; margin-bottom: 8px;">
                            <i class="fa fa-calendar"></i> ${row.fech_crea}
                        </div>
                        <header class="activity-line-item-header" style="display: flex; align-items: center; margin-bottom: 10px;">
                            <div class="activity-line-item-user-photo" style="margin-right: 12px;">
                                <a href="#">
                                    <img src="/ESTADIAS/public/img/user.jpg" alt="" style="width:48px; height:48px; border-radius:50%; border:2px solid #f1f1f1;">
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